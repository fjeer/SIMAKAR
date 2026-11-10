<?php

use App\Models\Keahlian;
use App\Models\Pendidikan;
use App\Models\Pengalaman;
use App\Models\Pengguna;
use App\Models\Sertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function mahasiswaPengguna(array $attributes = []): Pengguna
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

test('tamu diarahkan ke halaman login saat membuka CRUD CV', function () {
    $this->get(route('mahasiswa.pendidikan.index'))->assertRedirect(route('login'));
    $this->get(route('mahasiswa.keahlian.index'))->assertRedirect(route('login'));
});

test('mahasiswa dapat melihat halaman pendidikan dan keahlian', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.pendidikan.index'))
        ->assertOk()
        ->assertSee('Tambah Pendidikan');

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.keahlian.index'))
        ->assertOk()
        ->assertSee('Tambah Keahlian');
});

test('mahasiswa dapat menambahkan pendidikan miliknya', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.pendidikan.store'), [
            'jenjang' => 'S1',
            'nama_institusi' => 'Universitas Nurul Jadid',
            'jurusan' => 'Teknik Informatika',
            'tahun_mulai' => '2022',
            'tahun_selesai' => null,
            'nilai_akhir' => '3.75',
        ])
        ->assertRedirect(route('mahasiswa.pendidikan.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('cv_pendidikan', [
        'pengguna_id' => $mahasiswa->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Universitas Nurul Jadid',
    ]);
});

test('validasi pendidikan menolak data yang tidak valid', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.pendidikan.store'), [
            'jenjang' => 'Tidak Ada',
            'nama_institusi' => '',
            'tahun_mulai' => '20',
            'tahun_selesai' => '1800',
            'nilai_akhir' => '200',
        ])
        ->assertSessionHasErrors(['jenjang', 'nama_institusi', 'tahun_mulai', 'tahun_selesai', 'nilai_akhir']);

    $this->assertDatabaseCount('cv_pendidikan', 0);
});

test('mahasiswa dapat memperbarui dan menghapus pendidikannya', function () {
    $mahasiswa = mahasiswaPengguna();

    $pendidikan = Pendidikan::create([
        'pengguna_id' => $mahasiswa->id,
        'jenjang' => 'SMA/SMK',
        'nama_institusi' => 'SMAN 1',
        'tahun_mulai' => '2019',
        'tahun_selesai' => '2022',
    ]);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.pendidikan.update', $pendidikan), [
            'jenjang' => 'S1',
            'nama_institusi' => 'Universitas Nurul Jadid',
            'jurusan' => 'Teknik Informatika',
            'tahun_mulai' => '2022',
            'tahun_selesai' => '2026',
            'nilai_akhir' => '3.80',
        ])
        ->assertRedirect(route('mahasiswa.pendidikan.index'));

    $this->assertDatabaseHas('cv_pendidikan', [
        'id' => $pendidikan->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Universitas Nurul Jadid',
    ]);

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.pendidikan.destroy', $pendidikan))
        ->assertRedirect(route('mahasiswa.pendidikan.index'));

    $this->assertDatabaseMissing('cv_pendidikan', ['id' => $pendidikan->id]);
});

test('mahasiswa tidak dapat mengubah atau menghapus pendidikan milik mahasiswa lain', function () {
    $pemilik = mahasiswaPengguna();
    $mahasiswaLain = mahasiswaPengguna();

    $pendidikan = Pendidikan::create([
        'pengguna_id' => $pemilik->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Institusi Asli',
        'tahun_mulai' => '2020',
    ]);

    $this->actingAs($mahasiswaLain)
        ->put(route('mahasiswa.pendidikan.update', $pendidikan), [
            'jenjang' => 'S1',
            'nama_institusi' => 'Institusi Diubah',
            'tahun_mulai' => '2020',
        ])
        ->assertNotFound();

    $this->actingAs($mahasiswaLain)
        ->delete(route('mahasiswa.pendidikan.destroy', $pendidikan))
        ->assertNotFound();

    $this->assertDatabaseHas('cv_pendidikan', [
        'id' => $pendidikan->id,
        'nama_institusi' => 'Institusi Asli',
    ]);
});

test('mahasiswa dapat mengelola keahlian miliknya', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.keahlian.store'), [
            'nama_keahlian' => 'Laravel',
            'tingkat' => 'Mahir',
        ])
        ->assertRedirect(route('mahasiswa.keahlian.index'));

    $keahlian = Keahlian::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($keahlian->sumber_data)->toBe('Manual')
        ->and($keahlian->is_verified)->toBeFalse();

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.keahlian.update', $keahlian), [
            'nama_keahlian' => 'Laravel & Vue',
            'tingkat' => 'Ahli',
        ])
        ->assertRedirect(route('mahasiswa.keahlian.index'));

    $this->assertDatabaseHas('cv_keahlian', [
        'id' => $keahlian->id,
        'nama_keahlian' => 'Laravel & Vue',
        'tingkat' => 'Ahli',
    ]);

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.keahlian.destroy', $keahlian))
        ->assertRedirect(route('mahasiswa.keahlian.index'));

    $this->assertDatabaseMissing('cv_keahlian', ['id' => $keahlian->id]);
});

test('keahlian milik mahasiswa lain tidak dapat diubah', function () {
    $pemilik = mahasiswaPengguna();
    $mahasiswaLain = mahasiswaPengguna();

    $keahlian = Keahlian::create([
        'pengguna_id' => $pemilik->id,
        'nama_keahlian' => 'Laravel',
        'tingkat' => 'Mahir',
    ]);

    $this->actingAs($mahasiswaLain)
        ->put(route('mahasiswa.keahlian.update', $keahlian), [
            'nama_keahlian' => 'Diretas',
            'tingkat' => 'Ahli',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('cv_keahlian', [
        'id' => $keahlian->id,
        'nama_keahlian' => 'Laravel',
    ]);
});

test('pengguna selain mahasiswa tidak dapat mengakses CRUD CV', function () {
    $dosen = Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'peran' => 'dosen',
        'id_prodi' => 'TI',
    ]);

    $this->actingAs($dosen)
        ->get(route('mahasiswa.pendidikan.index'))
        ->assertForbidden();
});

test('seluruh halaman mahasiswa dapat dirender', function () {
    $mahasiswa = mahasiswaPengguna();

    $halaman = [
        'mahasiswa.profil.index' => 'Profil CV',
        'mahasiswa.pendidikan.index' => 'Pendidikan',
        'mahasiswa.pengalaman.index' => 'Manajemen Pengalaman',
        'mahasiswa.sertifikasi.index' => 'Manajemen Sertifikasi',
        'mahasiswa.keahlian.index' => 'Keahlian',
        'mahasiswa.preview.index' => 'Preview CV ATS',
    ];

    foreach ($halaman as $routeName => $judul) {
        $this->actingAs($mahasiswa)
            ->get(route($routeName))
            ->assertOk()
            ->assertSee($judul);
    }
});

test('sidebar mahasiswa memakai tautan route yang valid', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->get(route('dashboard.mahasiswa'))
        ->assertOk()
        ->assertSee(route('dashboard.mahasiswa'), false)
        ->assertSee(route('mahasiswa.profil.index'), false)
        ->assertSee(route('mahasiswa.pendidikan.index'), false)
        ->assertSee(route('mahasiswa.pengalaman.index'), false)
        ->assertSee(route('mahasiswa.sertifikasi.index'), false)
        ->assertSee(route('mahasiswa.keahlian.index'), false)
        ->assertSee(route('mahasiswa.preview.index'), false)
        ->assertDontSee('href="#"', false);
});

test('mahasiswa dapat mengelola pengalaman miliknya', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.pengalaman.store'), [
            'jenis' => 'Magang',
            'posisi' => 'Backend Developer',
            'nama_institusi' => 'PT Teknologi Nusantara',
            'deskripsi' => 'Membangun API internal.',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-06-30',
        ])
        ->assertRedirect(route('mahasiswa.pengalaman.index'))
        ->assertSessionHas('status');

    $pengalaman = Pengalaman::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($pengalaman->sumber_data)->toBe('Manual')
        ->and($pengalaman->is_verified)->toBeFalse();

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.pengalaman.update', $pengalaman), [
            'jenis' => 'Kerja',
            'posisi' => 'Full Stack Developer',
            'nama_institusi' => 'PT Teknologi Nusantara',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => null,
        ])
        ->assertRedirect(route('mahasiswa.pengalaman.index'));

    $this->assertDatabaseHas('cv_pengalaman', [
        'id' => $pengalaman->id,
        'jenis' => 'Kerja',
        'posisi' => 'Full Stack Developer',
        'sumber_data' => 'Manual',
    ]);

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.pengalaman.destroy', $pengalaman))
        ->assertRedirect(route('mahasiswa.pengalaman.index'));

    $this->assertDatabaseMissing('cv_pengalaman', ['id' => $pengalaman->id]);
});

test('validasi pengalaman menolak data yang tidak valid', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.pengalaman.store'), [
            'jenis' => 'Tidak Ada',
            'posisi' => '',
            'nama_institusi' => '',
            'tanggal_mulai' => '',
            'tanggal_selesai' => '2020-01-01',
        ])
        ->assertSessionHasErrors(['jenis', 'posisi', 'nama_institusi', 'tanggal_mulai']);

    $this->assertDatabaseCount('cv_pengalaman', 0);
});

test('pengalaman dari sistem kampus tidak dapat diubah atau dihapus', function () {
    $mahasiswa = mahasiswaPengguna();

    $pengalaman = Pengalaman::create([
        'pengguna_id' => $mahasiswa->id,
        'jenis' => 'Magang',
        'posisi' => 'Data Analyst',
        'nama_institusi' => 'PT Contoh',
        'tanggal_mulai' => '2023-07-01',
        'sumber_data' => 'MO-KEL',
        'is_verified' => true,
    ]);

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.pengalaman.index'))
        ->assertOk()
        ->assertSee('Terverifikasi Sistem')
        ->assertSee('Terkunci')
        ->assertDontSee(route('mahasiswa.pengalaman.destroy', $pengalaman), false);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.pengalaman.update', $pengalaman), [
            'jenis' => 'Kerja',
            'posisi' => 'Diubah',
            'nama_institusi' => 'Institusi Lain',
            'tanggal_mulai' => '2023-07-01',
        ])
        ->assertForbidden();

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.pengalaman.destroy', $pengalaman))
        ->assertForbidden();

    $this->assertDatabaseHas('cv_pengalaman', [
        'id' => $pengalaman->id,
        'posisi' => 'Data Analyst',
        'sumber_data' => 'MO-KEL',
        'is_verified' => true,
    ]);
});

test('mahasiswa dapat mengelola sertifikasi miliknya', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.sertifikasi.store'), [
            'nama_sertifikasi' => 'Junior Web Developer',
            'penerbit' => 'BNSP',
            'nomor_sertifikat' => '1234/SERT/2024',
            'tanggal_terbit' => '2024-02-01',
            'tanggal_kedaluwarsa' => '2027-02-01',
        ])
        ->assertRedirect(route('mahasiswa.sertifikasi.index'))
        ->assertSessionHas('status');

    $sertifikasi = Sertifikasi::whereBelongsTo($mahasiswa)->firstOrFail();

    expect($sertifikasi->sumber_data)->toBe('Manual')
        ->and($sertifikasi->is_verified)->toBeFalse();

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.sertifikasi.update', $sertifikasi), [
            'nama_sertifikasi' => 'Junior Web Developer',
            'penerbit' => 'BNSP',
            'nomor_sertifikat' => '5678/SERT/2024',
            'tanggal_terbit' => '2024-02-01',
            'tanggal_kedaluwarsa' => null,
        ])
        ->assertRedirect(route('mahasiswa.sertifikasi.index'));

    $this->assertDatabaseHas('cv_sertifikasi', [
        'id' => $sertifikasi->id,
        'nomor_sertifikat' => '5678/SERT/2024',
        'sumber_data' => 'Manual',
    ]);

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.sertifikasi.destroy', $sertifikasi))
        ->assertRedirect(route('mahasiswa.sertifikasi.index'));

    $this->assertDatabaseMissing('cv_sertifikasi', ['id' => $sertifikasi->id]);
});

test('validasi sertifikasi menolak data yang tidak valid', function () {
    $mahasiswa = mahasiswaPengguna();

    $this->actingAs($mahasiswa)
        ->post(route('mahasiswa.sertifikasi.store'), [
            'nama_sertifikasi' => '',
            'penerbit' => '',
            'tanggal_terbit' => '',
            'tanggal_kedaluwarsa' => '2020-01-01',
        ])
        ->assertSessionHasErrors(['nama_sertifikasi', 'penerbit', 'tanggal_terbit']);

    $this->assertDatabaseCount('cv_sertifikasi', 0);
});

test('sertifikasi dari sistem kampus tidak dapat diubah atau dihapus', function () {
    $mahasiswa = mahasiswaPengguna();

    $sertifikasi = Sertifikasi::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_sertifikasi' => 'Sertifikat Kompetensi',
        'penerbit' => 'UKom',
        'tanggal_terbit' => '2024-03-01',
        'sumber_data' => 'SKPI',
        'is_verified' => true,
    ]);

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.sertifikasi.index'))
        ->assertOk()
        ->assertSee('Terverifikasi Sistem')
        ->assertSee('Terkunci')
        ->assertDontSee(route('mahasiswa.sertifikasi.destroy', $sertifikasi), false);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.sertifikasi.update', $sertifikasi), [
            'nama_sertifikasi' => 'Diubah',
            'penerbit' => 'Penerbit Lain',
            'tanggal_terbit' => '2024-03-01',
        ])
        ->assertForbidden();

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.sertifikasi.destroy', $sertifikasi))
        ->assertForbidden();

    $this->assertDatabaseHas('cv_sertifikasi', [
        'id' => $sertifikasi->id,
        'nama_sertifikasi' => 'Sertifikat Kompetensi',
        'sumber_data' => 'SKPI',
        'is_verified' => true,
    ]);
});

test('keahlian dari sistem kampus tidak dapat diubah atau dihapus', function () {
    $mahasiswa = mahasiswaPengguna();

    $keahlian = Keahlian::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_keahlian' => 'Jaringan Komputer',
        'tingkat' => 'Mahir',
        'sumber_data' => 'UKom',
        'is_verified' => true,
    ]);

    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.keahlian.index'))
        ->assertOk()
        ->assertSee('Terverifikasi Sistem')
        ->assertSee('Terkunci')
        ->assertDontSee(route('mahasiswa.keahlian.destroy', $keahlian), false);

    $this->actingAs($mahasiswa)
        ->put(route('mahasiswa.keahlian.update', $keahlian), [
            'nama_keahlian' => 'Diubah',
            'tingkat' => 'Pemula',
        ])
        ->assertForbidden();

    $this->actingAs($mahasiswa)
        ->delete(route('mahasiswa.keahlian.destroy', $keahlian))
        ->assertForbidden();

    $this->assertDatabaseHas('cv_keahlian', [
        'id' => $keahlian->id,
        'nama_keahlian' => 'Jaringan Komputer',
        'sumber_data' => 'UKom',
        'is_verified' => true,
    ]);
});
