@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
    $labelClass = 'block space-y-2 text-sm font-medium text-gray-700 dark:text-gray-200';
@endphp

@if ($editingId)
    <input type="hidden" name="_editing_id" value="{{ $editingId }}">
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <label class="{{ $labelClass }}">
        <span>Nama Sertifikasi <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="nama_sertifikasi"
            value="{{ old('nama_sertifikasi', $sertifikasi?->nama_sertifikasi) }}"
            placeholder="Contoh: Junior Web Developer"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Penerbit <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="penerbit"
            value="{{ old('penerbit', $sertifikasi?->penerbit) }}"
            placeholder="Contoh: BNSP"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }} sm:col-span-2">
        <span>Nomor Sertifikat</span>
        <input
            type="text"
            name="nomor_sertifikat"
            value="{{ old('nomor_sertifikat', $sertifikasi?->nomor_sertifikat) }}"
            placeholder="Contoh: 1234/SERT/2024"
            maxlength="255"
            class="{{ $inputClass }}"
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tanggal Terbit <span class="text-red-500">*</span></span>
        <input
            type="date"
            name="tanggal_terbit"
            value="{{ old('tanggal_terbit', $sertifikasi?->tanggal_terbit?->format('Y-m-d')) }}"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tanggal Kedaluwarsa</span>
        <input
            type="date"
            name="tanggal_kedaluwarsa"
            value="{{ old('tanggal_kedaluwarsa', $sertifikasi?->tanggal_kedaluwarsa?->format('Y-m-d')) }}"
            class="{{ $inputClass }}"
        >
        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Kosongkan jika berlaku selamanya.</span>
    </label>
</div>
