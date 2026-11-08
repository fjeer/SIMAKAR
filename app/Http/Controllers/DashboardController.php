<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function mahasiswa(): View
    {
        return view('dashboard.mahasiswa');
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
