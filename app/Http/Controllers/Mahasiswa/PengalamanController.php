<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pengalaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengalamanController extends Controller
{
    /**
     * @var list<string>
     */
    private const JENIS_OPTIONS = ['Kerja', 'Organisasi', 'Kepanitiaan', 'Magang'];

    public function index(): View
    {
        return view('mahasiswa.pengalaman.index', [
            'pengalaman' => Auth::user()->pengalaman()->latest()->get(),
            'jenisOptions' => self::JENIS_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Pengalaman::create([
            ...$validated,
            'pengguna_id' => Auth::id(),
            'sumber_data' => 'Manual',
            'is_verified' => false,
        ]);

        return redirect()
            ->route('mahasiswa.pengalaman.index')
            ->with('status', 'Data pengalaman berhasil ditambahkan.');
    }

    public function update(Request $request, Pengalaman $pengalaman): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($pengalaman);
        $this->ensureManual($pengalaman);

        $pengalaman->update($request->validate($this->rules()));

        return redirect()
            ->route('mahasiswa.pengalaman.index')
            ->with('status', 'Data pengalaman berhasil diperbarui.');
    }

    public function destroy(Pengalaman $pengalaman): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($pengalaman);
        $this->ensureManual($pengalaman);

        $pengalaman->delete();

        return redirect()
            ->route('mahasiswa.pengalaman.index')
            ->with('status', 'Data pengalaman berhasil dihapus.');
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(self::JENIS_OPTIONS)],
            'posisi' => ['required', 'string', 'max:255'],
            'nama_institusi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ];
    }

    private function ensureOwnedByCurrentUser(Pengalaman $pengalaman): void
    {
        abort_unless($pengalaman->pengguna_id === Auth::id(), 404);
    }

    private function ensureManual(Pengalaman $pengalaman): void
    {
        abort_if(
            $pengalaman->sumber_data !== 'Manual',
            403,
            'Data resmi dari sistem kampus tidak dapat diubah secara manual.',
        );
    }
}
