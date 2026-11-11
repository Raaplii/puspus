<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Penjaga/Petugas Perpustakaan
        $petugas = User::create([
            'name' => 'Pak Joko Santoso (Petugas)',
            'email' => 'petugas@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '081234567890',
        ]);

        // 2. Akun Siswa Utama (Sesuai Permintaan)
        $siswa = User::create([
            'name' => 'Budi Santoso',
            'email' => 'siswa@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nis' => '20261001',
            'phone' => '085712345678',
        ]);

        // Akun Siswa Tambahan (agar tabel pengawasan petugas memiliki variasi data)
        $siswa2 = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'siti@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nis' => '20261002',
            'phone' => '087812345679',
        ]);

        $siswa3 = User::create([
            'name' => 'Ahmad Pratama',
            'email' => 'ahmad@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nis' => '20261003',
            'phone' => '089612345680',
        ]);

        // 3. Buku Perpustakaan
        $booksData = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'year' => 2005,
                'isbn' => '978-979-3062-79-2',
                'category' => 'Sastra & Fiksi',
                'stock' => 5,
                'available_stock' => 4,
                'description' => 'Kisah inspiratif tentang perjuangan sepuluh anak di Belitung dalam menggapai mimpi dan cita-cita di tengah keterbatasan sarana sekolah.',
            ],
            [
                'title' => 'Filosofi Teras: Panduan Stoikisme',
                'author' => 'Henry Manampiring',
                'publisher' => 'Kompas Gramedia',
                'year' => 2018,
                'isbn' => '978-602-412-518-9',
                'category' => 'Pengembangan Diri',
                'stock' => 4,
                'available_stock' => 3,
                'description' => 'Penerapan filsafat Yunani-Romawi Kuno Stoikisme untuk membantu hidup tenang, bebas emosi negatif, dan tangguh menghadapi tantangan.',
            ],
            [
                'title' => 'Pemrograman Web Modern dengan Laravel & PHP',
                'author' => 'Ir. Rahmat Hidayat, M.Kom',
                'publisher' => 'Informatika',
                'year' => 2024,
                'isbn' => '978-602-8758-12-3',
                'category' => 'Teknologi & IT',
                'stock' => 6,
                'available_stock' => 5,
                'description' => 'Buku panduan komprehensif merancang dan membangun aplikasi web skala enterprise menggunakan ekosistem Laravel modern.',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'publisher' => 'Hasta Mitra / Lentera Dipantara',
                'year' => 1980,
                'isbn' => '978-979-97312-3-4',
                'category' => 'Sastra & Fiksi',
                'stock' => 4,
                'available_stock' => 3,
                'description' => 'Mahakarya sastra Indonesia yang mengisahkan pergulatan Minke di era Hindia Belanda awal abad ke-20.',
            ],
            [
                'title' => 'Fisika Dasar untuk Sains dan Teknik',
                'author' => 'Prof. Dr. Bambang Sutrisno',
                'publisher' => 'Erlangga',
                'year' => 2022,
                'isbn' => '978-979-015-889-1',
                'category' => 'Sains & Sains Terapan',
                'stock' => 7,
                'available_stock' => 7,
                'description' => 'Membedah konsep mekanika fluida, termodinamika, gelombang optik, dan elektrodinamika disertai latihan soal aplikatif.',
            ],
            [
                'title' => 'Struktur Data dan Algoritma Lanjutan',
                'author' => 'Eko Kurniawan Khannedy',
                'publisher' => 'Andi Publisher',
                'year' => 2023,
                'isbn' => '978-623-01-2345-6',
                'category' => 'Teknologi & IT',
                'stock' => 5,
                'available_stock' => 5,
                'description' => 'Penjelasan mendalam mengenai pohon biner, graf, teknik dynamic programming, dan kompleksitas waktu algoritma.',
            ],
            [
                'title' => 'Sejarah Lengkap Bangsa Indonesia',
                'author' => 'Drs. Supriyanto, M.Pd',
                'publisher' => 'Pustaka Pelajar',
                'year' => 2021,
                'isbn' => '978-602-229-432-1',
                'category' => 'Sejarah & Budaya',
                'stock' => 3,
                'available_stock' => 3,
                'description' => 'Rekam jejak perjalanan peradaban Nusantara dari masa kerajaan kuno, era kolonial, hingga proklamasi kemerdekaan.',
            ],
            [
                'title' => 'Negeri 5 Menara',
                'author' => 'Ahmad Fuadi',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => 2009,
                'isbn' => '978-979-22-4861-6',
                'category' => 'Sastra & Fiksi',
                'stock' => 5,
                'available_stock' => 5,
                'description' => 'Perjalanan enam santri dari berbagai daerah di Pondok Madani dengan filosofi Man Jadda Wajada yang memukau.',
            ],
        ];

        $books = [];
        foreach ($booksData as $b) {
            $books[] = Book::create($b);
        }

        $today = Carbon::today();

        // 4. Data Peminjaman untuk Siswa Contoh (Budi Santoso)
        // Loan 1: Sedang dipinjam oleh Budi, tenggat 5 hari lagi (Aman)
        Loan::create([
            'user_id' => $siswa->id,
            'book_id' => $books[0]->id, // Laskar Pelangi
            'loan_date' => $today->copy()->subDays(2)->toDateString(),
            'due_date' => $today->copy()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
            'notes' => 'Peminjaman rutin untuk tugas resensi',
        ]);

        // Loan 2: Sudah dikembalikan oleh Budi (Tepat Waktu)
        Loan::create([
            'user_id' => $siswa->id,
            'book_id' => $books[3]->id, // Bumi Manusia
            'loan_date' => $today->copy()->subDays(10)->toDateString(),
            'due_date' => $today->copy()->subDays(3)->toDateString(),
            'return_date' => $today->copy()->subDays(4)->toDateString(),
            'status' => 'dikembalikan',
            'notes' => 'Kondisi buku baik dan rapi',
        ]);

        // 5. Data Peminjaman Siswa 2 (Siti Rahmawati)
        // Loan 3: Sedang dipinjam oleh Siti, jatuh tempo BESOK (Mendekati tenggat)
        Loan::create([
            'user_id' => $siswa2->id,
            'book_id' => $books[1]->id, // Filosofi Teras
            'loan_date' => $today->copy()->subDays(6)->toDateString(),
            'due_date' => $today->copy()->addDays(1)->toDateString(),
            'status' => 'dipinjam',
            'notes' => 'Buku pengembangan diri',
        ]);

        // Loan 4: Sudah dikembalikan oleh Siti (Tepat waktu)
        Loan::create([
            'user_id' => $siswa2->id,
            'book_id' => $books[6]->id, // Sejarah Lengkap
            'loan_date' => $today->copy()->subDays(12)->toDateString(),
            'due_date' => $today->copy()->subDays(5)->toDateString(),
            'return_date' => $today->copy()->subDays(5)->toDateString(),
            'status' => 'dikembalikan',
            'notes' => 'Dikembalikan tepat pada hari tenggat waktu',
        ]);

        // 6. Data Peminjaman Siswa 3 (Ahmad Pratama)
        // Loan 5: Sedang dipinjam oleh Ahmad, TERLAMBAT 3 HARI (Untuk melihat fitur peringatan tenggat waktu di petugas!)
        Loan::create([
            'user_id' => $siswa3->id,
            'book_id' => $books[2]->id, // Laravel
            'loan_date' => $today->copy()->subDays(10)->toDateString(),
            'due_date' => $today->copy()->subDays(3)->toDateString(),
            'status' => 'dipinjam',
            'notes' => 'Peminjaman untuk tugas akhir web',
        ]);

        // Loan 6: Sudah dikembalikan oleh Ahmad (Tetapi terlambat 2 hari)
        Loan::create([
            'user_id' => $siswa3->id,
            'book_id' => $books[7]->id, // Negeri 5 Menara
            'loan_date' => $today->copy()->subDays(15)->toDateString(),
            'due_date' => $today->copy()->subDays(8)->toDateString(),
            'return_date' => $today->copy()->subDays(6)->toDateString(),
            'status' => 'dikembalikan',
            'notes' => 'Terlambat 2 hari karena izin sakit',
        ]);
    }
}
