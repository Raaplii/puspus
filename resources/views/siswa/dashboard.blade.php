@extends('layouts.app')

@section('title', 'Beranda Siswa - Sistem Perpustakaan')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-sky-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 opacity-10 pointer-events-none flex items-center pr-10">
            <i class="fa-solid fa-graduation-cap text-[180px]"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-sky-100 backdrop-blur-md mb-3">
                <i class="fa-solid fa-id-card mr-1.5 text-sky-300"></i> NIS: {{ $user->nis ?? '20261001' }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ $user->name }}! 👋</h1>
            <p class="text-sky-100 text-sm mt-2 leading-relaxed">
                Selamat datang di portal perpustakaan siswa. Di sini Anda dapat meminjam buku, memantau tenggat waktu pengembalian buku secara real-time, dan mengembalikan buku yang telah selesai dibaca.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('siswa.catalog') }}" class="inline-flex items-center px-4 py-2 bg-white text-brand-900 hover:bg-sky-50 font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-magnifying-glass mr-1.5 text-brand-600"></i> Cari & Pinjam Buku
                </a>
                <a href="{{ route('siswa.tenggat') }}" class="inline-flex items-center px-4 py-2 bg-sky-600/40 hover:bg-sky-600/60 text-white font-semibold text-xs rounded-xl border border-sky-400/30 transition">
                    <i class="fa-solid fa-clock mr-1.5"></i> Cek Tenggat Waktu
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-13 h-13 rounded-2xl bg-sky-100 text-brand-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sedang Dipinjam</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <span class="text-2xl font-bold text-slate-900">{{ $activeCount }}</span>
                    <span class="text-xs text-slate-400">/ maks 3 buku</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-13 h-13 rounded-2xl {{ $overdueCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid {{ $overdueCount > 0 ? 'fa-triangle-exclamation' : 'fa-calendar-check' }}"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Status Tenggat</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    @if($overdueCount > 0)
                        <span class="text-2xl font-bold text-rose-600">{{ $overdueCount }}</span>
                        <span class="text-xs font-semibold text-rose-600">Terlambat!</span>
                    @else
                        <span class="text-2xl font-bold text-emerald-600">0</span>
                        <span class="text-xs font-medium text-emerald-600">Semua aman</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-13 h-13 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Buku Telah Dikembalikan</p>
                <div class="flex items-baseline space-x-2 mt-0.5">
                    <span class="text-2xl font-bold text-slate-900">{{ $returnedCount }}</span>
                    <span class="text-xs text-slate-400">selesai dibaca</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loans Section (Tenggat Waktu & Tombol Pengembalian) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-clock text-brand-600 mr-2"></i>
                    Buku yang Sedang Dipinjam & Tenggat Waktu Pengembalian
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau tanggal jatuh tempo agar tidak terkena sanksi keterlambatan</p>
            </div>
            <a href="{{ route('siswa.tenggat') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center">
                Lihat Semua Riwayat <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>

        @if($activeLoans->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada buku yang sedang dipinjam</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Anda belum meminjam buku saat ini. Silakan kunjungi katalog buku untuk mulai meminjam.</p>
                <a href="{{ route('siswa.catalog') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-plus mr-1.5"></i> Pinjam Buku Sekarang
                </a>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($activeLoans as $loan)
                    @php
                        $badge = $loan->getDeadlineBadge();
                        $diff = $loan->daysRemaining();
                    @endphp
                    <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                        <!-- Book Information -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-16 rounded-lg bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex flex-col items-center justify-center text-xs shrink-0 shadow">
                                <i class="fa-solid fa-book text-base text-sky-400 mb-1"></i>
                                <span class="text-[9px] font-bold text-slate-300 uppercase truncate max-w-[42px]">{{ substr($loan->book->category, 0, 5) }}</span>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $loan->book->category }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $loan->book->title }}</h3>
                                <p class="text-xs text-slate-500">Penulis: <span class="font-medium text-slate-700">{{ $loan->book->author }}</span> &bull; ISBN: {{ $loan->book->isbn ?? '-' }}</p>
                                
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                    <span><i class="fa-regular fa-calendar-check mr-1 text-slate-400"></i> Pinjam: <strong class="text-slate-700">{{ $loan->loan_date->translatedFormat('d M Y') }}</strong></span>
                                    <span><i class="fa-solid fa-calendar-xmark mr-1 text-rose-500"></i> Tenggat: <strong class="text-rose-600 font-bold">{{ $loan->due_date->translatedFormat('d M Y') }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Deadline Badge & Return Button -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 self-end md:self-center shrink-0">
                            <!-- Due date alert badge -->
                            <div class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold flex items-center space-x-1.5 {{ $badge['badge_class'] }}">
                                <i class="fa-solid {{ $diff < 0 ? 'fa-bell text-rose-600' : ($diff <= 2 ? 'fa-hourglass-half' : 'fa-clock') }}"></i>
                                <span>{{ $badge['label'] }}</span>
                            </div>

                            <!-- Kembalikan Buku Action Button -->
                            <form method="POST" action="{{ route('siswa.return', $loan) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku \'{{ $loan->book->title }}\'?')">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center space-x-1.5">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span>Kembalikan Buku</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Available Books Preview (Rekomendasi Buku untuk Dipinjam) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Buku Rekomendasi Tersedia</h2>
                <p class="text-xs text-slate-500">Pilih buku favorit Anda dan pinjam secara langsung</p>
            </div>
            <a href="{{ route('siswa.catalog') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Buka Katalog Lengkap <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($availableBooks as $book)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-2">
                            <span class="px-2 py-0.5 rounded bg-sky-50 text-brand-700 font-semibold border border-sky-200">
                                {{ $book->category }}
                            </span>
                            <span class="text-slate-400 font-medium">Stok: {{ $book->available_stock }}</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 line-clamp-1 mt-1">{{ $book->title }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $book->author }}</p>
                        <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed">{{ $book->description }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <form method="POST" action="{{ route('siswa.borrow', $book) }}">
                            @csrf
                            <input type="hidden" name="duration" value="7">
                            <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center space-x-1.5"
                                onclick="return confirm('Pinjam buku \'{{ $book->title }}\' dengan durasi 7 hari?')">
                                <i class="fa-solid fa-book-bookmark text-[11px]"></i>
                                <span>Pinjam Buku (7 Hari)</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

