<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Sertifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SertifikasiController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.sertifikasi.index', [
            'sertifikasi' => Auth::user()->sertifikasi()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Sertifikasi::create([
            ...$validated,
            'pengguna_id' => Auth::id(),
            'sumber_data' => 'Manual',
            'is_verified' => false,
        ]);

        return redirect()
            ->route('mahasiswa.sertifikasi.index')
            ->with('status', 'Data sertifikasi berhasil ditambahkan.');
    }

    public function update(Request $request, Sertifikasi $sertifikasi): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($sertifikasi);
        $this->ensureManual($sertifikasi);

        $sertifikasi->update($request->validate($this->rules()));

        return redirect()
            ->route('mahasiswa.sertifikasi.index')
            ->with('status', 'Data sertifikasi berhasil diperbarui.');
    }

    public function destroy(Sertifikasi $sertifikasi): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($sertifikasi);
        $this->ensureManual($sertifikasi);

        $sertifikasi->delete();

        return redirect()
            ->route('mahasiswa.sertifikasi.index')
            ->with('status', 'Data sertifikasi berhasil dihapus.');
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'nama_sertifikasi' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_kedaluwarsa' => ['nullable', 'date', 'after_or_equal:tanggal_terbit'],
        ];
    }

    private function ensureOwnedByCurrentUser(Sertifikasi $sertifikasi): void
    {
        abort_unless($sertifikasi->pengguna_id === Auth::id(), 404);
    }

    private function ensureManual(Sertifikasi $sertifikasi): void
    {
        abort_if(
            $sertifikasi->sumber_data !== 'Manual',
            403,
            'Data resmi dari sistem kampus tidak dapat diubah secara manual.',
        );
    }
}
