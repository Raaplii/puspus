@extends('layouts.app')

@section('title', 'Edit Buku - Petugas Perpustakaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('petugas.books') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Katalog Buku
        </a>
        <span class="text-xs bg-amber-50 text-amber-700 font-bold px-2.5 py-1 rounded-lg border border-amber-200">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data Buku
        </span>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-slate-900 to-amber-900 p-6 sm:p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 mb-2">
                    <i class="fa-solid fa-pen mr-1.5"></i> Perbarui Informasi
                </span>
                <h1 class="text-2xl font-extrabold tracking-tight">Edit Buku: {{ $book->title }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Perbarui judul, pengarang, kategori, atau jumlah stok fisik buku perpustakaan.
                </p>
            </div>
        </div>

        <!-- Form Body -->
        <form method="POST" action="{{ route('petugas.books.update', $book) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Buku -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Buku <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition @error('title') border-rose-400 bg-rose-50 @enderror">
                @error('title')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penulis & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="author" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Penulis / Pengarang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition @error('author') border-rose-400 bg-rose-50 @enderror">
                    @error('author')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Buku <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="category" name="category" value="{{ old('category', $book->category) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition @error('category') border-rose-400 bg-rose-50 @enderror">
                    @error('category')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Penerbit, Tahun, Stok -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="publisher" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Penerbit
                    </label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tahun Terbit
                    </label>
                    <input type="number" id="year" name="year" value="{{ old('year', $book->year) }}" min="1900" max="2035"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jumlah Stok Buku <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $book->stock) }}" min="1" max="1000" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition @error('stock') border-rose-400 bg-rose-50 @enderror">
                        <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-medium">eksemplar</span>
                    </div>
                    @error('stock')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Saat ini tersedia: {{ $book->available_stock }} dari {{ $book->stock }} eks</p>
                </div>
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nomor ISBN
                </label>
                <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
            </div>

            <!-- Deskripsi / Sinopsis -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Sinopsis / Deskripsi Ringkas Buku
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">{{ old('description', $book->description) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-5 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('petugas.books') }}" class="w-full sm:w-auto px-5 py-2.5 text-slate-600 hover:text-slate-800 text-center font-bold text-xs rounded-xl hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-md active:scale-95 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
