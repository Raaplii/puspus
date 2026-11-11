@extends('layouts.app')

@section('title', 'Ringkasan Petugas Perpustakaan')

@section('content')
<div class="space-y-8">
    <!-- Officer Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-brand-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 opacity-10 pointer-events-none flex items-center pr-10">
            <i class="fa-solid fa-shield-halved text-[180px]"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-400/20 text-amber-200 border border-amber-400/30 backdrop-blur-md mb-3">
                <i class="fa-solid fa-user-shield mr-1.5 text-amber-400"></i> Panel Penjaga / Petugas Perpustakaan
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Pusat Pengawasan & Pengelolaan Buku</h1>
            <p class="text-slate-300 text-sm mt-2 leading-relaxed">
                Tugas petugas: Menambahkan dan melihat koleksi buku untuk dipinjam siswa, mengawasi siswa yang telah mengumpulkan buku, serta memantau tenggat waktu peminjaman.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('petugas.books.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Buku untuk Siswa
                </a>
                <a href="{{ route('petugas.deadlines') }}" class="inline-flex items-center px-4 py-2 bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-clock mr-1.5"></i> Pantau Tenggat Waktu
                </a>
                <a href="{{ route('petugas.returns') }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-900 hover:bg-slate-100 font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-clipboard-check mr-1.5 text-emerald-600"></i> Siswa Sudah Mengumpulkan
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Katalog & Tambah Buku -->
        <a href="{{ route('petugas.books') }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Koleksi Buku</p>
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-books"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline space-x-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalBooks }}</span>
                <span class="text-xs text-slate-400">judul ({{ $totalStock }} eks)</span>
            </div>
            <p class="text-[11px] text-purple-600 font-medium mt-2 flex items-center">
                Lihat & tambah buku baru <i class="fa-solid fa-chevron-right ml-1 text-[9px]"></i>
            </p>
        </a>

        <!-- Peminjaman Aktif -->
        <a href="{{ route('petugas.deadlines') }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sedang Dipinjam</p>
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-brand-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline space-x-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $activeLoansCount }}</span>
                <span class="text-xs text-slate-400">buku aktif</span>
            </div>
            <p class="text-[11px] text-brand-600 font-medium mt-2 flex items-center">
                Lihat daftar tenggat waktu <i class="fa-solid fa-chevron-right ml-1 text-[9px]"></i>
            </p>
        </a>

        <!-- Sudah Mengumpulkan -->
        <a href="{{ route('petugas.returns') }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Mengumpulkan</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline space-x-2">
                <span class="text-3xl font-extrabold text-emerald-600">{{ $returnedLoansCount }}</span>
                <span class="text-xs text-slate-400">pengembalian</span>
            </div>
            <p class="text-[11px] text-emerald-600 font-medium mt-2 flex items-center">
                Awasi riwayat pengembalian <i class="fa-solid fa-chevron-right ml-1 text-[9px]"></i>
            </p>
        </a>

        <!-- Melewati Tenggat (Terlambat) -->
        <a href="{{ route('petugas.deadlines', ['filter' => 'overdue']) }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lewat Tenggat Waktu</p>
                <div class="w-10 h-10 rounded-xl {{ $overdueCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline space-x-2">
                <span class="text-3xl font-extrabold {{ $overdueCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $overdueCount }}</span>
                <span class="text-xs text-rose-500 font-semibold">siswa menunggak</span>
            </div>
            <p class="text-[11px] text-rose-600 font-medium mt-2 flex items-center">
                Filter yang terlambat <i class="fa-solid fa-chevron-right ml-1 text-[9px]"></i>
            </p>
        </a>
    </div>

    <!-- Koleksi Buku Siswa & Tambah Buku Section -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-book-open-reader text-brand-600 mr-2"></i>
                    Koleksi Buku untuk Siswa (Terbaru)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Buku yang tersedia untuk dibaca dan dipinjam oleh para siswa</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('petugas.books.create') }}" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center">
                    <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Buku Baru
                </a>
                <a href="{{ route('petugas.books') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Lihat Semua Buku
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-5 sm:p-6">
            @foreach($recentBooks as $rb)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-sm transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-[10px] mb-1.5">
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">{{ $rb->category }}</span>
                            <span class="font-bold {{ $rb->available_stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Stok: {{ $rb->available_stock }}/{{ $rb->stock }}
                            </span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $rb->title }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $rb->author }}</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Tahun {{ $rb->year ?? '-' }}</span>
                        <a href="{{ route('petugas.books.edit', $rb) }}" class="text-brand-600 hover:text-brand-700 font-semibold">
                            Edit <i class="fa-solid fa-chevron-right text-[9px] ml-0.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Urgent Loans Table (Mendekati / Lewat Tenggat Waktu) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-bell text-rose-500 mr-2"></i>
                    Perhatian Khusus: Peminjaman Mendekati / Melewati Tenggat Waktu
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar siswa yang harus segera mengembalikan buku dalam 3 hari ke depan atau sudah lewat batas</p>
            </div>
            <a href="{{ route('petugas.deadlines') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Lihat Semua Peminjaman Aktif <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($urgentLoans->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2"></i>
                <p class="font-medium text-slate-600">Tidak ada peminjaman yang mendesak atau terlambat saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nama Siswa / NIS</th>
                            <th class="py-3 px-4">Judul Buku</th>
                            <th class="py-3 px-4">Tgl Pinjam</th>
                            <th class="py-3 px-4">Tenggat Waktu</th>
                            <th class="py-3 px-4">Status Tenggat</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($urgentLoans as $loan)
                            @php
                                $badge = $loan->getDeadlineBadge();
                                $diff = $loan->daysRemaining();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4">
                                    <strong class="text-slate-900 font-bold block">{{ $loan->user->name }}</strong>
                                    <span class="text-[11px] text-slate-500">NIS: {{ $loan->user->nis ?? '-' }} &bull; {{ $loan->user->phone ?? $loan->user->email }}</span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-800">
                                    {{ $loan->book->title }}
                                    <span class="block text-[11px] text-slate-400 font-normal">{{ $loan->book->category }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $loan->loan_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $loan->due_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] border {{ $badge['badge_class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form method="POST" action="{{ route('petugas.confirmReturn', $loan) }}" onsubmit="return confirm('Konfirmasi bahwa buku \'{{ $loan->book->title }}\' telah dikembalikan oleh {{ $loan->user->name }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-300 font-bold text-[11px] rounded-lg transition">
                                            Konfirmasi Terima
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Returns (Pengawasan Siswa yang Baru Mengembalikan) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-clipboard-check text-emerald-600 mr-2"></i>
                    Aktivitas Pengembalian Terbaru Siswa (Siswa yang Sudah Mengumpulkan)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau ketepatan waktu pengembalian buku oleh siswa</p>
            </div>
            <a href="{{ route('petugas.returns') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Buka Pengawasan Lengkap <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($recentReturns->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Belum ada aktivitas pengembalian buku tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Judul Buku</th>
                            <th class="py-3 px-4">Tenggat Waktu</th>
                            <th class="py-3 px-4">Tgl Dikembalikan</th>
                            <th class="py-3 px-4">Status Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentReturns as $ret)
                            @php
                                $badge = $ret->getDeadlineBadge();
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $ret->user->name }}
                                    <span class="block text-[11px] text-slate-400 font-normal">NIS: {{ $ret->user->nis ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-800">{{ $ret->book->title }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $ret->due_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $ret->return_date ? $ret->return_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] border {{ $badge['badge_class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
