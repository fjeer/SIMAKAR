<?php

use App\Models\Keahlian;
use App\Models\Pendidikan;
use App\Models\Pengalaman;
use App\Models\Pengguna;
use App\Models\ProfilPengguna;
use App\Models\Sertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function mahasiswaCvPdf(array $attributes = []): Pengguna
{
    $mahasiswa = Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => 'Fahmi Bahrul Widad',
        'email' => 'fahmi@unuja.ac.id',
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
        ...$attributes,
    ]);

    ProfilPengguna::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_prodi' => 'Teknik Informatika',
        'no_hp' => '081234567890',
        'alamat' => 'Probolinggo, Jawa Timur',
        'bio' => 'Mahasiswa Teknik Informatika yang tertarik pada pengembangan web.',
    ]);

    Pendidikan::create([
        'pengguna_id' => $mahasiswa->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Universitas Nurul Jadid',
        'jurusan' => 'Teknik Informatika',
        'tahun_mulai' => '2022',
        'nilai_akhir' => '3.75',
        'sumber_data' => 'SSO',
        'is_verified' => true,
    ]);

    Pengalaman::create([
        'pengguna_id' => $mahasiswa->id,
        'jenis' => 'Magang',
        'posisi' => 'Backend Developer',
        'nama_institusi' => 'PT Teknologi Nusantara',
        'deskripsi' => "Membangun API internal.\nMenulis pengujian otomatis.",
        'tanggal_mulai' => '2024-01-01',
        'tanggal_selesai' => '2024-06-30',
    ]);

    Sertifikasi::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_sertifikasi' => 'Junior Web Developer',
        'penerbit' => 'BNSP',
        'tanggal_terbit' => '2024-02-01',
    ]);

    Keahlian::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_keahlian' => 'Laravel',
        'tingkat' => 'Mahir',
    ]);

    return $mahasiswa;
}

test('halaman preview menampilkan tombol download dan pratinjau cv', function () {
    $mahasiswa = mahasiswaCvPdf();

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.preview.index'))
        ->assertOk()
        ->assertSee('Download CV (PDF ATS Friendly)')
        ->assertSee(route('mahasiswa.preview.download'), false)
        ->assertSee(route('mahasiswa.preview.cv'), false);
});

test('halaman cv menampilkan seluruh bagian cv ats', function () {
    $mahasiswa = mahasiswaCvPdf();

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.preview.cv'))
        ->assertOk()
        ->assertSee('Fahmi Bahrul Widad')
        ->assertSee('fahmi@unuja.ac.id')
        ->assertSee('081234567890')
        ->assertSee('Tentang Saya')
        ->assertSee('Pendidikan')
        ->assertSee('Universitas Nurul Jadid')
        ->assertSee('Pengalaman')
        ->assertSee('Backend Developer')
        ->assertSee('Membangun API internal.')
        ->assertSee('Sertifikasi')
        ->assertSee('BNSP')
        ->assertSee('Keahlian')
        ->assertSee('Laravel (Mahir)');
});

test('mahasiswa dapat mengunduh cv dalam format pdf', function () {
    $mahasiswa = mahasiswaCvPdf();

    $response = $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.preview.download'));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->headers->get('content-disposition'))
        ->toContain('CV_ATS_')
        ->toContain('Fahmi Bahrul Widad');

    expect($response->getContent())->toStartWith('%PDF');
});

test('tamu diarahkan ke login saat mengakses preview dan download cv', function () {
    $this->get(route('mahasiswa.preview.index'))->assertRedirect(route('login'));
    $this->get(route('mahasiswa.preview.cv'))->assertRedirect(route('login'));
    $this->get(route('mahasiswa.preview.download'))->assertRedirect(route('login'));
});

test('pengguna selain mahasiswa tidak dapat mengakses preview dan download cv', function () {
    $dosen = Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'peran' => 'dosen',
        'id_prodi' => 'TI',
    ]);

    $this->actingAs($dosen)
        ->get(route('mahasiswa.preview.index'))
        ->assertForbidden();

    $this->actingAs($dosen)
        ->get(route('mahasiswa.preview.cv'))
        ->assertForbidden();

    $this->actingAs($dosen)
        ->get(route('mahasiswa.preview.download'))
        ->assertForbidden();
});
