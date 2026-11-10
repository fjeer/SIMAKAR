<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Keahlian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KeahlianController extends Controller
{
    /**
     * @var list<string>
     */
    private const TINGKAT_OPTIONS = ['Pemula', 'Menengah', 'Mahir', 'Ahli'];

    public function index(): View
    {
        return view('mahasiswa.keahlian.index', [
            'keahlian' => Auth::user()->keahlian()->latest()->get(),
            'tingkatOptions' => self::TINGKAT_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Keahlian::create([
            ...$validated,
            'pengguna_id' => Auth::id(),
            'sumber_data' => 'Manual',
            'is_verified' => false,
        ]);

        return redirect()
            ->route('mahasiswa.keahlian.index')
            ->with('status', 'Data keahlian berhasil ditambahkan.');
    }

    public function update(Request $request, Keahlian $keahlian): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($keahlian);
        $this->ensureManual($keahlian);

        $keahlian->update($request->validate($this->rules()));

        return redirect()
            ->route('mahasiswa.keahlian.index')
            ->with('status', 'Data keahlian berhasil diperbarui.');
    }

    public function destroy(Keahlian $keahlian): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($keahlian);
        $this->ensureManual($keahlian);

        $keahlian->delete();

        return redirect()
            ->route('mahasiswa.keahlian.index')
            ->with('status', 'Data keahlian berhasil dihapus.');
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'nama_keahlian' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', Rule::in(self::TINGKAT_OPTIONS)],
        ];
    }

    private function ensureOwnedByCurrentUser(Keahlian $keahlian): void
    {
        abort_unless($keahlian->pengguna_id === Auth::id(), 404);
    }

    private function ensureManual(Keahlian $keahlian): void
    {
        abort_if(
            $keahlian->sumber_data !== 'Manual',
            403,
            'Data resmi dari sistem kampus tidak dapat diubah secara manual.',
        );
    }
}
