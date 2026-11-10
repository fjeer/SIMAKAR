<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PreviewController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.preview.index', [
            'user' => $this->userWithCvData(),
        ]);
    }

    /**
     * Render the CV ATS template as a standalone HTML page for live preview.
     */
    public function cv(): View
    {
        return view('mahasiswa.preview.cv_ats', [
            'user' => $this->userWithCvData(),
        ]);
    }

    public function download(): Response
    {
        $user = $this->userWithCvData();

        $pdf = Pdf::loadView('mahasiswa.preview.cv_ats', compact('user'));

        return $pdf->download('CV_ATS_'.$user->nama.'.pdf');
    }

    /**
     * Load the authenticated mahasiswa with every CV relation, newest first.
     */
    private function userWithCvData(): Pengguna
    {
        /** @var Pengguna $user */
        $user = Auth::user();

        return $user->load([
            'profil',
            'pendidikan' => fn ($query) => $query->orderByDesc('tahun_mulai'),
            'pengalaman' => fn ($query) => $query->orderByDesc('tanggal_mulai'),
            'sertifikasi' => fn ($query) => $query->orderByDesc('tanggal_terbit'),
            'keahlian' => fn ($query) => $query->orderBy('nama_keahlian'),
        ]);
    }
}
