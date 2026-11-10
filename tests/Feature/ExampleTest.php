<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tamu diarahkan ke halaman login saat membuka root', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('pengguna yang sudah login diarahkan ke dashboard sesuai perannya', function () {
    $pengguna = Pengguna::create([
        'sso_id' => fake()->unique()->uuid(),
        'nama' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'peran' => 'mahasiswa',
        'id_prodi' => 'TI',
    ]);

    $this->actingAs($pengguna)
        ->get('/')
        ->assertRedirect(route('dashboard.mahasiswa'));
});
