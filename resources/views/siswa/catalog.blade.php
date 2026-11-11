@extends('layouts.app')

@section('title', 'Katalog Buku - Sistem Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Header & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="max-w-2xl">
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Katalog Buku Perpustakaan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Cari dan pinjam buku untuk menunjang kegiatan belajar Anda.</p>
        </div>

        <form method="GET" action="{{ route('siswa.catalog') }}" class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-8 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari berdasarkan judul buku, pengarang, atau ISBN..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>

            <div class="md:col-span-3">
                <select name="category" onchange="this.form.submit()"
                    class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl shadow-sm transition">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Books Grid -->
    @if($books->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                <i class="fa-solid fa-book-skull"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700">Tidak ada buku yang ditemukan</h3>
            <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang berbeda atau reset filter kategori.</p>
            <a href="{{ route('siswa.catalog') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                Reset Pencarian
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                @php
                    $isBorrowingThis = in_array($book->id, $borrowedBookIds, true);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-5 sm:p-6">
                        <!-- Category & Stock Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-brand-700 border border-sky-200">
                                {{ $book->category }}
                            </span>
                            @if($book->available_stock > 0)
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Tersedia: {{ $book->available_stock }} / {{ $book->stock }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fa-solid fa-circle-xmark text-[10px] mr-1"></i> Habis Dipinjam
                                </span>
                            @endif
                        </div>

                        <!-- Title & Author -->
                        <h3 class="text-base font-bold text-slate-900 line-clamp-1" title="{{ $book->title }}">{{ $book->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Oleh: <strong class="text-slate-700 font-medium">{{ $book->author }}</strong>
                            @if($book->year) &bull; Tahun {{ $book->year }} @endif
                        </p>

                        <!-- Synopsis -->
                        <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                            {{ $book->description ?: 'Buku perpustakaan ini tersedia untuk dipinjam siswa guna referensi dan literasi.' }}
                        </p>

                        <!-- ISBN / Details -->
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>ISBN: {{ $book->isbn ?? 'N/A' }}</span>
                            <span>{{ $book->publisher ?? 'Penerbit Umum' }}</span>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        @if($isBorrowingThis)
                            <div class="w-full py-2.5 px-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-center text-xs font-semibold flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-hourglass-half"></i>
                                <span>Sedang Anda Pinjam</span>
                            </div>
                        @elseif($book->available_stock > 0)
                            <form method="POST" action="{{ route('siswa.borrow', $book) }}" class="space-y-2">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <select name="duration" class="w-1/2 py-2 px-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-700 focus:ring-1 focus:ring-brand-500 outline-none">
                                        <option value="3">Durasi: 3 Hari</option>
                                        <option value="7" selected>Durasi: 7 Hari (Standar)</option>
                                        <option value="14">Durasi: 14 Hari</option>
                                    </select>
                                    <button type="submit" class="w-1/2 py-2 px-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center space-x-1"
                                        onclick="return confirm('Konfirmasi peminjaman buku \'{{ $book->title }}\'?')">
                                        <i class="fa-solid fa-plus text-[11px]"></i>
                                        <span>Pinjam Buku</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <button type="button" disabled class="w-full py-2.5 px-3 bg-slate-200 text-slate-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                                Stok Saat Ini Kosong
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection

