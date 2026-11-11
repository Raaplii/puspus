@extends('layouts.app')

@section('title', 'Pengawasan Siswa yang Sudah Mengumpulkan - Petugas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-2">
                    <i class="fa-solid fa-clipboard-check mr-1.5 text-emerald-600"></i> Tugas Pengawasan Penjaga
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengawasan Siswa yang Sudah Mengumpulkan Buku</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Memantau data riwayat siswa yang telah mengembalikan buku pinjaman beserta evaluasi ketepatan waktu pengembalian.
                </p>
            </div>

            <!-- Summary Badges -->
            <div class="flex items-center gap-2 self-start md:self-center shrink-0">
                <div class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Kembali</span>
                    <span class="text-base font-extrabold text-slate-800">{{ $totalReturned }}</span>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                    <span class="text-[10px] text-emerald-600 font-bold uppercase block">Tepat Waktu</span>
                    <span class="text-base font-extrabold text-emerald-700">{{ $onTimeCount }}</span>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-center">
                    <span class="text-[10px] text-amber-600 font-bold uppercase block">Terlambat</span>
                    <span class="text-base font-extrabold text-amber-700">{{ $lateCount }}</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('petugas.returns') }}" class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-3 pt-5 border-t border-slate-100">
            <div class="md:col-span-8 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari berdasarkan nama siswa, NIS, judul buku, atau ISBN..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>

            <div class="md:col-span-3">
                <select name="timeliness" onchange="this.form.submit()"
                    class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    <option value="" {{ empty($timeliness) ? 'selected' : '' }}>Semua Ketepatan Waktu</option>
                    <option value="on_time" {{ $timeliness === 'on_time' ? 'selected' : '' }}>Hanya Tepat Waktu</option>
                    <option value="late" {{ $timeliness === 'late' ? 'selected' : '' }}>Hanya Terlambat Dikembalikan</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Returns -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        @if($returns->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <i class="fa-solid fa-inbox text-4xl mb-3 text-slate-300"></i>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada data pengembalian</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan siswa yang mengembalikan buku sesuai kriteria pencarian ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">No</th>
                            <th class="py-3.5 px-4">Siswa (Peminjam)</th>
                            <th class="py-3.5 px-4">Buku yang Dipinjam</th>
                            <th class="py-3.5 px-4">Tgl Pinjam</th>
                            <th class="py-3.5 px-4">Tenggat Waktu</th>
                            <th class="py-3.5 px-4">Tgl Dikembalikan</th>
                            <th class="py-3.5 px-4">Status Pengembalian</th>
                            <th class="py-3.5 px-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($returns as $idx => $loan)
                            @php
                                $badge = $loan->getDeadlineBadge();
                                $isLate = $loan->return_date && $loan->return_date->gt($loan->due_date);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $returns->firstItem() + $idx }}</td>
                                <td class="py-3.5 px-4">
                                    <strong class="text-slate-900 font-bold block text-sm">{{ $loan->user->name }}</strong>
                                    <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                        <span>NIS: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">{{ $loan->user->nis ?? '-' }}</code></span>
                                        <span>&bull;</span>
                                        <span>{{ $loan->user->email }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 text-sm block">{{ $loan->book->title }}</span>
                                    <span class="text-[11px] text-slate-500">Kategori: {{ $loan->book->category }} &bull; {{ $loan->book->author }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $loan->loan_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $loan->due_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <i class="fa-solid fa-calendar-check text-emerald-600 mr-1"></i>
                                    {{ $loan->return_date ? $loan->return_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold border {{ $badge['badge_class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px] max-w-xs">
                                    {{ $loan->notes ?: 'Telah dikonfirmasi' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

