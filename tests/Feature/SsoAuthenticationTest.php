<?php

use App\Models\Pengguna;
use App\Models\ProfilPengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        ->assertRedirect(route('dashboard.mahasiswa'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '1234567890',
        'nama' => 'Fahmi Bahrul Widad',
        'email' => 'fa******nt@unuja.ac.id',
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
        ->assertRedirect(route('dashboard.mahasiswa'));

    $pengguna->refresh();

    expect($pengguna->nama)->toBe('Nama Baru');
    expect($pengguna->peran)->toBe('mahasiswa');
    expect($pengguna->id_prodi)->toBe('TE');
    expect(Pengguna::count())->toBe(1);

    $this->assertAuthenticatedAs($pengguna);
});

test('user profile is synced from sso after login', function () {
    fakeSsoProfile([
        'nim' => '1234567890',
        'nama' => 'Fahmi Bahrul Widad',
        'id_sms' => '6bb7c485-08a3-465c-b549-e5223c2affe2',
        'prodi' => 'Teknologi Informasi',
        'gelar_depan' => 'Ir.',
        'gelar_belakang' => 'S.Kom.',
        'jenis_kelamin' => 'L',
        'no_hp' => '081234567890',
        'id_fakultas' => 'lembaga-1',
        'fakultas' => 'Fakultas Teknik',
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $pengguna = Pengguna::where('sso_id', '1234567890')->firstOrFail();

    $this->assertDatabaseHas('profil_pengguna', [
        'pengguna_id' => $pengguna->id,
        'gelar_depan' => 'Ir.',
        'gelar_belakang' => 'S.Kom.',
        'jenis_kelamin' => 'L',
        'no_hp' => '081234567890',
        'id_fakultas' => 'lembaga-1',
        'nama_fakultas' => 'Fakultas Teknik',
        'nama_prodi' => 'Teknologi Informasi',
    ]);

    expect($pengguna->profil)
        ->toBeInstanceOf(ProfilPengguna::class)
        ->and($pengguna->profil?->nama_prodi)->toBe('Teknologi Informasi');
});

test('existing profile is updated when logging in again', function () {
    $pengguna = Pengguna::create([
        'sso_id' => '1234567890',
        'nama' => 'Nama Lama',
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
    ]);

    ProfilPengguna::create([
        'pengguna_id' => $pengguna->id,
        'gelar_depan' => 'Prof.',
        'jenis_kelamin' => 'P',
        'no_hp' => '080000000000',
        'id_fakultas' => 'lembaga-lama',
        'nama_fakultas' => 'Fakultas Lama',
        'nama_prodi' => 'Prodi Lama',
    ]);

    fakeSsoProfile([
        'nim' => '1234567890',
        'nama' => 'Nama Baru',
        'id_sms' => 'TE',
        'prodi' => 'Teknik Elektro',
        'gelar_depan' => 'Ir.',
        'gelar_belakang' => 'M.T.',
        'jenis_kelamin' => 'L',
        'no_hp' => '081234567890',
        'id_fakultas' => 'lembaga-1',
        'fakultas' => 'Fakultas Teknik',
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $this->assertDatabaseHas('profil_pengguna', [
        'pengguna_id' => $pengguna->id,
        'gelar_depan' => 'Ir.',
        'gelar_belakang' => 'M.T.',
        'jenis_kelamin' => 'L',
        'no_hp' => '081234567890',
        'id_fakultas' => 'lembaga-1',
        'nama_fakultas' => 'Fakultas Teknik',
        'nama_prodi' => 'Teknik Elektro',
    ]);

    expect(ProfilPengguna::count())->toBe(1);
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

        if ($request['filter'] === 'lembaga') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_lembaga' => 'lembaga-1', 'lembaga' => 'Fakultas Teknik'],
                        ['id_lembaga' => 'lembaga-2', 'lembaga' => 'Fakultas Ekonomi'],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'karyawan') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        [
                            'id_penduduk' => 'penduduk-1',
                            'struktur' => 'Dosen Tetap',
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
                        'email' => 'matlubul@unuja.ac.id',
                        'id_penduduk' => 'penduduk-1',
                        'fakultas' => 'Fakultas Teknik',
                        'id_sms' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
                        'prodi' => 'Teknik Informatika',
                    ],
                ],
            ],
        ]);
    });

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard.dosen'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '0702078504',
        'nama' => 'MATLUBUL KHAIRI',
        'email' => 'matlubul@unuja.ac.id',
        'peran' => 'dosen',
        'id_prodi' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
    ]);
});

test('dosen struktur determines the mapped role', function (string $struktur, string $expectedPeran) {
    Http::fake(function (ClientRequest $request) use ($struktur) {
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
                    'data' => [],
                ],
            ]);
        }

        if ($request['filter'] === 'lembaga') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_lembaga' => 'lembaga-1', 'lembaga' => 'Fakultas Teknik'],
                        ['id_lembaga' => 'lembaga-2', 'lembaga' => 'Fakultas Ekonomi'],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'karyawan') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_penduduk' => 'penduduk-2', 'struktur' => 'Dekan Fakultas Teknik'],
                        ['id_penduduk' => 'penduduk-1', 'struktur' => $struktur],
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
                        'id_penduduk' => 'penduduk-1',
                        'fakultas' => 'Fakultas Teknik',
                        'id_sms' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
                        'prodi' => 'Teknik Informatika',
                    ],
                ],
            ],
        ]);
    });

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard.'.$expectedPeran));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '0702078504',
        'peran' => $expectedPeran,
    ]);

    Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://sso.unuja.ac.id/portal/data/data/test-token'
        && $request['filter'] === 'karyawan'
        && ($request['id_lembaga'] ?? null) === 'lembaga-1'
        && $request->hasHeader('X-Token', 'test-x-token'));
})->with([
    'dekan' => ['Dekan Fakultas Teknik', 'dekan'],
    'wakil dekan' => ['Wakil Dekan I', 'dekan'],
    'ketua prodi' => ['Ketua Prodi Teknik Informatika', 'kaprodi'],
    'kaprodi' => ['KAPRODI Teknik Informatika', 'kaprodi'],
    'dosen' => ['Dosen Tetap', 'dosen'],
]);

test('karyawan data is fetched from the dynamic url with auth headers', function () {
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
                    'data' => [],
                ],
            ]);
        }

        if ($request['filter'] === 'lembaga') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_lembaga' => 'lembaga-1', 'lembaga' => 'Fakultas Teknik'],
                        ['id_lembaga' => 'lembaga-2', 'lembaga' => 'Fakultas Ekonomi'],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'karyawan') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [
                        ['id_penduduk' => 'penduduk-2', 'struktur' => 'Dekan Fakultas Teknik'],
                        ['id_penduduk' => 'penduduk-1', 'struktur' => 'Ketua Prodi Teknik Informatika'],
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
                        'id_penduduk' => 'penduduk-1',
                        'fakultas' => 'Fakultas Teknik',
                        'id_sms' => '1319bfef-2188-4240-b7a3-7a507bec2e64',
                    ],
                ],
            ],
        ]);
    });

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard.kaprodi'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '0702078504',
        'peran' => 'kaprodi',
    ]);

    Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://sso.unuja.ac.id/portal/data/data/test-token'
        && $request['filter'] === 'lembaga'
        && $request->hasHeader('X-Token', 'test-x-token'));

    Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://sso.unuja.ac.id/portal/data/data/test-token'
        && $request['filter'] === 'karyawan'
        && ($request['id_lembaga'] ?? null) === 'lembaga-1'
        && $request->hasHeader('X-Token', 'test-x-token'));
});

test('karyawan is not requested when no lembaga matches the dosen faculty', function () {
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
                        ['id_lembaga' => 'lembaga-2', 'lembaga' => 'Fakultas Ekonomi'],
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

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard.dosen'));

    $this->assertDatabaseHas('pengguna', [
        'sso_id' => '0702078504',
        'peran' => 'dosen',
    ]);

    Http::assertNotSent(fn (ClientRequest $request) => ($request['filter'] ?? null) === 'karyawan');
});

test('role evaluation is logged with the matched karyawan data', function () {
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
                        ['id_lembaga' => 'lembaga-2', 'lembaga' => 'Fakultas Ekonomi'],
                    ],
                ],
            ]);
        }

        if ($request['filter'] === 'karyawan') {
            return Http::response([
                'success' => true,
                'data' => [
                    ['id_penduduk' => 'penduduk-2', 'struktur' => 'Ketua Prodi Teknik Informatika'],
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

    Log::spy();

    $this->post('/login', ['nim_nidn' => '0702078504'])
        ->assertRedirect(route('dashboard.dekan'));

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Evaluasi Role SSO', Mockery::on(fn (array $context): bool => $context['id_penduduk'] === 'penduduk-1'
            && $context['hasil_peran'] === 'dekan'
            && $context['data_karyawan_ditemukan']['struktur'] === 'Dekan Fakultas Teknik'));
});

test('sso api failure response is logged as warning with the response body', function () {
    Http::fake([
        'sso.unuja.ac.id/portal/data/authorize' => Http::response([
            'success' => false,
            'message' => 'invalid credentials',
        ]),
    ]);

    Log::spy();

    $this->from('/login')
        ->post('/login', ['nim_nidn' => '0000000000'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('nim_nidn');

    Log::shouldHaveReceived('warning')
        ->withArgs(fn (string $message, array $context = []): bool => str_contains($message, 'SSO')
            && str_contains($context['body'] ?? '', 'invalid credentials'));
});

test('sso connection errors are logged with the api error prefix', function () {
    Http::fake(fn () => throw new ConnectionException('SSO down'));

    Log::spy();

    $this->from('/login')
        ->post('/login', ['nim_nidn' => '0000000000'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('nim_nidn');

    Log::shouldHaveReceived('error')
        ->withArgs(fn (string $message): bool => str_contains($message, 'API Error pada SSO: SSO down'));
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
    $this->get('/mahasiswa/dashboard')->assertRedirect(route('login'));
});
