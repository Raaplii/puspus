@extends('layouts.app')

@section('title', 'Pengawasan Tenggat Waktu Buku - Petugas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 mb-2">
                    <i class="fa-solid fa-calendar-clock mr-1.5 text-amber-600"></i> Tugas Pengawasan Penjaga
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengawasan Tenggat Waktu Buku yang Dipinjam</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pantau seluruh buku yang sedang berada di tangan siswa serta hitungan mundur tanggal batas pengembalian buku.
                </p>
            </div>

            <!-- Quick Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 self-start md:self-center">
                <a href="{{ route('petugas.deadlines', ['filter' => 'all', 'search' => $search]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Aktif ({{ $totalActive }})
                </a>
                <a href="{{ route('petugas.deadlines', ['filter' => 'overdue', 'search' => $search]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'overdue' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Terlambat ({{ $overdueCount }})
                </a>
                <a href="{{ route('petugas.deadlines', ['filter' => 'due_today', 'search' => $search]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'due_today' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                    Jatuh Tempo Hari Ini ({{ $dueTodayCount }})
                </a>
                <a href="{{ route('petugas.deadlines', ['filter' => 'urgent', 'search' => $search]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'urgent' ? 'bg-sky-700 text-white' : 'bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200' }}">
                    Mendekati Tenggat ({{ $urgentCount }})
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('petugas.deadlines') }}" class="mt-6 flex flex-col sm:flex-row gap-3 pt-5 border-t border-slate-100">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari nama siswa, NIS, atau judul buku..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm transition">
                Cari Data
            </button>
            @if(!empty($search) || $filter !== 'all')
                <a href="{{ route('petugas.deadlines') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs sm:text-sm rounded-xl transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Active Loans Deadlines Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        @if($loans->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <i class="fa-solid fa-calendar-check text-4xl mb-3 text-emerald-400"></i>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada peminjaman dalam kategori ini</h3>
                <p class="text-xs text-slate-400 mt-1">Semua peminjaman telah dikembalikan atau tidak ada data yang cocok dengan filter yang dipilih.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">No</th>
                            <th class="py-3.5 px-4">Siswa Peminjam</th>
                            <th class="py-3.5 px-4">Buku yang Dipinjam</th>
                            <th class="py-3.5 px-4">Tanggal Pinjam</th>
                            <th class="py-3.5 px-4">Tenggat Waktu (Due Date)</th>
                            <th class="py-3.5 px-4">Sisa Waktu / Keterlambatan</th>
                            <th class="py-3.5 px-4 text-right">Aksi Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($loans as $idx => $loan)
                            @php
                                $badge = $loan->getDeadlineBadge();
                                $diff = $loan->daysRemaining();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition {{ $diff < 0 ? 'bg-rose-50/30' : '' }}">
                                <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $loans->firstItem() + $idx }}</td>
                                <td class="py-3.5 px-4">
                                    <strong class="text-slate-900 font-bold block text-sm">{{ $loan->user->name }}</strong>
                                    <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                        <span>NIS: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">{{ $loan->user->nis ?? '-' }}</code></span>
                                        <span>&bull;</span>
                                        <span><i class="fa-solid fa-phone text-[9px] mr-0.5"></i> {{ $loan->user->phone ?? $loan->user->email }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 text-sm block">{{ $loan->book->title }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $loan->book->author }} &bull; ISBN: {{ $loan->book->isbn ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $loan->loan_date->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-sm font-extrabold {{ $diff < 0 ? 'text-rose-600' : 'text-slate-900' }} block">
                                        {{ $loan->due_date->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">Jatuh Tempo Pengembalian</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="inline-flex items-center px-3 py-1.5 rounded-lg border text-xs font-semibold {{ $badge['badge_class'] }}">
                                        <i class="fa-solid {{ $diff < 0 ? 'fa-triangle-exclamation mr-1.5' : ($diff <= 2 ? 'fa-hourglass-half mr-1.5' : 'fa-clock mr-1.5') }}"></i>
                                        <span>{{ $badge['label'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <!-- Aksi Terima Pengembalian di Meja Petugas -->
                                    <form method="POST" action="{{ route('petugas.confirmReturn', $loan) }}"
                                        onsubmit="return confirm('Konfirmasi penerimaan pengembalian buku \'{{ $loan->book->title }}\' dari siswa {{ $loan->user->name }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-lg shadow-sm transition flex items-center space-x-1.5 ml-auto">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Terima Buku</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

