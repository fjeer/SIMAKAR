<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function mahasiswa(): View
    {
        $pengguna = Auth::user();

        $pengguna->loadCount(['pendidikan', 'pengalaman', 'sertifikasi', 'keahlian']);

        return view('dashboard.mahasiswa', [
            'totalPendidikan' => $pengguna->pendidikan_count,
            'totalPengalaman' => $pengguna->pengalaman_count,
            'totalSertifikasi' => $pengguna->sertifikasi_count,
            'totalKeahlian' => $pengguna->keahlian_count,
        ]);
    }

    public function dosen(): View
    {
        return view('dashboard.dosen');
    }

    public function kaprodi(): View
    {
        return view('dashboard.kaprodi');
    }

    public function dekan(): View
    {
        return view('dashboard.dekan');
    }

    public function admin(): View
    {
        return view('dashboard.admin');
    }
}
