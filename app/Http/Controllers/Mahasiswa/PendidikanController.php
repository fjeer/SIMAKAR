<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PendidikanController extends Controller
{
    /**
     * @var list<string>
     */
    private const JENJANG_OPTIONS = ['SD', 'SMP', 'SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3'];

    public function index(): View
    {
        return view('mahasiswa.pendidikan.index', [
            'pendidikan' => Auth::user()->pendidikan()->latest()->get(),
            'jenjangOptions' => self::JENJANG_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Pendidikan::create([
            ...$validated,
            'pengguna_id' => Auth::id(),
            'sumber_data' => 'Manual',
            'is_verified' => false,
        ]);

        return redirect()
            ->route('mahasiswa.pendidikan.index')
            ->with('status', 'Data pendidikan berhasil ditambahkan.');
    }

    public function update(Request $request, Pendidikan $pendidikan): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($pendidikan);
        $this->ensureEditable($pendidikan);

        $pendidikan->update($request->validate($this->rules()));

        return redirect()
            ->route('mahasiswa.pendidikan.index')
            ->with('status', 'Data pendidikan berhasil diperbarui.');
    }

    public function destroy(Pendidikan $pendidikan): RedirectResponse
    {
        $this->ensureOwnedByCurrentUser($pendidikan);
        $this->ensureEditable($pendidikan);

        $pendidikan->delete();

        return redirect()
            ->route('mahasiswa.pendidikan.index')
            ->with('status', 'Data pendidikan berhasil dihapus.');
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        $latestYear = now()->year + 1;

        return [
            'jenjang' => ['required', Rule::in(self::JENJANG_OPTIONS)],
            'nama_institusi' => ['required', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'tahun_mulai' => ['required', 'integer', 'between:1900,'.$latestYear],
            'tahun_selesai' => ['nullable', 'integer', 'between:1900,'.$latestYear, 'gte:tahun_mulai'],
            'nilai_akhir' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    private function ensureOwnedByCurrentUser(Pendidikan $pendidikan): void
    {
        abort_unless($pendidikan->pengguna_id === Auth::id(), 404);
    }

    private function ensureEditable(Pendidikan $pendidikan): void
    {
        abort_if($pendidikan->sumber_data === 'SSO', 403, 'Data pendidikan dari SSO tidak dapat diubah.');
    }
}
