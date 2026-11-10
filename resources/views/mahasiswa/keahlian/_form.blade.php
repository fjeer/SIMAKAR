@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
    $labelClass = 'block space-y-2 text-sm font-medium text-gray-700 dark:text-gray-200';
@endphp

@if ($editingId)
    <input type="hidden" name="_editing_id" value="{{ $editingId }}">
@endif

<div class="grid gap-4">
    <label class="{{ $labelClass }}">
        <span>Nama Keahlian <span class="text-red-500">*</span></span>
        <input
            type="text"
            name="nama_keahlian"
            value="{{ old('nama_keahlian', $keahlian?->nama_keahlian) }}"
            placeholder="Contoh: Laravel"
            maxlength="255"
            class="{{ $inputClass }}"
            required
        >
    </label>

    <label class="{{ $labelClass }}">
        <span>Tingkat <span class="text-red-500">*</span></span>
        <select name="tingkat" class="{{ $inputClass }}" required>
            <option value="">Pilih tingkat penguasaan</option>
            @foreach ($tingkatOptions as $tingkat)
                <option value="{{ $tingkat }}" @selected(old('tingkat', $keahlian?->tingkat) === $tingkat)>
                    {{ $tingkat }}
                </option>
            @endforeach
        </select>
    </label>
</div>
