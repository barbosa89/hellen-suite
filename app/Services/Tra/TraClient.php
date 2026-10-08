<?php

declare(strict_types=1);

namespace App\Services\Tra;

use App\Constants\TraSubmissionKind;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

final class TraClient
{
    public function sendPrincipal(array $payload): TraResult
    {
        return $this->post('/one/', $payload);
    }

    public function sendCompanion(array $payload): TraResult
    {
        return $this->post('/two/', $payload);
    }

    private function post(string $path, array $payload): TraResult
    {
        try {
            $response = $this->request()->post($path, $payload);
        } catch (ConnectionException $exception) {
            throw TraApiException::transport($exception->getMessage());
        } catch (Throwable $exception) {
            throw TraApiException::transport($exception->getMessage());
        }

        if ($response->successful()) {
            $body = $response->json();

            return TraResult::sent($response->status(), is_array($body) ? ($body['reference'] ?? $body['id'] ?? null) : null);
        }

        if ($response->serverError()) {
            throw TraApiException::transport("TRA respondió {$response->status()}");
        }

        return TraResult::rejected(
            $response->status(),
            "http_{$response->status()}",
            mb_substr($response->body(), 0, 500),
        );
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl((string) config('services.tra.base_url'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout((int) config('services.tra.connect_timeout', 3))
            ->timeout((int) config('services.tra.timeout', 10))
            ->retry(
                (int) config('services.tra.retries', 2),
                (int) config('services.tra.retry_sleep_ms', 500),
                throw: false,
            );
    }

    public static function pathFor(TraSubmissionKind $kind): string
    {
        return $kind === TraSubmissionKind::Principal ? '/one/' : '/two/';
    }
}
