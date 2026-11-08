@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIMAKAR')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Ruang Kerja Mahasiswa</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Selamat datang, {{ auth()->user()->nama }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Lengkapi riwayat pendidikan, pengalaman, sertifikasi, dan keahlian untuk CV Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-800"
                >
                    Keluar
                </button>
            </form>
        </div>
    </div>
@endsection
