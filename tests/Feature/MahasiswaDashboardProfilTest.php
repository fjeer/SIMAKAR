<?php

use App\Models\Keahlian;
use App\Models\Pendidikan;
use App\Models\Pengalaman;
use App\Models\Pengguna;
use App\Models\ProfilPengguna;
use App\Models\Sertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function mahasiswaProfil(array $attributes = []): Pengguna
{
    return Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
        ...$attributes,
    ]);
}

/**
 * @param  array{ketuntasan?: array<string, mixed>, mahasiswa?: array<string, mixed>}  $transkrip
 */
function fakeSsoLoginApi(array $transkrip = []): void
{
    Http::fake(function (ClientRequest $request) use ($transkrip) {
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

        if ($request['filter'] === 'transkrip') {
            return Http::response([
                'success' => true,
                'data' => [
                    'ketuntasan' => $transkrip['ketuntasan'] ?? [],
                    'mahasiswa' => $transkrip['mahasiswa'] ?? [],
                ],
            ]);
        }

        if ($request['filter'] === 'mahasiswa') {
            return Http::response([
                'success' => true,
                'data' => [
                    'data' => [[
                        'nim' => '1234567890',
                        'nama' => 'Fahmi Bahrul Widad',
                        'email' => 'fahmi@unuja.ac.id',
                        'id_sms' => 'TI',
                        'prodi' => 'Teknik Informatika',
                    ]],
                ],
            ]);
        }

        return Http::response([
            'success' => true,
            'data' => ['data' => []],
        ]);
    });
}

test('dashboard mahasiswa menampilkan sapaan dan total data cv', function () {
    $mahasiswa = mahasiswaProfil(['nama' => 'Fahmi Bahrul Widad']);

    foreach (['SMA/SMK', 'S1'] as $jenjang) {
        Pendidikan::create([
            'pengguna_id' => $mahasiswa->id,
            'jenjang' => $jenjang,
            'nama_institusi' => 'Institusi '.$jenjang,
            'tahun_mulai' => '2020',
        ]);
    }

    Pengalaman::create([
        'pengguna_id' => $mahasiswa->id,
        'jenis' => 'Magang',
        'posisi' => 'Backend Developer',
        'nama_institusi' => 'PT Contoh',
        'tanggal_mulai' => '2024-01-01',
    ]);

    Sertifikasi::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_sertifikasi' => 'Junior Web Developer',
        'penerbit' => 'BNSP',
        'tanggal_terbit' => '2024-02-01',
    ]);

    foreach (['Laravel', 'Vue', 'MySQL'] as $namaKeahlian) {
        Keahlian::create([
            'pengguna_id' => $mahasiswa->id,
            'nama_keahlian' => $namaKeahlian,
            'tingkat' => 'Mahir',
        ]);
    }

    $this->actingAs($mahasiswa)
        ->get(route('dashboard.mahasiswa'))
        ->assertOk()
        ->assertSee('Selamat Datang, Fahmi Bahrul Widad')
        ->assertSee(route('mahasiswa.profil.index'), false)
        ->assertViewHas('totalPendidikan', 2)
        ->assertViewHas('totalPengalaman', 1)
        ->assertViewHas('totalSertifikasi', 1)
        ->assertViewHas('totalKeahlian', 3);
});

test('halaman profil menampilkan identitas sso yang tidak dapat diubah', function () {
    $mahasiswa = mahasiswaProfil([
        'nama' => 'Fahmi Bahrul Widad',
        'email' => 'fahmi@unuja.ac.id',
        'id_prodi' => 'TI',
    ]);

    ProfilPengguna::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_prodi' => 'Teknik Informatika',
    ]);

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.profil.index'))
        ->assertOk()
        ->assertSee('Profil CV')
        ->assertSee('Fahmi Bahrul Widad')
        ->assertSee('fahmi@unuja.ac.id')
        ->assertSee('Teknik Informatika')
        ->assertSee('readonly', false)
        ->assertSee('Tempat Lahir')
        ->assertSee('Tanggal Lahir')
        ->assertSee('Alamat Domisili')
        ->assertSee('Bio / Tentang Saya');
});

test('mahasiswa dapat memperbarui detail profilnya', function () {
    $mahasiswa = mahasiswaProfil();

    ProfilPengguna::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_prodi' => 'Teknik Informatika',
    ]);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.profil.update'), [
            'tempat_lahir' => 'Probolinggo',
            'tanggal_lahir' => '2003-05-17',
            'alamat' => 'Jl. Raya Panglima Sudirman No. 1',
            'bio' => 'Mahasiswa Teknik Informatika yang tertarik pada pengembangan web.',
        ])
        ->assertRedirect(route('mahasiswa.profil.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('profil_pengguna', [
        'pengguna_id' => $mahasiswa->id,
        'tempat_lahir' => 'Probolinggo',
        'alamat' => 'Jl. Raya Panglima Sudirman No. 1',
        'bio' => 'Mahasiswa Teknik Informatika yang tertarik pada pengembangan web.',
    ]);

    expect(ProfilPengguna::where('pengguna_id', $mahasiswa->id)->count())->toBe(1)
        ->and(ProfilPengguna::where('pengguna_id', $mahasiswa->id)->first()?->nama_prodi)
        ->toBe('Teknik Informatika');
});

test('detail profil dibuat otomatis saat mahasiswa belum memiliki profil', function () {
    $mahasiswa = mahasiswaProfil();

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.profil.update'), [
            'tempat_lahir' => 'Probolinggo',
        ])
        ->assertRedirect(route('mahasiswa.profil.index'));

    $this->assertDatabaseHas('profil_pengguna', [
        'pengguna_id' => $mahasiswa->id,
        'tempat_lahir' => 'Probolinggo',
    ]);
});

test('pendidikan manual tersimpan dengan sumber data manual', function () {
    $mahasiswa = mahasiswaProfil();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.pendidikan.store'), [
            'jenjang' => 'SMA/SMK',
            'nama_institusi' => 'SMAN 1 Probolinggo',
            'tahun_mulai' => '2019',
            'tahun_selesai' => '2022',
        ])
        ->assertRedirect(route('mahasiswa.pendidikan.index'));

    $pendidikan = Pendidikan::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($pendidikan->sumber_data)->toBe('Manual')
        ->and($pendidikan->is_verified)->toBeFalse();
});

test('pendidikan dari sso tidak dapat diubah atau dihapus', function () {
    $mahasiswa = mahasiswaProfil();

    $pendidikan = Pendidikan::create([
        'pengguna_id' => $mahasiswa->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Universitas Nurul Jadid',
        'jurusan' => 'Teknik Informatika',
        'tahun_mulai' => '2022',
        'sumber_data' => 'SSO',
        'is_verified' => true,
    ]);

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.pendidikan.index'))
        ->assertOk()
        ->assertSee('SSO Terverifikasi')
        ->assertSee('Terkunci')
        ->assertDontSee(route('mahasiswa.pendidikan.destroy', $pendidikan), false);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.pendidikan.update', $pendidikan), [
            'jenjang' => 'S1',
            'nama_institusi' => 'Institusi Diubah',
            'tahun_mulai' => '2022',
        ])
        ->assertForbidden();

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.pendidikan.destroy', $pendidikan))
        ->assertForbidden();

    $this->assertDatabaseHas('cv_pendidikan', [
        'id' => $pendidikan->id,
        'nama_institusi' => 'Universitas Nurul Jadid',
        'sumber_data' => 'SSO',
        'is_verified' => true,
    ]);
});

test('transkrip sso disinkronkan sebagai pendidikan s1 terverifikasi saat login', function () {
    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');
    config()->set('services.sso.institusi', 'Universitas Nurul Jadid');

    Http::preventStrayRequests();

    fakeSsoLoginApi([
        'mahasiswa' => [
            'nim' => '1234567890',
            'prodi' => 'Teknik Informatika',
        ],
        'ketuntasan' => [
            'tahun_masuk' => '2022',
            'tahun_keluar' => null,
            'ipk_ketuntasan' => '3.75',
        ],
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $mahasiswa = Pengguna::where('sso_id', '1234567890')->firstOrFail();

    $pendidikan = Pendidikan::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($pendidikan->jenjang)->toBe('S1')
        ->and($pendidikan->nama_institusi)->toBe('Universitas Nurul Jadid')
        ->and($pendidikan->jurusan)->toBe('Teknik Informatika')
        ->and($pendidikan->tahun_mulai)->toBe('2022')
        ->and($pendidikan->tahun_selesai)->toBe('Sekarang')
        ->and($pendidikan->nilai_akhir)->toBe('3.75')
        ->and($pendidikan->sumber_data)->toBe('SSO')
        ->and($pendidikan->is_verified)->toBeTrue();

    Http::assertSent(fn (ClientRequest $request) => ($request['filter'] ?? null) === 'transkrip'
        && $request['pagination'] === 'on'
        && $request['nim'] === '1234567890');
});

test('tahun transkrip berkode semester dipotong dan ipk dari ketuntasan tersimpan', function () {
    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');

    Http::preventStrayRequests();

    fakeSsoLoginApi([
        'mahasiswa' => [
            'nim' => '1234567890',
            'prodi' => 'Teknik Informatika',
        ],
        'ketuntasan' => [
            'tahun_masuk' => '20221',
            'tahun_keluar' => '20262',
            'ipk_ketuntasan' => '3.82',
        ],
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $mahasiswa = Pengguna::where('sso_id', '1234567890')->firstOrFail();
    $pendidikan = Pendidikan::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($pendidikan->tahun_mulai)->toBe('2022')
        ->and($pendidikan->tahun_selesai)->toBe('2026')
        ->and($pendidikan->nilai_akhir)->toBe('3.82')
        ->and($pendidikan->jurusan)->toBe('Teknik Informatika');
});

test('transkrip tanpa tahun masuk menyimpan tahun mulai kosong dan tahun selesai sekarang', function () {
    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');

    Http::preventStrayRequests();

    fakeSsoLoginApi([
        'mahasiswa' => [
            'nim' => '1234567890',
            'prodi' => 'Teknik Informatika',
        ],
        'ketuntasan' => [
            'ipk_ketuntasan' => '3.90',
        ],
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $mahasiswa = Pengguna::where('sso_id', '1234567890')->firstOrFail();
    $pendidikan = Pendidikan::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($pendidikan->tahun_mulai)->toBeNull()
        ->and($pendidikan->tahun_selesai)->toBe('Sekarang')
        ->and($pendidikan->nilai_akhir)->toBe('3.90')
        ->and($pendidikan->jurusan)->toBe('Teknik Informatika');
});

test('login ulang tidak menduplikasi pendidikan sso', function () {
    config()->set('services.sso.endpoint', 'https://sso.unuja.ac.id/portal/data/authorize');
    config()->set('services.sso.app_id', 'test-app-id');
    config()->set('services.sso.dev_id', 'test-dev-id');
    config()->set('services.sso.x_token', 'test-x-token');

    Http::preventStrayRequests();

    fakeSsoLoginApi([
        'mahasiswa' => [
            'nim' => '1234567890',
            'prodi' => 'Teknik Informatika',
        ],
        'ketuntasan' => [
            'tahun_masuk' => '2022',
            'ipk_ketuntasan' => '3.75',
        ],
    ]);

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $this->post('/logout')->assertRedirect(route('login'));

    $this->post('/login', ['nim_nidn' => '1234567890'])
        ->assertRedirect(route('dashboard.mahasiswa'));

    $mahasiswa = Pengguna::where('sso_id', '1234567890')->firstOrFail();

    expect(Pendidikan::whereBelongsTo($mahasiswa)->count())->toBe(1);
});
