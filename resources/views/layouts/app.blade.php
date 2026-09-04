<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir & Warehouse')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex min-h-screen">

        {{-- ===== SIDEBAR (desktop: fixed, mobile: slide-in drawer) ===== --}}
        <aside id="sidebar"
            class="hw-sidebar fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transition-transform duration-200 ease-in-out overflow-y-auto sm:translate-x-0">

            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <div class="hw-logo-badge w-10 h-10 rounded-full flex items-center justify-center text-sm">HW</div>
                <div>
                    <p class="text-white font-semibold text-sm leading-tight">Kasir & Warehouse</p>
                    <p class="text-gray-400 text-xs">{{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Kasir Panel' }}</p>
                </div>
            </div>

            <nav class="px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="hw-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>

                @if (auth()->user()->isAdmin())
                    <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Warehouse</p>

                    <a href="{{ route('products.index') }}" class="hw-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Kelola Produk
                    </a>

                    <a href="{{ route('stock.index') }}" class="hw-nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                        Stok Masuk/Keluar
                    </a>

                     <a href="{{ route('admin.stock-opname.index') }}"
                        class="hw-nav-link {{ request()->routeIs('admin.stock-opname.*') ? 'active' : '' }}">

                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5h6M9 12h6m-6 4h4" />
                        </svg>

                        Stock Opname
                    </a>
                @endif

                @if (auth()->user()->isKasir())

                        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kasir
                        </p>

                        {{-- POINT OF SALE --}}
                        <a
                            href="{{ route('pos.index') }}"
                            class="hw-nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}"
                        >

                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h18v4H3V3Zm2 4v14h14V7M8 11h8M8 15h5"
                                />
                            </svg>

                            Point of Sale

                        </a>

                        {{-- INVOICE KREDIT --}}
                        <a
                            href="{{ route('pos.credit.index') }}"
                            class="hw-nav-link {{ request()->routeIs('pos.credit.*') ? 'active' : '' }}"
                        >

                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 14h6m-6-4h6m2 11H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2ZM9 3v2h6V3"
                                />
                            </svg>

                            Invoice Kredit

                        </a>

                        {{-- RIWAYAT TRANSAKSI --}}
                        <a
                            href="{{ route('transactions.index') }}"
                            class="hw-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                        >

                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5h6m-8 4h10M7 13h10M7 17h6m5 4H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2Z"
                                />
                            </svg>

                            Riwayat Transaksi

                        </a>

                    @endif
            </nav>
        </aside>

        {{-- Backdrop buat mobile, klik luar sidebar buat nutup --}}
        <div id="backdrop" class="hw-backdrop fixed inset-0 z-30 hidden sm:hidden"></div>

       {{-- ===== MAIN CONTENT ===== --}}
<div class="flex-1 sm:ml-64 min-w-0">

    {{-- ===== NAVBAR ===== --}}
    <header class="sticky top-0 z-20 bg-white border-b border-gray-200">

        <div class="h-16 px-4 sm:px-6 flex items-center justify-between">

            {{-- Kiri --}}
            <div class="flex items-center gap-3">

                {{-- Hamburger Mobile --}}
                <button
                    id="menuToggle"
                    type="button"
                    class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:bg-gray-100 transition"
                    aria-label="Buka menu">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>

                </button>


                {{-- Judul Halaman --}}
                <div>

                    <h1 class="text-base sm:text-lg font-semibold text-gray-800">
                        @yield('title', 'Kasir & Warehouse')
                    </h1>

                    <p class="hidden sm:block text-xs text-gray-500">
                        Sistem Manajemen Kasir & Warehouse
                    </p>

                </div>

            </div>


            {{-- Kanan: User --}}
            <div class="relative">

                <button
                    id="profileToggle"
                    type="button"
                    class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition">

                    {{-- Avatar --}}
                    <div class="w-9 h-9 rounded-full bg-[#e8a33c] text-[#141414] flex items-center justify-center font-bold text-sm uppercase">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>


                    {{-- Nama User --}}
                    <div class="hidden sm:block text-left">

                        <p class="text-sm font-semibold text-gray-800 leading-tight">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-gray-500 capitalize">
                            {{ auth()->user()->role }}
                        </p>

                    </div>


                    {{-- Chevron --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="hidden sm:block w-4 h-4 text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m19 9-7 7-7-7" />

                    </svg>

                </button>


                {{-- Profile Dropdown --}}
                <div
                    id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

                    {{-- User Info --}}
                    <div class="px-4 py-4 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-full bg-[#e8a33c] text-[#141414] flex items-center justify-center font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-gray-800 truncate">
                                    {{ auth()->user()->name }}
                                </p>

                                <p class="text-xs text-gray-500 capitalize">
                                    {{ auth()->user()->role }}
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Logout --}}
                    <div class="p-2">

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50 transition">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />

                                </svg>

                                <span>
                                    Logout
                                </span>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </header>


    {{-- ===== CONTENT ===== --}}
    <main class="p-4 sm:p-6 max-w-5xl mx-auto">

        @if (session('success'))

            <div class="mb-4 bg-green-50 text-green-700 border border-green-200 rounded-lg p-3 text-sm">
                {{ session('success') }}
            </div>

        @endif


        @if ($errors->any())

            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-sm">
                {{ $errors->first() }}
            </div>

        @endif


        @yield('content')

    </main>

</div>

   <script>

    // ==============================
    // SIDEBAR MOBILE
    // ==============================

    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('backdrop');
    const menuToggle = document.getElementById('menuToggle');

    function openSidebar() {

        sidebar?.classList.remove('-translate-x-full');

        backdrop?.classList.remove('hidden');

    }

    function closeSidebar() {

        sidebar?.classList.add('-translate-x-full');

        backdrop?.classList.add('hidden');

    }

    menuToggle?.addEventListener('click', openSidebar);

    backdrop?.addEventListener('click', closeSidebar);


    // ==============================
    // PROFILE DROPDOWN
    // ==============================

    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    profileToggle?.addEventListener('click', function (event) {

        event.stopPropagation();

        profileDropdown?.classList.toggle('hidden');

    });


    // Tutup dropdown ketika klik di luar
    document.addEventListener('click', function (event) {

        if (
            profileDropdown &&
            !profileDropdown.contains(event.target) &&
            !profileToggle.contains(event.target)
        ) {

            profileDropdown.classList.add('hidden');

        }

    });

</script>

    @stack('scripts')
</body>
</html>
