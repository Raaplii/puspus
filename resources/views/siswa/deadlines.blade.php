@extends('layouts.app')

@section('title', 'Tenggat Waktu & Peminjaman Saya - Siswa')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tenggat Waktu & Peminjaman Buku</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Periksa batas waktu pengembalian buku dan lakukan pengembalian secara mandiri.</p>
        </div>
        <a href="{{ route('siswa.catalog') }}" class="inline-flex items-center self-start px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
            <i class="fa-solid fa-plus mr-1.5"></i> Pinjam Buku Baru
        </a>
    </div>

    <!-- Active Loans (Buku Sedang Dipinjam & Tenggat Waktu) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center">
                        <i class="fa-solid fa-hourglass-start text-brand-600 mr-2"></i>
                        Peminjaman Aktif (Sedang Berjalan)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Segera kembalikan buku sebelum tanggal jatuh tempo untuk menjaga ketersediaan buku bagi siswa lain.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-brand-800 border border-sky-300">
                    {{ $activeLoans->count() }} Buku
                </span>
            </div>
        </div>

        @if($activeLoans->isEmpty())
            <div class="p-10 text-center">
                <i class="fa-solid fa-clipboard-check text-slate-300 text-4xl mb-3"></i>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada tanggungan peminjaman buku</h3>
                <p class="text-xs text-slate-400 mt-1">Semua buku yang Anda pinjam sudah dikembalikan atau Anda belum meminjam buku.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($activeLoans as $loan)
                    @php
                        $badge = $loan->getDeadlineBadge();
                        $diff = $loan->daysRemaining();
                    @endphp
                    <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 hover:bg-slate-50/70 transition">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-16 rounded-xl bg-gradient-to-tr from-sky-600 to-brand-800 text-white flex flex-col items-center justify-center text-xs shrink-0 shadow-md">
                                <i class="fa-solid fa-book text-lg mb-1"></i>
                                <span class="text-[9px] font-bold text-sky-200">BUKU</span>
                            </div>
                            <div class="space-y-1">
                                <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $loan->book->category }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900">{{ $loan->book->title }}</h3>
                                <p class="text-xs text-slate-500">
                                    Penulis: <span class="font-medium text-slate-700">{{ $loan->book->author }}</span> &bull; 
                                    Penerbit: {{ $loan->book->publisher ?? '-' }} ({{ $loan->book->year ?? '-' }})
                                </p>
                                <div class="pt-1 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                    <span><i class="fa-solid fa-calendar-day mr-1 text-slate-400"></i> Tanggal Pinjam: <strong>{{ $loan->loan_date->translatedFormat('d F Y') }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Due Date Showcase Card & Return Button -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 border-t md:border-t-0 pt-3 md:pt-0 shrink-0">
                            <!-- Prominent Due Date Box -->
                            <div class="p-3.5 rounded-xl border {{ $diff < 0 ? 'bg-rose-50 border-rose-300' : ($diff <= 2 ? 'bg-amber-50 border-amber-300' : 'bg-sky-50 border-sky-200') }} text-center sm:text-right min-w-[190px]">
                                <div class="text-[10px] font-bold uppercase tracking-wider {{ $diff < 0 ? 'text-rose-600' : ($diff <= 2 ? 'text-amber-700' : 'text-sky-700') }}">
                                    TENGGAT WAKTU (DEADLINE)
                                </div>
                                <div class="text-sm font-extrabold text-slate-900 mt-0.5">
                                    {{ $loan->due_date->translatedFormat('d F Y') }}
                                </div>
                                <div class="mt-1">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $badge['badge_class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Kembalikan -->
                            <form method="POST" action="{{ route('siswa.return', $loan) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku \'{{ $loan->book->title }}\'?')">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-4 py-3 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center space-x-1.5">
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

    <!-- Returned Books History (Riwayat Buku yang Sudah Dikembalikan) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-solid fa-clock-rotate-left text-slate-500 mr-2"></i>
                Riwayat Buku yang Sudah Dikembalikan
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat seluruh buku yang telah selesai Anda kembalikan ke perpustakaan.</p>
        </div>

        @if($historyLoans->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Belum ada riwayat pengembalian buku.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Judul Buku</th>
                            <th class="py-3 px-4">Tgl Pinjam</th>
                            <th class="py-3 px-4">Tenggat Waktu</th>
                            <th class="py-3 px-4">Tgl Dikembalikan</th>
                            <th class="py-3 px-4">Status Ketepatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($historyLoans as $hLoan)
                            @php
                                $badge = $hLoan->getDeadlineBadge();
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-semibold text-slate-800">
                                    {{ $hLoan->book->title }}
                                    <span class="block text-[11px] text-slate-400 font-normal">{{ $hLoan->book->author }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $hLoan->loan_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $hLoan->due_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 font-medium text-slate-700">
                                    {{ $hLoan->return_date ? $hLoan->return_date->translatedFormat('d M Y') : '-' }}
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

            <div class="p-4 border-t border-slate-100">
                {{ $historyLoans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

