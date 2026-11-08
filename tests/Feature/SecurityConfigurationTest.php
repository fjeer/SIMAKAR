<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

uses(RefreshDatabase::class);

function makeSecurityPengguna(string $peran): Pengguna
{
    return Pengguna::create([
        'sso_id' => 'user-'.$peran,
        'nama' => 'Pengguna '.$peran,
        'peran' => $peran,
        'id_prodi' => 'prodi-1',
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

test('each role only passes its own gate', function (string $peran) {
    $pengguna = makeSecurityPengguna($peran);

    $gates = ['admin', 'dekan', 'kaprodi', 'dosen', 'mahasiswa'];

    foreach ($gates as $gate) {
        expect(Gate::forUser($pengguna)->allows($gate))->toBe($gate === $peran);
    }
})->with(['admin', 'dekan', 'kaprodi', 'dosen', 'mahasiswa']);

test('an idle authenticated session is logged out', function () {
    $pengguna = makeSecurityPengguna('mahasiswa');

    $this->actingAs($pengguna)
        ->withSession(['last_activity' => now()->subMinutes(16)])
        ->get('/mahasiswa/dashboard')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('idle');

    expect(session('errors')->first('idle'))->toBe('Sesi berakhir karena tidak ada aktivitas.');
    $this->assertGuest();
});

test('login page displays the idle session error', function () {
    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'idle' => 'Sesi berakhir karena tidak ada aktivitas.',
    ]));

    $this->view('auth.login', ['errors' => $errors])
        ->assertSee('Sesi berakhir karena tidak ada aktivitas.');
});

test('an active authenticated session is refreshed', function () {
    $pengguna = makeSecurityPengguna('mahasiswa');

    $this->actingAs($pengguna)
        ->withSession(['last_activity' => now()->subMinutes(5)])
        ->get('/mahasiswa/dashboard')
        ->assertOk();

    expect(session('last_activity'))->not->toBeNull();
    expect(now()->diffInSeconds(session('last_activity')))->toBeLessThan(5);
});

test('guest requests are not affected by the idle timeout', function () {
    $this->withSession(['last_activity' => now()->subMinutes(16)])
        ->get('/login')
        ->assertOk();
});

test('throttled login returns a clean error message', function () {
    Http::fake([
        'sso.unuja.ac.id/portal/data/authorize' => Http::response(['success' => false], 500),
    ]);

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->from('/login')
            ->post('/login', ['nim_nidn' => '0000000000'])
            ->assertSessionHasErrors('nim_nidn');
    }

    $this->from('/login')
        ->post('/login', ['nim_nidn' => '0000000000'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('nim_nidn');

    expect(session('errors')->first('nim_nidn'))
        ->toContain('Terlalu banyak percobaan login');
});
