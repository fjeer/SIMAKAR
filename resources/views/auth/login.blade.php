<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50 dark:bg-gray-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Masuk - {{ config('app.name', 'SIMAKAR') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-full items-center justify-center bg-gray-50 p-6 antialiased dark:bg-gray-950">
        <main class="w-full max-w-md">
            <div class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-bold text-white shadow-lg shadow-indigo-600/30">
                        S
                    </span>

                    <div class="space-y-1">
                        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            Masuk ke SIMAKAR
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Gunakan NIM atau NIDN yang terdaftar di SSO kampus.
                        </p>
                    </div>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300 dark:ring-red-400/30">
                        {{ $errors->first('nim_nidn') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label for="nim_nidn" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            NIM / NIDN
                        </label>

                        <input
                            id="nim_nidn"
                            name="nim_nidn"
                            type="text"
                            value="{{ old('nim_nidn') }}"
                            placeholder="Masukkan NIM atau NIDN"
                            autocomplete="username"
                            autofocus
                            required
                            aria-invalid="{{ $errors->has('nim_nidn') ? 'true' : 'false' }}"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                        >
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/30 active:bg-indigo-700"
                    >
                        Masuk
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                Layanan terintegrasi dengan SSO Universitas Nurul Jadid.
            </p>
        </main>
    </body>
</html>
