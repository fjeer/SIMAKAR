<?php

namespace App\Http\Controllers;

use App\Exceptions\SsoAuthenticationException;
use App\Http\Requests\LoginRequest;
use App\Models\Pengguna;
use App\Services\SsoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request, SsoService $ssoService): RedirectResponse
    {
        $nimNidn = $request->string('nim_nidn')->trim()->toString();

        try {
            $profile = $ssoService->getProfile($nimNidn);
        } catch (SsoAuthenticationException $exception) {
            Log::error('Login SSO gagal.', [
                'nim_nidn' => $nimNidn,
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withErrors(['nim_nidn' => $exception->getMessage()])
                ->withInput();
        }

        $attributes = $this->profileAttributes($profile);

        Log::info('Evaluasi Role SSO', [
            'id_penduduk' => data_get($profile, 'id_penduduk'),
            'hasil_peran' => $attributes['peran'],
            'data_karyawan_ditemukan' => data_get($profile, 'data_karyawan'),
        ]);

        $pengguna = Pengguna::updateOrCreate(
            ['sso_id' => $nimNidn],
            $attributes,
        );

        Auth::login($pengguna);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return array{nama: string, peran: string, id_prodi: string}
     */
    private function profileAttributes(array $profile): array
    {
        return [
            'nama' => $this->profileValue($profile, ['nama', 'nama_penduduk', 'name', 'nama_lengkap'], 'Pengguna'),
            'peran' => $this->resolvePeran($profile),
            'id_prodi' => $this->profileValue($profile, ['id_sms', 'id_prodi', 'kode_prodi', 'prodi.id', 'prodi.kode', 'prodi'], '-'),
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private function resolvePeran(array $profile): string
    {
        if (Str::lower($this->profileValue($profile, ['peran', 'role'], '')) === 'mahasiswa') {
            return 'mahasiswa';
        }

        $struktur = Str::lower($this->profileValue($profile, ['struktur'], ''));

        if (Str::contains($struktur, 'dekan')) {
            return 'dekan';
        }

        if (Str::contains($struktur, ['kaprodi', 'ketua prodi'])) {
            return 'kaprodi';
        }

        return 'dosen';
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  list<string>  $keys
     */
    private function profileValue(array $profile, array $keys, string $default): string
    {
        foreach ($keys as $key) {
            $value = data_get($profile, $key);

            if (is_scalar($value) && filled($value)) {
                return (string) $value;
            }
        }

        return $default;
    }
}
