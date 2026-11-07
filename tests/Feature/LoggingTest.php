<?php

use App\Jobs\GeneratePdfCvJob;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    Http::preventStrayRequests();

    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');
});

function makeLoggingPengguna(): Pengguna
{
    return Pengguna::create([
        'sso_id' => 'logging-user',
        'nama' => 'Logging User',
        'peran' => 'mahasiswa',
        'id_prodi' => 'prodi-1',
    ]);
}

test('fatal http exceptions are logged with request context', function () {
    $pengguna = makeLoggingPengguna();

    Route::get('/__logging-test', fn () => throw new HttpException(500, 'boom'));

    Log::spy();

    $this->actingAs($pengguna)->get('/__logging-test')->assertStatus(500);

    Log::shouldHaveReceived('error')
        ->withArgs(fn (string $message, array $context = []): bool => $context['url'] === url('/__logging-test')
            && $context['user_id'] === $pengguna->id
            && $context['message'] === 'boom');
});

test('pdf job logs when it starts and when it completes', function () {
    Log::spy();

    (new GeneratePdfCvJob(42))->handle();

    Log::shouldHaveReceived('info')
        ->withArgs(fn (string $message): bool => str_contains($message, '42')
            && str_contains($message, 'sedang berjalan'))
        ->once();

    Log::shouldHaveReceived('info')
        ->withArgs(fn (string $message): bool => str_contains($message, '42')
            && str_contains($message, 'berhasil'))
        ->once();
});

test('pdf job logs when it fails', function () {
    Log::spy();

    (new GeneratePdfCvJob(42))->failed(new RuntimeException('boom'));

    Log::shouldHaveReceived('error')
        ->withArgs(fn (string $message): bool => str_contains($message, '42')
            && str_contains($message, 'boom'))
        ->once();
});

test('failed user persistence is logged with the payload', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->url() === 'https://sso.unuja.ac.id/portal/data/authorize') {
            return Http::response([
                'success' => true,
                'data' => [
                    'info' => [
                        'urls' => [
                            'data' => 'https://sso.unuja.ac.id/portal/data/data/test-token',
                        ],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'lembaga') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_lembaga' => 'lembaga-1', 'lembaga' => 'Fakultas Teknik'],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'karyawan') {
            return Http::response([
                'success' => true,
                'data' => [
                    ['id_penduduk' => 'penduduk-1', 'struktur' => 'Dekan Fakultas Teknik'],
                ],
            ]);
        }

        return Http::response([
            'success' => true,
            'data' => [
                'data' => [
                    [
                        'nidn' => '0702078504',
                        'nama_penduduk' => 'MATLUBUL KHAIRI',
                        'id_penduduk' => 'penduduk-1',
                        'fakultas' => 'Fakultas Teknik',
                        'id_sms' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
                    ],
                ],
            ],
        ]);
    });

    Pengguna::saving(fn () => throw new RuntimeException('db down'));

    Log::spy();

    $this->post('/login', ['nim_nidn' => '0702078504'])->assertStatus(500);

    Log::shouldHaveReceived('error')
        ->withArgs(fn (string $message, array $context = []): bool => $message === 'Gagal menyimpan data pengguna saat login.'
            && $context['sso_id'] === '0702078504'
            && $context['payload']['peran'] === 'dekan');
});
