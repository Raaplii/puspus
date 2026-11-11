<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Perpustakaan Digital')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-sky-500 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition">
                            <i class="fa-solid fa-book-open-reader text-lg"></i>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-slate-900 group-hover:text-brand-600 transition">PerpusDigital</span>
                            <span class="block text-[11px] text-slate-400 font-medium -mt-1">Sistem Informasi Perpustakaan</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links based on Role -->
                @auth
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    @if(Auth::user()->isSiswa())
                        <a href="{{ route('siswa.dashboard') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('siswa.dashboard') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-house-chimney mr-1.5 opacity-70"></i> Beranda
                        </a>
                        <a href="{{ route('siswa.catalog') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('siswa.catalog') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-books mr-1.5 opacity-70"></i> Katalog Buku
                        </a>
                        <a href="{{ route('siswa.tenggat') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('siswa.tenggat') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-clock mr-1.5 opacity-70"></i> Tenggat Waktu & Peminjaman
                        </a>
                    @elseif(Auth::user()->isPetugas())
                        <a href="{{ route('petugas.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('petugas.dashboard') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-chart-pie mr-1.5 opacity-70"></i> Ringkasan
                        </a>
                        <a href="{{ route('petugas.books') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('petugas.books*') && !request()->routeIs('petugas.books.create') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-book-bookmark mr-1.5 opacity-70"></i> Katalog Buku
                        </a>
                        <a href="{{ route('petugas.books.create') }}" class="px-3 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('petugas.books.create') ? 'bg-brand-600 text-white' : 'text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200' }}">
                            <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Buku
                        </a>
                        <a href="{{ route('petugas.deadlines') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('petugas.deadlines') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-calendar-clock mr-1.5 opacity-70"></i> Tenggat Waktu
                        </a>
                        <a href="{{ route('petugas.returns') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('petugas.returns') ? 'bg-sky-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-clipboard-check mr-1.5 opacity-70"></i> Siswa Sudah Mengumpulkan
                        </a>
                    @endif
                </nav>

                <!-- User Profile & Logout -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-semibold text-slate-800 leading-tight">{{ Auth::user()->name }}</span>
                        <div class="flex items-center justify-end space-x-1.5 mt-0.5">
                            @if(Auth::user()->isPetugas())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <i class="fa-solid fa-shield-halved text-[9px] mr-1"></i> Petugas / Penjaga
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                    <i class="fa-solid fa-user-graduate text-[9px] mr-1"></i> Siswa (NIS: {{ Auth::user()->nis ?? '-' }})
                                </span>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                            <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                        </button>
                    </form>
                </div>
                @else
                <div>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk
                    </a>
                </div>
                @endauth
            </div>
        </div>

        <!-- Mobile Navigation Bar -->
        @auth
        <div class="md:hidden border-t border-slate-200 bg-slate-50/80 px-4 py-2 flex items-center justify-around text-xs font-semibold">
            @if(Auth::user()->isSiswa())
                <a href="{{ route('siswa.dashboard') }}" class="py-1 px-2 rounded {{ request()->routeIs('siswa.dashboard') ? 'text-brand-700' : 'text-slate-600' }}">Beranda</a>
                <a href="{{ route('siswa.catalog') }}" class="py-1 px-2 rounded {{ request()->routeIs('siswa.catalog') ? 'text-brand-700' : 'text-slate-600' }}">Katalog</a>
                <a href="{{ route('siswa.tenggat') }}" class="py-1 px-2 rounded {{ request()->routeIs('siswa.tenggat') ? 'text-brand-700' : 'text-slate-600' }}">Tenggat & Peminjaman</a>
            @else
                <a href="{{ route('petugas.dashboard') }}" class="py-1 px-2 rounded {{ request()->routeIs('petugas.dashboard') ? 'text-brand-700 font-bold' : 'text-slate-600' }}">Ringkasan</a>
                <a href="{{ route('petugas.books') }}" class="py-1 px-2 rounded {{ request()->routeIs('petugas.books') ? 'text-brand-700 font-bold' : 'text-slate-600' }}">Katalog</a>
                <a href="{{ route('petugas.books.create') }}" class="py-1 px-2 rounded {{ request()->routeIs('petugas.books.create') ? 'text-brand-700 font-bold' : 'text-brand-600 font-bold' }}">+ Tambah Buku</a>
                <a href="{{ route('petugas.deadlines') }}" class="py-1 px-2 rounded {{ request()->routeIs('petugas.deadlines') ? 'text-brand-700 font-bold' : 'text-slate-600' }}">Tenggat</a>
                <a href="{{ route('petugas.returns') }}" class="py-1 px-2 rounded {{ request()->routeIs('petugas.returns') ? 'text-brand-700 font-bold' : 'text-slate-600' }}">Pengembalian</a>
            @endif
        </div>
        @endauth
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-sm animate-fade-in">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="font-semibold flex items-center mb-1">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-rose-600"></i> Terjadi kesalahan input:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium">PerpusDigital &bull; Sistem Informasi Perpustakaan & Peminjaman Buku</p>
            <p class="mt-1 text-slate-400">Database MySQL &bull; Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

