@extends('layouts.app')

@section('title', 'Tambah Buku untuk Siswa - Petugas Perpustakaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('petugas.books') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Katalog Buku
        </a>
        <span class="text-xs bg-brand-50 text-brand-700 font-bold px-2.5 py-1 rounded-lg border border-brand-200">
            <i class="fa-solid fa-book-medical mr-1"></i> Form Tambah Buku
        </span>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-slate-900 to-brand-900 p-6 sm:p-8 text-white relative overflow-hidden">
            <div class="absolute right-0 top-0 bottom-0 opacity-10 pointer-events-none flex items-center pr-8">
                <i class="fa-solid fa-book-bookmark text-[140px]"></i>
            </div>
            <div class="relative z-10">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 mb-2">
                    <i class="fa-solid fa-plus-circle mr-1.5"></i> Tambah Koleksi Baru
                </span>
                <h1 class="text-2xl font-extrabold tracking-tight">Tambah Buku untuk Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                    Masukkan informasi buku baru perpustakaan. Buku yang Anda tambahkan di sini akan langsung tampil pada katalog siswa dan siap untuk dipinjam oleh para siswa.
                </p>
            </div>
        </div>

        <!-- Form Body -->
        <form method="POST" action="{{ route('petugas.books.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Judul Buku -->
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Buku <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-heading text-sm"></i>
                    </div>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                        placeholder="Contoh: Pemrograman Web Modern dengan Laravel"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition @error('title') border-rose-400 bg-rose-50 @enderror">
                </div>
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
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user-pen text-sm"></i>
                        </div>
                        <input type="text" id="author" name="author" value="{{ old('author') }}" required
                            placeholder="Contoh: Andrea Hirata"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition @error('author') border-rose-400 bg-rose-50 @enderror">
                    </div>
                    @error('author')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Buku <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-tags text-sm"></i>
                        </div>
                        <input type="text" id="category" name="category" value="{{ old('category') }}" required
                            placeholder="Ketik atau pilih kategori di bawah"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition @error('category') border-rose-400 bg-rose-50 @enderror">
                    </div>
                    @error('category')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Quick Category Pills -->
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span class="text-[10px] text-slate-400 font-medium self-center mr-1">Pilih Cepat:</span>
                        @foreach(['Sastra & Fiksi', 'Teknologi & IT', 'Pengembangan Diri', 'Sains', 'Sejarah & Budaya', 'Pelajaran'] as $catSuggestion)
                            <button type="button" onclick="document.getElementById('category').value = '{{ $catSuggestion }}'"
                                class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 hover:bg-brand-50 hover:text-brand-700 border border-slate-200 transition">
                                {{ $catSuggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Penerbit, Tahun, Stok -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="publisher" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Penerbit
                    </label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher') }}"
                        placeholder="Contoh: Gramedia"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tahun Terbit
                    </label>
                    <input type="number" id="year" name="year" value="{{ old('year', date('Y')) }}" min="1900" max="2035"
                        placeholder="Contoh: 2024"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jumlah Stok Buku <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 5) }}" min="1" max="1000" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition @error('stock') border-rose-400 bg-rose-50 @enderror">
                        <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-medium">eksemplar</span>
                    </div>
                    @error('stock')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nomor ISBN (Opsional)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-barcode text-sm"></i>
                    </div>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn') }}"
                        placeholder="Contoh: 978-602-000-000-0"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>
            </div>

            <!-- Deskripsi / Sinopsis -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Sinopsis / Deskripsi Ringkas Buku
                </label>
                <textarea id="description" name="description" rows="3"
                    placeholder="Tuliskan gambaran ringkas mengenai topik atau isi buku untuk membantu siswa saat memilih buku..."
                    class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition">{{ old('description') }}</textarea>
            </div>

            <!-- Submit Button & Cancel -->
            <div class="pt-5 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('petugas.books') }}" class="w-full sm:w-auto px-5 py-2.5 text-slate-600 hover:text-slate-800 text-center font-bold text-xs rounded-xl hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl shadow-md shadow-brand-500/20 active:scale-95 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Simpan & Terbitkan untuk Siswa</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
