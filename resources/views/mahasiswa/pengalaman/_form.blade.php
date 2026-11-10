@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
    $labelClass = 'block space-y-2 text-sm font-medium text-gray-700 dark:text-gray-200';
@endphp

@if ($editingId)
    <input type="hidden" name="_editing_id" value="{{ $editingId }}">
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <label class="{{ $labelClass }}">
        <span>Jenis <span class="text-red-500">*</span></span>
        <select name="jenis" class="{{ $inputClass }}" required>
            <option value="">Pilih jenis pengalaman</option>
            @foreach ($jenisOptions as $jenis)
                <option value="{{ $jenis }}" @selected(old('jenis', $pengalaman?->jenis) === $jenis)>
                    {{ $jenis }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="{{ $labelClass }}">
        <span>Posisi <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="posisi"
            value="{{ old('posisi', $pengalaman?->posisi) }}"
            placeholder="Contoh: Backend Developer"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }} sm:col-span-2">
        <span>Nama Institusi <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="nama_institusi"
            value="{{ old('nama_institusi', $pengalaman?->nama_institusi) }}"
            placeholder="Contoh: PT Teknologi Nusantara"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tanggal Mulai <span class="text-red-500">*</span></span>
        <input
            type="date"
            name="tanggal_mulai"
            value="{{ old('tanggal_mulai', $pengalaman?->tanggal_mulai?->format('Y-m-d')) }}"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tanggal Selesai</span>
        <input
            type="date"
            name="tanggal_selesai"
            value="{{ old('tanggal_selesai', $pengalaman?->tanggal_selesai?->format('Y-m-d')) }}"
            class="{{ $inputClass }}"
        >
        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Kosongkan jika masih berjalan.</span>
    </label>

    <label class="{{ $labelClass }} sm:col-span-2">
        <span>Deskripsi</span>
        <textarea
            name="deskripsi"
            rows="4"
            maxlength="2000"
            placeholder="Ceritakan peran, tanggung jawab, dan pencapaian Anda."
            class="{{ $inputClass }}"
        >{{ old('deskripsi', $pengalaman?->deskripsi) }}</textarea>
        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Maksimal 2000 karakter.</span>
    </label>
</div>
