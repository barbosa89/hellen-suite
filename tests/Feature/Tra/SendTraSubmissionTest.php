<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use App\Jobs\SendTraSubmission;
use App\Models\TraSubmission;
use App\Services\Tra\TraClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendTraSubmissionTest extends TestCase
{
    use InteractsWithTra;
    use RefreshDatabase;

    #[Test]
    public function it_marks_a_principal_as_sent_and_records_the_attempt(): void
    {
        ['stay' => $stay, 'occupancy' => $occupancy, 'principal' => $principal] = $this->traStay();

        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/one/*' => Http::response(['reference' => 'TRA-1'], 200)]);

        $submission = TraSubmission::factory()->create([
            'hotel_id' => $stay->hotel_id,
            'stay_id' => $stay->id,
            'room_occupancy_id' => $occupancy->id,
            'stay_guest_id' => $principal->id,
            'kind' => TraSubmissionKind::Principal,
            'status' => TraSubmissionStatus::Pending,
            'payload_snapshot' => ['rnt' => '12345', 'token' => 'secret'],
        ]);

        (new SendTraSubmission($submission->id))->handle(app(TraClient::class));

        $submission->refresh();

        $this->assertSame(TraSubmissionStatus::Sent, $submission->status);
        $this->assertSame('TRA-1', $submission->external_reference);
        $this->assertNotNull($submission->sent_at);

        $attempt = $submission->attempts()->sole();
        $this->assertSame('api', $attempt->channel);
        $this->assertSame(200, $attempt->response_status);
        $this->assertSame('TRA-1', $attempt->response_reference);
        $this->assertSame(hash('sha256', json_encode(['rnt' => '12345']) ?? ''), $attempt->request_hash);
    }

    #[Test]
    public function it_rejects_without_retries_on_client_errors(): void
    {
        ['stay' => $stay, 'occupancy' => $occupancy, 'principal' => $principal] = $this->traStay();

        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/one/*' => Http::response('RNT inválido', 422)]);

        $submission = TraSubmission::factory()->create([
            'hotel_id' => $stay->hotel_id,
            'stay_id' => $stay->id,
            'room_occupancy_id' => $occupancy->id,
            'stay_guest_id' => $principal->id,
            'kind' => TraSubmissionKind::Principal,
            'status' => TraSubmissionStatus::Pending,
            'payload_snapshot' => ['rnt' => 'bad'],
        ]);

        (new SendTraSubmission($submission->id))->handle(app(TraClient::class));

        $this->assertSame(TraSubmissionStatus::Rejected, $submission->refresh()->status);
        $this->assertSame('http_422', $submission->refresh()->last_error_code);
    }

    #[Test]
    public function it_blocks_a_companion_until_its_principal_is_sent_and_then_unblocks_it(): void
    {
        ['stay' => $stay, 'occupancy' => $occupancy, 'principal' => $principal, 'companion' => $companion] = $this->traStay();

        Http::preventStrayRequests();
        Http::fake([
            'pms.mincit.gov.co/one/*' => Http::response(null, 200),
            'pms.mincit.gov.co/two/*' => Http::response(null, 200),
        ]);

        $principalSubmission = TraSubmission::factory()->create([
            'hotel_id' => $stay->hotel_id,
            'stay_id' => $stay->id,
            'room_occupancy_id' => $occupancy->id,
            'stay_guest_id' => $principal->id,
            'kind' => TraSubmissionKind::Principal,
            'status' => TraSubmissionStatus::Pending,
            'payload_snapshot' => ['rnt' => '12345'],
        ]);

        $companionSubmission = TraSubmission::factory()->create([
            'hotel_id' => $stay->hotel_id,
            'stay_id' => $stay->id,
            'room_occupancy_id' => $occupancy->id,
            'stay_guest_id' => $companion->id,
            'kind' => TraSubmissionKind::Companion,
            'status' => TraSubmissionStatus::Pending,
            'payload_snapshot' => ['documento_principal' => '1020304050'],
        ]);

        Queue::fake();

        (new SendTraSubmission($companionSubmission->id))->handle(app(TraClient::class));

        $this->assertSame(TraSubmissionStatus::Blocked, $companionSubmission->refresh()->status);
        Http::assertNothingSent();

        (new SendTraSubmission($principalSubmission->id))->handle(app(TraClient::class));

        $this->assertSame(TraSubmissionStatus::Sent, $principalSubmission->refresh()->status);
        $this->assertSame(TraSubmissionStatus::Pending, $companionSubmission->refresh()->status);
        Queue::assertPushed(SendTraSubmission::class, fn (SendTraSubmission $job): bool => $job->submissionId === $companionSubmission->id);
    }
}
