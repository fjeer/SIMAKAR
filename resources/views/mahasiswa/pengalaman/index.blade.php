@extends('layouts.app')

@section('title', 'Pengalaman - SIMAKAR')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Profil & CV</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Manajemen Pengalaman</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola riwayat magang, organisasi, dan pengalaman kerja yang relevan.
                </p>
            </div>

            <button
                type="button"
                data-dialog-open="pengalaman-create"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Pengalaman
            </button>
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

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jenis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Posisi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Institusi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Periode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sumber</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($pengalaman as $item)
                            <tr class="transition hover:bg-gray-50/70 dark:hover:bg-white/5">
                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                        $jenisClasses = match ($item->jenis) {
                                            'Kerja' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-400/30',
                                            'Organisasi' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-400/30',
                                            'Kepanitiaan' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/30',
                                            'Magang' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30',
                                            default => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-white/10 dark:text-gray-200 dark:ring-white/20',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $jenisClasses }}">
                                        {{ $item->jenis }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->posisi }}</p>
                                    @if ($item->deskripsi)
                                        <p class="mt-1 line-clamp-2 max-w-xs text-xs text-gray-500 dark:text-gray-400">
                                            {{ $item->deskripsi }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $item->nama_institusi }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $item->tanggal_mulai?->format('d/m/Y') }}
                                    –
                                    {{ $item->tanggal_selesai?->format('d/m/Y') ?: 'Sekarang' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($item->sumber_data === 'Manual')
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-600/20 dark:bg-white/10 dark:text-gray-300 dark:ring-white/20">
                                            Manual
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Terverifikasi Sistem
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($item->sumber_data !== 'Manual')
                                        <div class="flex items-center justify-end">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                Terkunci
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                data-dialog-open="pengalaman-edit-{{ $item->id }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:bg-gray-950 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-800"
                                            >
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                                Edit
                                            </button>

                                            <form
                                                method="POST"
                                                action="{{ route('mahasiswa.pengalaman.destroy', $item) }}"
                                                onsubmit="return confirm('Hapus data pengalaman ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-gray-300 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-500/20 dark:bg-gray-950 dark:text-red-400 dark:ring-white/10 dark:hover:bg-red-500/10"
                                                >
                                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.2v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500">
                                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.098a2.25 2.25 0 0 1-2.25 2.25h-12a2.25 2.25 0 0 1-2.25-2.25V14.15M16.5 6.75V6a2.25 2.25 0 0 0-2.25-2.25h-4.5A2.25 2.25 0 0 0 7.5 6v.75m9 0h-9m9 0h1.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 3.75 10.5V9A2.25 2.25 0 0 1 6 6.75h1.5" />
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Belum ada data pengalaman</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan pengalaman pertama Anda.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dialog tambah pengalaman --}}
    <dialog
        id="pengalaman-create"
        data-dismissible
        class="m-auto max-h-[calc(100vh-2rem)] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border-0 bg-transparent p-0 backdrop:bg-gray-950/60 backdrop:backdrop-blur-sm"
    >
        <div class="rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-gray-900/10 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Pengalaman</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi riwayat pengalaman Anda.</p>
                </div>

                <button
                    type="button"
                    data-dialog-close
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:hover:bg-white/5 dark:hover:text-gray-200"
                >
                    <span class="sr-only">Tutup</span>
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('mahasiswa.pengalaman.store') }}" class="mt-5 space-y-5">
                @csrf

                @include('mahasiswa.pengalaman._form', [
                    'pengalaman' => null,
                    'jenisOptions' => $jenisOptions,
                    'editingId' => null,
                ])

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-white/10">
                    <button
                        type="button"
                        data-dialog-close
                        class="inline-flex justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-800"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30"
                    >
                        Simpan Pengalaman
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Dialog edit pengalaman (hanya untuk data manual) --}}
    @foreach ($pengalaman as $item)
        @continue($item->sumber_data !== 'Manual')

        <dialog
            id="pengalaman-edit-{{ $item->id }}"
            data-dismissible
            class="m-auto max-h-[calc(100vh-2rem)] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border-0 bg-transparent p-0 backdrop:bg-gray-950/60 backdrop:backdrop-blur-sm"
        >
            <div class="rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-gray-900/10 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Pengalaman</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui data pengalaman Anda.</p>
                    </div>

                    <button
                        type="button"
                        data-dialog-close
                        class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:hover:bg-white/5 dark:hover:text-gray-200"
                    >
                        <span class="sr-only">Tutup</span>
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('mahasiswa.pengalaman.update', $item) }}" class="mt-5 space-y-5">
                    @csrf
                    @method('PUT')

                    @include('mahasiswa.pengalaman._form', [
                        'pengalaman' => $item,
                        'jenisOptions' => $jenisOptions,
                        'editingId' => $item->id,
                    ])

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-white/10">
                        <button
                            type="button"
                            data-dialog-close
                            class="inline-flex justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-800"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    @endforeach

    @include('mahasiswa._dialog-scripts')

    @if ($errors->any())
        @push('scripts')
            <script>
                document.getElementById({{ Js::from(old('_editing_id') ? 'pengalaman-edit-'.old('_editing_id') : 'pengalaman-create') }})?.showModal();
            </script>
        @endpush
    @endif
@endsection
