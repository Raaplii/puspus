@extends('layouts.app')

@section('title', 'Katalog & Kelola Buku - Petugas Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Buttons -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-200 mb-2">
                    <i class="fa-solid fa-book-bookmark mr-1.5 text-brand-600"></i> Inventaris Buku Perpustakaan
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Katalog & Koleksi Buku untuk Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                    Petugas dapat melihat seluruh daftar buku perpustakaan dan menambahkan buku-buku baru agar dapat dipinjam oleh para siswa.
                </p>
            </div>

            <!-- Action: Tambah Buku -->
            <div class="flex flex-wrap items-center gap-2.5 self-start md:self-center shrink-0">
                <a href="{{ route('petugas.books.create') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-brand-500/20 transition">
                    <i class="fa-solid fa-plus-circle mr-2 text-base"></i> Tambah Buku Baru
                </a>
                <button type="button" onclick="document.getElementById('modalTambahBuku').classList.remove('hidden')"
                    class="inline-flex items-center px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition">
                    <i class="fa-solid fa-bolt mr-1.5 text-amber-500"></i> Tambah Cepat
                </button>
            </div>
        </div>

        <!-- Mini Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mt-6 pt-6 border-t border-slate-100">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-books"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Judul Buku</span>
                    <span class="text-xl font-extrabold text-slate-900">{{ $totalBooks }} Judul</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Stok Tersedia untuk Dipinjam</span>
                    <span class="text-xl font-extrabold text-emerald-800">{{ $totalAvailableStock }} eks</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-200/70 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-sky-700 uppercase tracking-wider block">Total Fisik Buku</span>
                    <span class="text-xl font-extrabold text-sky-900">{{ $totalStock }} eks</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('petugas.books') }}" class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari judul buku, nama penulis, ISBN, atau penerbit..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>

            <div class="md:col-span-3">
                <select name="category" onchange="this.form.submit()"
                    class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status" onchange="this.form.submit()"
                    class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                    <option value="" {{ empty($status) ? 'selected' : '' }}>Semua Status</option>
                    <option value="available" {{ $status === 'available' ? 'selected' : '' }}>Hanya Tersedia</option>
                    <option value="empty" {{ $status === 'empty' ? 'selected' : '' }}>Sedang Habis</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-sm transition">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Judul Buku & Penulis</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Penerbit & Tahun</th>
                        <th class="py-3.5 px-4">ISBN</th>
                        <th class="py-3.5 px-4">Ketersediaan Stok</th>
                        <th class="py-3.5 px-4">Peminjaman Siswa</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($books as $idx => $b)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $books->firstItem() + $idx }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 text-sm block">{{ $b->title }}</span>
                                <span class="text-[11px] text-slate-500">Penulis: <strong class="text-slate-700">{{ $b->author }}</strong></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold border border-slate-200">
                                    {{ $b->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $b->publisher ?? '-' }} ({{ $b->year ?? '-' }})
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                {{ $b->isbn ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-bold {{ $b->available_stock > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $b->available_stock }}
                                    </span>
                                    <span class="text-slate-400">/ {{ $b->stock }} eks</span>
                                </div>
                                <div class="mt-1">
                                    @if($b->available_stock > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px] mr-1"></i> Siap Dipinjam
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-ban text-[9px] mr-1"></i> Sedang Habis
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($b->activeLoans->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($b->activeLoans as $activeLoan)
                                            <div class="text-[11px] text-slate-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100 flex items-center justify-between">
                                                <span class="font-medium truncate max-w-[120px]">{{ $activeLoan->user->name }}</span>
                                                <span class="text-[10px] text-sky-800 font-mono ml-1">{{ $activeLoan->due_date->format('d/m') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tidak ada pinjaman</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    <!-- Edit Button -->
                                    <a href="{{ route('petugas.books.edit', $b) }}"
                                        class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                        title="Edit Buku">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    <!-- Delete Button (Only if no active loans) -->
                                    @if($b->activeLoans->isEmpty())
                                        <form method="POST" action="{{ route('petugas.books.destroy', $b) }}" onsubmit="return confirm('Hapus buku \'{{ $b->title }}\' dari katalog perpustakaan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Buku">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="p-2 text-slate-300 cursor-not-allowed" title="Buku sedang dipinjam siswa, tidak dapat dihapus">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <i class="fa-solid fa-book-open text-4xl mb-3 text-slate-300"></i>
                                <h3 class="text-sm font-bold text-slate-700">Tidak ada buku yang ditemukan</h3>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau tambahkan buku baru untuk siswa.</p>
                                <a href="{{ route('petugas.books.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition">
                                    <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Buku Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $books->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Buku Cepat -->
<div id="modalTambahBuku" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 {{ $errors->any() ? '' : 'hidden' }}">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 animate-scale-up">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-900 to-brand-900 text-white">
            <div>
                <h3 class="font-bold text-base">Tambah Buku Cepat untuk Siswa</h3>
                <p class="text-[11px] text-slate-300">Buku akan langsung dapat dipinjam siswa</p>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahBuku').classList.add('hidden')" class="text-slate-300 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('petugas.books.store') }}" class="p-5 space-y-3.5 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Belajar Python untuk Pemula"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Penulis <span class="text-rose-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author') }}" required placeholder="Contoh: Budi Susanto"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category') }}" required placeholder="Contoh: Teknologi & IT"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="Contoh: Erlangga"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun Terbit</label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" min="1900" max="2035"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok Fisik <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 5) }}" min="1" max="1000" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none font-bold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="Contoh: 978-602-000-000-0"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Sinopsis Singkat</label>
                <textarea name="description" rows="2" placeholder="Deskripsi ringkas mengenai isi buku..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-1 focus:ring-brand-500 outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalTambahBuku').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-sm transition">
                    Simpan Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
