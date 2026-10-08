<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use App\Models\TraSubmission;
use App\Services\Tra\TraApiException;
use App\Services\Tra\TraClient;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use function in_array;

class SendTraSubmission implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900];

    public int $timeout = 60;

    public function __construct(public int $submissionId) {}

    public function uniqueId(): string
    {
        return "tra-submission-{$this->submissionId}";
    }

    public function handle(TraClient $client): void
    {
        $submission = TraSubmission::query()
            ->with(['stay', 'roomOccupancy', 'stayGuest.guest.identificationType'])
            ->find($this->submissionId);

        if ($submission === null || ! in_array($submission->status, [TraSubmissionStatus::Pending, TraSubmissionStatus::Queued], true)) {
            return;
        }

        if ($submission->kind === TraSubmissionKind::Companion && ! $this->principalSent($submission)) {
            $submission->update(['status' => TraSubmissionStatus::Blocked]);

            return;
        }

        $payload = $submission->payload_snapshot ?? [];

        $submission->update(['status' => TraSubmissionStatus::Sending]);

        try {
            $result = $submission->kind === TraSubmissionKind::Principal
                ? $client->sendPrincipal($payload)
                : $client->sendCompanion($payload);
        } catch (TraApiException $exception) {
            $this->recordAttempt($submission, null, 'transport', $exception->getMessage());
            $submission->update([
                'status' => TraSubmissionStatus::Pending,
                'last_error_code' => 'transport',
                'last_error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        if (! $result->sent) {
            $this->recordAttempt($submission, $result->status, 'validation', $result->errorMessage, $result->errorCode);
            $submission->update([
                'status' => TraSubmissionStatus::Rejected,
                'last_error_code' => $result->errorCode,
                'last_error_message' => $result->errorMessage,
            ]);

            return;
        }

        $this->recordAttempt($submission, $result->status, null, null, null, $result->reference);
        $submission->update([
            'status' => TraSubmissionStatus::Sent,
            'external_reference' => $result->reference,
            'last_error_code' => null,
            'last_error_message' => null,
            'sent_at' => now(),
        ]);

        $this->unblockCompanions($submission);
    }

    public function failed(null|\Throwable $exception): void
    {
        TraSubmission::query()->whereKey($this->submissionId)->update([
            'status' => TraSubmissionStatus::Failed,
            'last_error_code' => 'exhausted',
            'last_error_message' => $exception?->getMessage(),
        ]);
    }

    private function principalSent(TraSubmission $submission): bool
    {
        return TraSubmission::query()
            ->where('room_occupancy_id', $submission->room_occupancy_id)
            ->where('kind', TraSubmissionKind::Principal)
            ->where('status', TraSubmissionStatus::Sent)
            ->exists();
    }

    private function unblockCompanions(TraSubmission $submission): void
    {
        if ($submission->kind !== TraSubmissionKind::Principal) {
            return;
        }

        $companions = TraSubmission::query()
            ->where('room_occupancy_id', $submission->room_occupancy_id)
            ->where('kind', TraSubmissionKind::Companion)
            ->whereIn('status', [TraSubmissionStatus::Blocked, TraSubmissionStatus::Pending])
            ->get();

        foreach ($companions as $companion) {
            $companion->update(['status' => TraSubmissionStatus::Pending]);
            self::dispatch($companion->id);
        }
    }

    private function recordAttempt(TraSubmission $submission, null|int $status, null|string $category = null, null|string $message = null, null|string $code = null, null|string $reference = null): void
    {
        $payload = $submission->payload_snapshot ?? [];
        unset($payload['token']);

        $submission->attempts()->create([
            'channel' => 'api',
            'request_hash' => hash('sha256', json_encode($payload) ?? ''),
            'response_status' => $status,
            'response_reference' => $reference,
            'error_category' => $category,
            'error_message' => $message === null ? null : mb_substr("[{$code}] {$message}", 0, 1000),
        ]);
    }
}
