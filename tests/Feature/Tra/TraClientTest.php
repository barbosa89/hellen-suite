<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Services\Tra\TraApiException;
use App\Services\Tra\TraClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TraClientTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_posts_the_principal_to_the_one_endpoint_as_json(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'pms.mincit.gov.co/one/*' => Http::response(['reference' => 'TRA-1'], 200),
        ]);

        $result = (new TraClient())->sendPrincipal(['rnt' => '12345', 'token' => 'secret']);

        $this->assertTrue($result->sent);
        $this->assertSame(200, $result->status);
        $this->assertSame('TRA-1', $result->reference);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://pms.mincit.gov.co/one/'
                && $request->header('Content-Type') === ['application/json']
                && $request->header('Accept') === ['application/json']
                && $request->data() === ['rnt' => '12345', 'token' => 'secret'];
        });
    }

    #[Test]
    public function it_posts_companions_to_the_two_endpoint(): void
    {
        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/two/*' => Http::response(null, 201)]);

        $result = (new TraClient())->sendCompanion(['documento_principal' => '1020304050']);

        $this->assertTrue($result->sent);
        $this->assertSame(201, $result->status);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://pms.mincit.gov.co/two/');
    }

    #[Test]
    public function it_maps_client_errors_to_rejections_without_throwing(): void
    {
        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/one/*' => Http::response('RNT inválido', 422)]);

        $result = (new TraClient())->sendPrincipal(['rnt' => 'bad']);

        $this->assertFalse($result->sent);
        $this->assertSame(422, $result->status);
        $this->assertSame('http_422', $result->errorCode);
    }

    #[Test]
    public function it_throws_on_server_errors_and_connection_failures(): void
    {
        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/one/*' => Http::response(null, 500)]);

        $this->expectException(TraApiException::class);

        (new TraClient())->sendPrincipal(['rnt' => '12345']);
    }

    #[Test]
    public function it_throws_on_connection_failures(): void
    {
        Http::preventStrayRequests();
        Http::fake(['pms.mincit.gov.co/one/*' => Http::failedConnection()]);

        $this->expectException(TraApiException::class);

        (new TraClient())->sendPrincipal(['rnt' => '12345']);
    }
}
