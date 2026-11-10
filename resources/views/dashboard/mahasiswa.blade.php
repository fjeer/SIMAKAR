@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIMAKAR')

@section('content')
    <div class="space-y-6">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 px-6 py-8 text-white shadow-xl shadow-indigo-600/20 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-24 size-64 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-24 right-24 size-48 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>

            <div class="relative max-w-2xl">
                <p class="text-sm font-medium text-indigo-200">Ruang Kerja Mahasiswa</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                    Selamat Datang, {{ Auth::user()->nama }}
                </h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-indigo-100/90 sm:text-base">
                    Lengkapi data pendidikan, pengalaman, sertifikasi, dan keahlian Anda untuk menghasilkan CV
                    berstandar ATS yang siap digunakan melamar kerja.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('mahasiswa.profil.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg shadow-indigo-950/20 transition hover:bg-indigo-50 focus:outline-none focus:ring-4 focus:ring-white/30"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Lengkapi Profil
                    </a>

                    <a
                        href="{{ route('mahasiswa.preview.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/25 transition hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/30"
                    >
                        Preview ATS
                    </a>
                </div>
            </div>
        </section>

        {{-- Quick stats --}}
        @php
            $statistik = [
                [
                    'label' => 'Total Pendidikan',
                    'nilai' => $totalPendidikan,
                    'route' => 'mahasiswa.pendidikan.index',
                    'icon' => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342',
                    'icon_class' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400',
                ],
                [
                    'label' => 'Total Pengalaman',
                    'nilai' => $totalPengalaman,
                    'route' => 'mahasiswa.pengalaman.index',
                    'icon' => 'M20.25 14.15v4.098a2.25 2.25 0 0 1-2.25 2.25h-12a2.25 2.25 0 0 1-2.25-2.25V14.15M16.5 6.75V6a2.25 2.25 0 0 0-2.25-2.25h-4.5A2.25 2.25 0 0 0 7.5 6v.75m9 0h-9m9 0h1.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 3.75 10.5V9A2.25 2.25 0 0 1 6 6.75h1.5',
                    'icon_class' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400',
                ],
                [
                    'label' => 'Total Sertifikasi',
                    'nilai' => $totalSertifikasi,
                    'route' => 'mahasiswa.sertifikasi.index',
                    'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'icon_class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                ],
                [
                    'label' => 'Total Keahlian',
                    'nilai' => $totalKeahlian,
                    'route' => 'mahasiswa.keahlian.index',
                    'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
                    'icon_class' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                ],
            ];
        @endphp

        <section aria-labelledby="statistik-ringkas">
            <h2 id="statistik-ringkas" class="sr-only">Statistik ringkas</h2>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($statistik as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-900 dark:ring-white/10"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $item['label'] }}</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    {{ $item['nilai'] }}
                                </p>
                            </div>

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $item['icon_class'] }}">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                            </span>
                        </div>

                        <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 transition group-hover:gap-2 dark:text-indigo-400">
                            Kelola data
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Panduan singkat --}}
        <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 dark:border-white/15 dark:bg-gray-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Siap membuat CV ATS?</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Data pendidikan dari SSO sudah terverifikasi. Lengkapi pengalaman, sertifikasi, dan keahlian
                        untuk hasil CV yang lebih maksimal.
                    </p>
                </div>

                <a
                    href="{{ route('mahasiswa.pengalaman.index') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-500/20 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200"
                >
                    Tambah Pengalaman
                </a>
            </div>
        </section>
    </div>
@endsection
