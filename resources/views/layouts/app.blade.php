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
        {{-- Overlay untuk sidebar di layar kecil --}}
        <div
            id="sidebar-overlay"
            class="fixed inset-0 z-30 hidden bg-gray-900/60 backdrop-blur-sm lg:hidden"
            aria-hidden="true"
        ></div>

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-gradient-to-b from-indigo-950 via-indigo-900 to-slate-950 text-indigo-100 shadow-2xl transition-transform duration-300 ease-in-out lg:translate-x-0"
        >
            @php
                $dashboardRoute = 'dashboard.'.auth()->user()->peran;
            @endphp

            <a
                href="{{ Route::has($dashboardRoute) ? route($dashboardRoute) : url('/') }}"
                class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-6 transition hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/30"
            >
                <span class="flex size-9 items-center justify-center rounded-xl bg-white/10 text-sm font-bold text-white ring-1 ring-white/20">
                    S
                </span>

                <div class="leading-tight">
                    <p class="text-sm font-semibold tracking-wide text-white">SIMAKAR</p>
                    <p class="text-[11px] text-indigo-300">Sistem Karier Mahasiswa</p>
                </div>
            </a>

            <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6" aria-label="Navigasi utama">
                @php
                    $menuGroups = [
                        [
                            'gate' => 'mahasiswa',
                            'label' => 'Menu Utama',
                            'items' => [
                                ['label' => 'Dashboard', 'route' => 'dashboard.mahasiswa', 'icon' => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                            ],
                        ],
                        [
                            'gate' => 'mahasiswa',
                            'label' => 'Profil & CV',
                            'items' => [
                                ['label' => 'Profil CV', 'route' => 'mahasiswa.profil.index', 'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
                                ['label' => 'Pendidikan', 'route' => 'mahasiswa.pendidikan.index', 'icon' => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342'],
                                ['label' => 'Pengalaman', 'route' => 'mahasiswa.pengalaman.index', 'icon' => 'M20.25 14.15v4.098a2.25 2.25 0 0 1-2.25 2.25h-12a2.25 2.25 0 0 1-2.25-2.25V14.15M16.5 6.75V6a2.25 2.25 0 0 0-2.25-2.25h-4.5A2.25 2.25 0 0 0 7.5 6v.75m9 0h-9m9 0h1.5a2.25 2.25 0 0 1 2.25 2.25v1.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 3.75 10.5V9A2.25 2.25 0 0 1 6 6.75h1.5'],
                                ['label' => 'Sertifikasi', 'route' => 'mahasiswa.sertifikasi.index', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                                ['label' => 'Keahlian', 'route' => 'mahasiswa.keahlian.index', 'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z'],
                                ['label' => 'Preview ATS', 'route' => 'mahasiswa.preview.index', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                            ],
                        ],
                        [
                            'gate' => 'dosen',
                            'label' => 'Ruang Dosen',
                            'items' => [
                                ['label' => 'Dashboard', 'route' => 'dashboard.dosen', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
                            ],
                        ],
                        [
                            'gate' => 'kaprodi',
                            'label' => 'Ruang Kaprodi',
                            'items' => [
                                ['label' => 'Dashboard', 'route' => 'dashboard.kaprodi', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
                                ['label' => 'Talent Pool Prodi', 'route' => 'kaprodi.talent-pool', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
                            ],
                        ],
                        [
                            'gate' => 'dekan',
                            'label' => 'Ruang Dekan',
                            'items' => [
                                ['label' => 'Dashboard Fakultas', 'route' => 'dashboard.dekan', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z'],
                                ['label' => 'Statistik', 'route' => 'dekan.statistik', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
                            ],
                        ],
                        [
                            'gate' => 'admin',
                            'label' => 'Ruang Admin',
                            'items' => [
                                ['label' => 'Master Data', 'route' => 'admin.master-data', 'icon' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125'],
                                ['label' => 'Sistem Log', 'route' => 'admin.sistem-log', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z'],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($menuGroups as $group)
                    @can($group['gate'])
                        <div>
                            <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-indigo-300/70">
                                {{ $group['label'] }}
                            </p>

                            <ul class="mt-2 space-y-1">
                                @foreach ($group['items'] as $item)
                                    @php
                                        $isActive = $item['route'] !== null && Route::has($item['route']) && request()->routeIs($item['route']);
                                        $url = $item['route'] !== null && Route::has($item['route']) ? route($item['route']) : '#';
                                    @endphp

                                    <li>
                                        <a
                                            href="{{ $url }}"
                                            @class([
                                                'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                                'bg-white/10 text-white shadow-sm ring-1 ring-white/10' => $isActive,
                                                'text-indigo-200/80 hover:bg-white/5 hover:text-white' => ! $isActive,
                                            ])
                                            @if ($isActive) aria-current="page" @endif
                                        >
                                            <svg
                                                class="size-5 shrink-0 {{ $isActive ? 'text-white' : 'text-indigo-300/70 group-hover:text-white' }}"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                            </svg>

                                            <span class="truncate">{{ $item['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endcan
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-6 py-4">
                <p class="text-[11px] text-indigo-300/70">
                    &copy; {{ date('Y') }} SIMAKAR &middot; UNUJA
                </p>
            </div>
        </aside>

        {{-- Area utama --}}
        <div class="flex min-h-full flex-col lg:pl-64">
            {{-- Top navbar --}}
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-white/10 dark:bg-gray-950/80">
                <div class="flex h-16 items-center gap-4 px-4 sm:px-6">
                    <button
                        type="button"
                        id="sidebar-toggle"
                        class="inline-flex size-10 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 lg:hidden dark:text-gray-300 dark:hover:bg-white/5"
                        aria-controls="sidebar"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Buka menu navigasi</span>

                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white shadow-sm shadow-indigo-600/30">
                            {{ mb_strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}
                        </span>

                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                {{ auth()->user()->nama }}
                            </p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ ucfirst(auth()->user()->peran) }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:bg-gray-900 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-800"
                        >
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>

                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Konten halaman --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto w-full max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>

        <script>
            (() => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const toggle = document.getElementById('sidebar-toggle');

                if (!sidebar || !overlay || !toggle) {
                    return;
                }

                const setSidebarOpen = (isOpen) => {
                    sidebar.classList.toggle('-translate-x-full', !isOpen);
                    overlay.classList.toggle('hidden', !isOpen);
                    toggle.setAttribute('aria-expanded', String(isOpen));
                };

                toggle.addEventListener('click', () => {
                    setSidebarOpen(sidebar.classList.contains('-translate-x-full'));
                });

                overlay.addEventListener('click', () => setSidebarOpen(false));

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        setSidebarOpen(false);
                    }
                });
            })();
        </script>

        @stack('scripts')
    </body>
</html>
