<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.profil.index', [
            'profil' => Auth::user()->profil,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        Auth::user()->profil()->updateOrCreate(
            ['pengguna_id' => Auth::id()],
            $validated,
        );

        return redirect()
            ->route('mahasiswa.profil.index')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}
