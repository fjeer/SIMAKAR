@extends('layouts.app')

@section('title', 'Profil CV - SIMAKAR')

@section('content')
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
        $readonlyClass = 'block w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-500 shadow-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-400';
        $labelClass = 'block space-y-2 text-sm font-medium text-gray-700 dark:text-gray-200';
    @endphp

    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Profil & CV</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Profil CV</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Data identitas berasal dari SSO. Lengkapi detail profil yang akan tampil pada CV Anda.
            </p>
        </div>

        @if (session('status'))
            <div role="status" class="flex items-start gap-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30">
                <svg class="mt-0.5 size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300 dark:ring-red-400/30">
                <p class="font-semibold">Periksa kembali data yang diisi.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('mahasiswa.profil.update') }}"
            class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10"
        >
            @csrf
            @method('PUT')

            <div class="space-y-8 p-6 sm:p-8">
                {{-- Identitas SSO --}}
                <section aria-labelledby="identitas-sso">
                    <div class="flex items-start gap-3 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-800 ring-1 ring-indigo-600/10 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-400/20">
                        <svg class="mt-0.5 size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <p>Nama, email, dan program studi terkunci karena tersinkronisasi otomatis dari SSO kampus.</p>
                    </div>

                    <h2 id="identitas-sso" class="mt-6 text-base font-semibold text-gray-900 dark:text-white">Identitas SSO</h2>

                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <label class="{{ $labelClass }}">
                            <span>Nama Lengkap</span>
                            <input type="text" value="{{ Auth::user()->nama }}" class="{{ $readonlyClass }}" readonly>
                        </label>

                        <label class="{{ $labelClass }}">
                            <span>Email</span>
                            <input type="email" value="{{ Auth::user()->email }}" class="{{ $readonlyClass }}" readonly>
                        </label>

                        <label class="{{ $labelClass }}">
                            <span>Program Studi</span>
                            <input
                                type="text"
                                value="{{ $profil?->nama_prodi ?? Auth::user()->id_prodi }}"
                                class="{{ $readonlyClass }}"
                                readonly
                            >
                        </label>
                    </div>
                </section>

                <hr class="border-gray-200 dark:border-white/10">

                {{-- Detail yang dapat diedit --}}
                <section aria-labelledby="detail-profil">
                    <h2 id="detail-profil" class="text-base font-semibold text-gray-900 dark:text-white">Detail Profil</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Informasi ini digunakan untuk melengkapi CV ATS Anda.
                    </p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="{{ $labelClass }}">
                            <span>Tempat Lahir</span>
                            <input
                                type="text"
                                name="tempat_lahir"
                                value="{{ old('tempat_lahir', $profil?->tempat_lahir) }}"
                                placeholder="Contoh: Probolinggo"
                                maxlength="255"
                                class="{{ $inputClass }}"
                            >
                        </label>

                        <label class="{{ $labelClass }}">
                            <span>Tanggal Lahir</span>
                            <input
                                type="date"
                                name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $profil?->tanggal_lahir?->format('Y-m-d')) }}"
                                max="{{ now()->format('Y-m-d') }}"
                                class="{{ $inputClass }}"
                            >
                        </label>

                        <label class="{{ $labelClass }} sm:col-span-2">
                            <span>Alamat Domisili</span>
                            <textarea
                                name="alamat"
                                rows="3"
                                maxlength="1000"
                                placeholder="Contoh: Jl. Raya Panglima Sudirman, Probolinggo"
                                class="{{ $inputClass }}"
                            >{{ old('alamat', $profil?->alamat) }}</textarea>
                        </label>

                        <label class="{{ $labelClass }} sm:col-span-2">
                            <span>Bio / Tentang Saya</span>
                            <textarea
                                name="bio"
                                rows="5"
                                maxlength="2000"
                                placeholder="Ceritakan singkat tentang diri, minat, dan tujuan karier Anda."
                                class="{{ $inputClass }}"
                            >{{ old('bio', $profil?->bio) }}</textarea>
                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Maksimal 2000 karakter.</span>
                        </label>
                    </div>
                </section>
            </div>

            <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 sm:px-8 dark:border-white/10 dark:bg-white/5">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
@endsection
