@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
    $labelClass = 'block space-y-2 text-sm font-medium text-gray-700 dark:text-gray-200';
@endphp

@if ($editingId)
    <input type="hidden" name="_editing_id" value="{{ $editingId }}">
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <label class="{{ $labelClass }}">
        <span>Jenjang <span class="text-red-500">*</span></span>
        <select name="jenjang" class="{{ $inputClass }}" required>
            <option value="">Pilih jenjang</option>
            @foreach ($jenjangOptions as $jenjang)
                <option value="{{ $jenjang }}" @selected(old('jenjang', $pendidikan?->jenjang) === $jenjang)>
                    {{ $jenjang }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="{{ $labelClass }}">
        <span>Nama Institusi <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="nama_institusi"
            value="{{ old('nama_institusi', $pendidikan?->nama_institusi) }}"
            placeholder="Contoh: Universitas Nurul Jadid"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Jurusan</span>
        <input
            type="text"
            name="jurusan"
            value="{{ old('jurusan', $pendidikan?->jurusan) }}"
            placeholder="Contoh: Teknik Informatika"
            maxlength="255"
            class="{{ $inputClass }}"
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Nilai Akhir</span>
        <input
            type="number"
            name="nilai_akhir"
            value="{{ old('nilai_akhir', $pendidikan?->nilai_akhir) }}"
            placeholder="Contoh: 3.75"
            min="0"
            max="100"
            step="0.01"
            class="{{ $inputClass }}"
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tahun Mulai <span class="text-red-500">*</span></span>
        <input
            type="number"
            name="tahun_mulai"
            value="{{ old('tahun_mulai', $pendidikan?->tahun_mulai) }}"
            placeholder="{{ now()->year }}"
            min="1900"
            max="{{ now()->year + 1 }}"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tahun Selesai</span>
        <input
            type="number"
            name="tahun_selesai"
            value="{{ old('tahun_selesai', $pendidikan?->tahun_selesai) }}"
            placeholder="Kosongkan jika masih berjalan"
            min="1900"
            max="{{ now()->year + 1 }}"
            class="{{ $inputClass }}"
        >
    </label>
</div>
