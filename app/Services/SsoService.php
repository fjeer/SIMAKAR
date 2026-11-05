<?php

namespace App\Services;

use App\Exceptions\SsoAuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsoService
{
    /**
     * Fetch a user profile from the campus SSO API.
     *
     * @return array<string, mixed>
     *
     * @throws SsoAuthenticationException
     */
    public function getProfile(string $nimNidn): array
    {
        try {
            $authorization = $this->authorize($nimNidn);

            $dataUrl = data_get($authorization, 'urls.data');

            if (! is_string($dataUrl) || blank($dataUrl)) {
                throw new SsoAuthenticationException('Data profil dari SSO tidak valid.');
            }

            foreach (['mahasiswa', 'dosen'] as $peran) {
                $profile = $this->fetchProfile($dataUrl, $peran, $nimNidn);

                if ($profile !== null) {
                    return [...$profile, 'peran' => $peran];
                }
            }
        } catch (ConnectionException $exception) {
            Log::error('Koneksi ke SSO gagal.', [
                'message' => $exception->getMessage(),
            ]);

            throw new SsoAuthenticationException(
                'Layanan SSO tidak dapat dihubungi.',
                previous: $exception,
            );
        }

        throw new SsoAuthenticationException(
            'NIM/NIDN tidak ditemukan atau layanan SSO sedang tidak tersedia.'
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SsoAuthenticationException
     */
    private function authorize(string $nimNidn): array
    {
        $response = $this->client()->post($this->endpoint(), [
            'username' => $nimNidn,
        ]);

        if ($response->failed()) {
            $this->logFailedResponse('Permintaan authorize SSO gagal.', $response);

            throw new SsoAuthenticationException(
                'NIM/NIDN tidak ditemukan atau layanan SSO sedang tidak tersedia.'
            );
        }

        $authorization = $response->json('data.info');

        if (! is_array($authorization)) {
            throw new SsoAuthenticationException('Data profil dari SSO tidak valid.');
        }

        return $authorization;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchProfile(string $dataUrl, string $peran, string $nimNidn): ?array
    {
        $response = $this->client()->post($dataUrl, [
            'filter' => $peran,
            'search' => $nimNidn,
        ]);

        if ($response->failed()) {
            $this->logFailedResponse('Permintaan data SSO gagal.', $response);

            return null;
        }

        $payload = $response->json();

        $records = data_get($payload, 'data.data');
        $records = is_array($records) ? $records : data_get($payload, 'data');

        if (! is_array($records)) {
            return null;
        }

        $identifierKey = $peran === 'dosen' ? 'nidn' : 'nim';

        foreach ($records as $record) {
            if (is_array($record) && (string) data_get($record, $identifierKey) === $nimNidn) {
                return $record;
            }
        }

        return null;
    }

    private function client(): PendingRequest
    {
        return Http::withHeaders([
            'X-App-Id' => (string) config('services.sso.app_id'),
            'X-Dev-Id' => (string) config('services.sso.dev_id'),
            'X-Token' => (string) config('services.sso.x_token'),
        ])
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(10);
    }

    private function endpoint(): string
    {
        $endpoint = config('services.sso.endpoint');

        if (blank($endpoint)) {
            throw new SsoAuthenticationException('Endpoint SSO belum dikonfigurasi.');
        }

        return $endpoint;
    }

    private function logFailedResponse(string $message, Response $response): void
    {
        Log::warning($message, [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }
}
