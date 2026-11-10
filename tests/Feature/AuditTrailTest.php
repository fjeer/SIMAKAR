<?php

use App\Models\Keahlian;
use App\Models\Pendidikan;
use App\Models\Pengalaman;
use App\Models\Pengguna;
use App\Models\ProfilPengguna;
use App\Models\Sertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

uses(RefreshDatabase::class);

function auditPengguna(): Pengguna
{
    return Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => 'Fahmi Bahrul Widad',
        'email' => 'fahmi@unuja.ac.id',
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
    ]);
}

test('model penyusun cv menggunakan trait logs activity', function (string $modelClass) {
    expect(class_uses_recursive($modelClass))->toContain(LogsActivity::class);
})->with([
    ProfilPengguna::class,
    Pendidikan::class,
    Pengalaman::class,
    Sertifikasi::class,
    Keahlian::class,
]);

test('pembuatan dan perubahan pendidikan tercatat di activity log', function () {
    $mahasiswa = auditPengguna();
    $this->actingAs($mahasiswa);

    $pendidikan = Pendidikan::create([
        'pengguna_id' => $mahasiswa->id,
        'jenjang' => 'S1',
        'nama_institusi' => 'Universitas Nurul Jadid',
        'tahun_mulai' => '2022',
        'sumber_data' => 'Manual',
        'is_verified' => false,
    ]);

    $created = Activity::query()->where('event', 'created')->latest('id')->firstOrFail();

    expect($created->description)->toBe('Data Pendidikan telah di-created')
        ->and($created->subject_type)->toBe(Pendidikan::class)
        ->and($created->subject_id)->toBe($pendidikan->id)
        ->and($created->causer_type)->toBe($mahasiswa->getMorphClass())
        ->and($created->causer_id)->toBe($mahasiswa->id)
        ->and($created->attribute_changes['attributes']['nama_institusi'])->toBe('Universitas Nurul Jadid');

    $pendidikan->update(['nama_institusi' => 'Universitas Nurul Jadid Kampus 2']);

    $updated = Activity::query()->where('event', 'updated')->latest('id')->firstOrFail();

    expect($updated->description)->toBe('Data Pendidikan telah di-updated')
        ->and($updated->attribute_changes['attributes'])
        ->toBe(['nama_institusi' => 'Universitas Nurul Jadid Kampus 2'])
        ->and($updated->attribute_changes['old']['nama_institusi'])->toBe('Universitas Nurul Jadid');
});

test('menyimpan data tanpa perubahan tidak menambah activity log', function () {
    $mahasiswa = auditPengguna();
    $this->actingAs($mahasiswa);

    $keahlian = Keahlian::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_keahlian' => 'Laravel',
        'tingkat' => 'Mahir',
    ]);

    $jumlahLog = Activity::query()->count();

    $keahlian->save();

    expect(Activity::query()->count())->toBe($jumlahLog);
});

test('penghapusan data cv tercatat di activity log', function () {
    $mahasiswa = auditPengguna();
    $this->actingAs($mahasiswa);

    $sertifikasi = Sertifikasi::create([
        'pengguna_id' => $mahasiswa->id,
        'nama_sertifikasi' => 'Junior Web Developer',
        'penerbit' => 'BNSP',
        'tanggal_terbit' => '2024-02-01',
    ]);

    $sertifikasi->delete();

    $deleted = Activity::query()->where('event', 'deleted')->latest('id')->firstOrFail();

    expect($deleted->description)->toBe('Data Sertifikasi telah di-deleted')
        ->and($deleted->subject_type)->toBe(Sertifikasi::class)
        ->and($deleted->subject_id)->toBe($sertifikasi->id);
});
