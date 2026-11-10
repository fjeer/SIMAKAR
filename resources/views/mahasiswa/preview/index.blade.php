@extends('layouts.app')

@section('title', 'Preview ATS - SIMAKAR')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Profil & CV</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Preview CV ATS</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Periksa isi CV Anda, lalu unduh dalam format PDF yang ramah Applicant Tracking System (ATS).
            </p>
        </div>

        {{-- CTA --}}
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 px-6 py-7 text-white shadow-xl shadow-indigo-600/20 sm:px-8">
            <div class="pointer-events-none absolute -right-12 -top-16 size-48 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl">
                    <h2 class="text-lg font-semibold sm:text-xl">Siap mengirim lamaran?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-indigo-100/90">
                        Unduh CV Anda dalam format PDF ATS friendly. Semua data diambil langsung dari profil yang sudah
                        Anda lengkapi.
                    </p>
                </div>

                <a
                    href="{{ route('mahasiswa.preview.download') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-indigo-950/20 transition hover:bg-indigo-50 focus:outline-none focus:ring-4 focus:ring-white/30 sm:text-base"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download CV (PDF ATS Friendly)
                </a>
            </div>
        </section>

        {{-- Live preview --}}
        <section aria-labelledby="pratinjau-cv" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="pratinjau-cv" class="text-base font-semibold text-gray-900 dark:text-white">
                    Pratinjau CV
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Tampilan A4 · Font standar · Tanpa elemen dekoratif
                </p>
            </div>

            <div class="mx-auto w-full max-w-[210mm] overflow-hidden rounded-xl bg-white p-3 shadow-xl ring-1 ring-gray-900/10 sm:p-6 dark:ring-white/10">
                <iframe
                    src="{{ route('mahasiswa.preview.cv') }}"
                    title="Pratinjau CV ATS {{ $user->nama }}"
                    class="h-[1123px] w-full border-0 bg-white"
                ></iframe>
            </div>
        </section>
    </div>
@endsection
