@extends('layouts.app')

@section('title', 'Masuk - Sistem Perpustakaan Digital')

@section('content')
<div class="max-w-md mx-auto my-6 sm:my-12">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        <!-- Header Banner -->
        <div class="bg-gradient-to-br from-brand-800 via-brand-700 to-sky-600 p-8 text-white text-center relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 opacity-10">
                <i class="fa-solid fa-book-open-reader text-9xl"></i>
            </div>
            <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <i class="fa-solid fa-book-bookmark text-2xl text-sky-200"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Perpustakaan Sekolah</h1>
            <p class="text-xs text-sky-100 mt-1 max-w-xs mx-auto">Masuk untuk mengakses layanan peminjaman dan pengawasan buku</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@perpustakaan.com"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="ml-2">Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-semibold text-sm rounded-xl shadow-md shadow-brand-500/25 transition duration-150 flex items-center justify-center space-x-2">
                    <span>Masuk ke Sistem</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        <i class="fa-solid fa-key text-amber-500 mr-1"></i> Akun Contoh (1-Klik Isi)
                    </span>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">demo</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Akun Siswa -->
                    <button type="button" onclick="fillCredentials('siswa@perpustakaan.com', 'password')"
                        class="p-3 text-left rounded-xl border border-sky-200 bg-sky-50/70 hover:bg-sky-100 hover:border-sky-300 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-sky-900 group-hover:text-sky-700">Akun Siswa</span>
                            <i class="fa-solid fa-user-graduate text-sky-600 text-sm"></i>
                        </div>
                        <div class="text-[11px] text-slate-600 mt-1 font-mono truncate">siswa@perpustakaan.com</div>
                        <div class="text-[10px] text-sky-700 mt-1 font-medium">Budi Santoso &bull; Pinjam & Tenggat</div>
                    </button>

                    <!-- Akun Petugas -->
                    <button type="button" onclick="fillCredentials('petugas@perpustakaan.com', 'password')"
                        class="p-3 text-left rounded-xl border border-amber-200 bg-amber-50/70 hover:bg-amber-100 hover:border-amber-300 transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 group-hover:text-amber-700">Akun Petugas</span>
                            <i class="fa-solid fa-shield-halved text-amber-600 text-sm"></i>
                        </div>
                        <div class="text-[11px] text-slate-600 mt-1 font-mono truncate">petugas@perpustakaan.com</div>
                        <div class="text-[10px] text-amber-700 mt-1 font-medium">Pak Joko &bull; Mengawasi Siswa</div>
                    </button>
                </div>
                <p class="text-[11px] text-center text-slate-400 mt-3">Kata sandi kedua akun: <code class="bg-slate-100 px-1.5 py-0.5 rounded font-semibold text-slate-600">password</code></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
@endsection

