<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50 dark:bg-gray-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'SIMAKAR'))</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="min-h-full bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-white">
        <main class="mx-auto w-full max-w-7xl p-6">
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>
