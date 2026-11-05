<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

/**
 * @param  array<string, mixed>|null  $profile
 */
function fakeSsoProfile(?array $profile): void
{
    Http::fake([
        'sso.unuja.ac.id/portal/data/authorize' => Http::response([
            'success' => true,
            'data' => [
                'info' => [
                    'urls' => [
                        'data' => 'https://sso.unuja.ac.id/portal/data/data/test-token',
                    ],
                ],
            ],
        ]),
        'sso.unuja.ac.id/portal/data/data/*' => Http::response([
            'success' => true,
            'data' => [
                'data' => $profile === null ? [] : [$profile],
            ],
        ]),
    ]);
}

beforeEach(function () {
    $this->withoutVite();

    Http::preventStrayRequests();

    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');
});

test('login page can be rendered', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Masuk ke SIMAKAR');
});

test('user is synced and authenticated when sso returns a profile', function () {
    fakeSsoProfile([
        'nim' => '1234567890',
        'nama' => 'Fahmi Bahrul Widad',
        'email' => 'fa******nt@unuja.ac.id',
        'id_sms' => '6bb7c485-08a3-465c-b549-e5223c2affe2',
        'prodi' => 'Teknologi Informasi',
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '1234567890',
        'nama' => 'Fahmi Bahrul Widad',
        'peran' => 'mahasiswa',
        'id_prodi' => '6bb7c485-08a3-465c-b549-e5223c2affe2',
    ]);

    $pengguna = Pengguna::where('sso_id', '1234567890')->firstOrFail();

    $this->assertAuthenticatedAs($pengguna);

    Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://sso.unuja.ac.id/portal/data/authorize'
        && $request->hasHeader('X-App-Id', 'test-app-id')
        && $request->hasHeader('X-Dev-Id', 'test-dev-id')
        && $request->hasHeader('X-Token', 'test-x-token'));

    Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://sso.unuja.ac.id/portal/data/data/test-token'
        && $request['filter'] === 'mahasiswa'
        && $request['search'] === '1234567890');
});

test('existing user is updated when logging in again', function () {
    $pengguna = Pengguna::create([
        'sso_id' => '1234567890',
        'nama' => 'Nama Lama',
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
    ]);

    fakeSsoProfile([
        'nim' => '1234567890',
        'nama' => 'Nama Baru',
        'id_sms' => 'TE',
        'prodi' => 'Teknik Elektro',
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard'));

    $pengguna->refresh();

    expect($pengguna->nama)->toBe('Nama Baru');
    expect($pengguna->peran)->toBe('mahasiswa');
    expect($pengguna->id_prodi)->toBe('TE');
    expect(Pengguna::count())->toBe(1);

    $this->assertAuthenticatedAs($pengguna);
});

test('dosen is resolved with an exact nidn match', function () {
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

        if ($request['filter'] === 'mahasiswa') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        [
                            'nim' => '2021400163',
                            'nidn' => '0702078504',
                            'nama' => 'DUMAIRI',
                            'id_sms' => 'wrong-prodi',
                        ],
                    ],
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
                        'id_sms' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
                        'prodi' => 'Teknik Informatika',
                    ],
                ],
            ],
        ]);
    });

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '0702078504',
        'nama' => 'MATLUBUL KHAIRI',
        'peran' => 'dosen',
        'id_prodi' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
    ]);
});

test('failed sso response does not authenticate the user', function () {
    fakeSsoProfile(null);

    $this->from('/login')
        ->post('/login', ['nim_nidn' => '0000000000'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('nim_nidn');

    $this->assertGuest();
    $this->assertDatabaseMissing('pengguna', ['sso_id' => '0000000000']);
});

test('nim nidn is required', function () {
    $this->from('/login')
        ->post('/login', ['nim_nidn' => ''])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('nim_nidn');

    $this->assertGuest();
});

test('dashboard requires authentication', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
});
