<?php

namespace App\Services;

use App\Exceptions\SsoAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

            foreach (['dosen', 'mahasiswa'] as $peran) {
                $profile = $this->fetchProfile($dataUrl, $peran, $nimNidn);

                if ($profile === null) {
                    continue;
                }

                if ($peran === 'dosen') {
                    $dataKaryawan = $this->fetchKaryawan($dataUrl, $profile);
                    $struktur = data_get($dataKaryawan, 'struktur');

                    return [
                        ...$profile,
                        'peran' => $peran,
                        'struktur' => is_scalar($struktur) ? (string) $struktur : '',
                        'data_karyawan' => $dataKaryawan,
                    ];
                }

                return [...$profile, 'peran' => $peran];
            }
        } catch (SsoAuthenticationException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            Log::error('API Error pada SSO: '.$exception->getMessage(), [
                'nim_nidn' => $nimNidn,
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
        try {
            $response = $this->client()->post($this->endpoint(), [
                'username' => $nimNidn,
            ]);
        } catch (\Exception $exception) {
            Log::error('API Error pada SSO: '.$exception->getMessage(), [
                'endpoint' => 'authorize',
            ]);

            throw new SsoAuthenticationException(
                'Layanan SSO tidak dapat dihubungi.',
                previous: $exception,
            );
        }

        $body = $response->json();

        if ($response->failed() || data_get($body, 'success') === false) {
            $this->logFailedResponse('Respons API SSO tidak berhasil.', $response);

            throw new SsoAuthenticationException(
                'NIM/NIDN tidak ditemukan atau layanan SSO sedang tidak tersedia.'
            );
        }

        $authorization = data_get($body, 'data.info');

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
        $identifierKey = $peran === 'dosen' ? 'nidn' : 'nim';

        foreach ($this->fetchRecords($dataUrl, $peran, ['search' => $nimNidn]) as $record) {
            if ((string) data_get($record, $identifierKey) === $nimNidn) {
                return $record;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $dosen
     * @return array<string, mixed>|null
     */
    private function fetchKaryawan(string $dataUrl, array $dosen): ?array
    {
        $idPenduduk = data_get($dosen, 'id_penduduk');

        if (! is_scalar($idPenduduk) || blank($idPenduduk)) {
            return null;
        }

        $idLembaga = $this->fetchIdLembaga($dataUrl, $dosen);

        if ($idLembaga === null) {
            return null;
        }

        return collect($this->fetchRecords($dataUrl, 'karyawan', ['id_lembaga' => $idLembaga]))
            ->firstWhere('id_penduduk', $idPenduduk);
    }

    /**
     * @param  array<string, mixed>  $dosen
     */
    private function fetchIdLembaga(string $dataUrl, array $dosen): int|string|null
    {
        $fakultasDosen = Str::lower((string) data_get($dosen, 'fakultas', ''));

        if (blank($fakultasDosen)) {
            return null;
        }

        $lembagaCocok = collect($this->fetchRecords($dataUrl, 'lembaga'))
            ->first(fn (array $lembaga): bool => Str::contains(
                Str::lower((string) data_get($lembaga, 'lembaga', '')),
                $fakultasDosen,
            ));

        $idLembaga = data_get($lembagaCocok, 'id_lembaga');

        if (! is_int($idLembaga) && ! is_string($idLembaga)) {
            return null;
        }

        return $idLembaga;
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return list<array<string, mixed>>
     */
    private function fetchRecords(string $dataUrl, string $filter, array $parameters = []): array
    {
        try {
            $response = $this->client()->post($dataUrl, [
                'filter' => $filter,
                ...$parameters,
            ]);
        } catch (\Exception $exception) {
            Log::error('API Error pada SSO: '.$exception->getMessage(), [
                'filter' => $filter,
            ]);

            throw $exception;
        }

        $body = $response->json();

        if ($response->failed() || data_get($body, 'success') === false) {
            $this->logFailedResponse('Respons API SSO tidak berhasil.', $response);

            return [];
        }

        $data = data_get($body, 'data') ?? [];

        $records = data_get($data, 'data');

        if (! is_array($records)) {
            $records = $data;
        }

        if (! is_array($records)) {
            return [];
        }

        if (! array_is_list($records) && (isset($records['id_penduduk']) || isset($records['id_lembaga']) || isset($records['nidn']) || isset($records['nim']))) {
            $records = [$records];
        }

        return array_values(array_filter($records, 'is_array'));
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
