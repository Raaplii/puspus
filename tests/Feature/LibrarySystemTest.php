<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarySystemTest extends TestCase
{
    use RefreshDatabase;

    private User $siswa;

    private User $petugas;

    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siswa = User::create([
            'name' => 'Budi Santoso',
            'email' => 'siswa@perpustakaan.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '20261001',
        ]);

        $this->petugas = User::create([
            'name' => 'Pak Joko Santoso',
            'email' => 'petugas@perpustakaan.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        $this->book = Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'category' => 'Sastra',
            'stock' => 5,
            'available_stock' => 5,
        ]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Perpustakaan Sekolah');
        $response->assertSee('siswa@perpustakaan.com');
        $response->assertSee('petugas@perpustakaan.com');
    }

    public function test_siswa_can_login_and_redirect_to_siswa_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'siswa@perpustakaan.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('siswa.dashboard'));
        $this->assertAuthenticatedAs($this->siswa);
    }

    public function test_petugas_can_login_and_redirect_to_petugas_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'petugas@perpustakaan.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs($this->petugas);
    }

    public function test_siswa_cannot_access_petugas_pages(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('petugas.dashboard'));
        $response->assertRedirect(route('siswa.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_petugas_cannot_access_siswa_pages(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('siswa.dashboard'));
        $response->assertRedirect(route('petugas.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_siswa_can_borrow_book_and_due_date_is_calculated(): void
    {
        $response = $this->actingAs($this->siswa)->post(route('siswa.borrow', $this->book), [
            'duration' => 7,
        ]);

        $response->assertRedirect(route('siswa.tenggat'));

        // Check loan was created
        $this->assertDatabaseHas('loans', [
            'user_id' => $this->siswa->id,
            'book_id' => $this->book->id,
            'status' => 'dipinjam',
        ]);

        // Check book stock was decremented
        $this->book->refresh();
        $this->assertEquals(4, $this->book->available_stock);

        // Check due date is 7 days from now
        $loan = Loan::where('user_id', $this->siswa->id)->first();
        $expectedDue = Carbon::today()->addDays(7)->toDateString();
        $this->assertEquals($expectedDue, $loan->due_date->toDateString());
    }

    public function test_siswa_can_return_borrowed_book(): void
    {
        // Setup active loan
        $loan = Loan::create([
            'user_id' => $this->siswa->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::today()->subDays(3)->toDateString(),
            'due_date' => Carbon::today()->addDays(4)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->book->update(['available_stock' => 4]);

        $response = $this->actingAs($this->siswa)->post(route('siswa.return', $loan));

        $response->assertRedirect(route('siswa.tenggat'));

        // Loan status updated
        $loan->refresh();
        $this->assertEquals('dikembalikan', $loan->status);
        $this->assertEquals(Carbon::today()->toDateString(), $loan->return_date->toDateString());

        // Book stock restored
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_stock);
    }

    public function test_siswa_can_view_deadlines(): void
    {
        Loan::create([
            'user_id' => $this->siswa->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::today()->toDateString(),
            'due_date' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.tenggat'));
        $response->assertStatus(200);
        $response->assertSee('Tenggat Waktu');
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('TENGGAT WAKTU');
    }

    public function test_petugas_can_supervise_students_who_returned_books(): void
    {
        Loan::create([
            'user_id' => $this->siswa->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::today()->subDays(5)->toDateString(),
            'due_date' => Carbon::today()->addDays(2)->toDateString(),
            'return_date' => Carbon::today()->toDateString(),
            'status' => 'dikembalikan',
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.returns'));
        $response->assertStatus(200);
        $response->assertSee('Pengawasan Siswa yang Sudah Mengumpulkan Buku');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Laskar Pelangi');
    }

    public function test_petugas_can_supervise_active_loan_deadlines(): void
    {
        Loan::create([
            'user_id' => $this->siswa->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => Carbon::today()->subDays(3)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.deadlines'));
        $response->assertStatus(200);
        $response->assertSee('Pengawasan Tenggat Waktu Buku yang Dipinjam');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Terlambat');
    }

    public function test_petugas_can_view_book_catalog(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.books'));
        $response->assertStatus(200);
        $response->assertSee('Koleksi Buku untuk Siswa');
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('Tambah Buku Baru');
    }

    public function test_petugas_can_access_create_book_page(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.books.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Buku untuk Siswa');
        $response->assertSee('Terbitkan untuk Siswa');
    }

    public function test_petugas_can_store_new_book_for_students(): void
    {
        $newBookData = [
            'title' => 'Belajar Laravel 11 Terlengkap',
            'author' => 'Taylor Otwell',
            'category' => 'Teknologi & IT',
            'publisher' => 'Laravel Press',
            'year' => 2024,
            'isbn' => '978-602-999-111-2',
            'stock' => 8,
            'description' => 'Buku panduan lengkap membangun aplikasi web modern.',
        ];

        $response = $this->actingAs($this->petugas)->post(route('petugas.books.store'), $newBookData);

        $response->assertRedirect(route('petugas.books'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('books', [
            'title' => 'Belajar Laravel 11 Terlengkap',
            'stock' => 8,
            'available_stock' => 8,
        ]);

        // Student can now see and borrow this book
        $studentResponse = $this->actingAs($this->siswa)->get(route('siswa.catalog'));
        $studentResponse->assertSee('Belajar Laravel 11 Terlengkap');
    }

    public function test_petugas_can_update_book(): void
    {
        $response = $this->actingAs($this->petugas)->put(route('petugas.books.update', $this->book), [
            'title' => 'Laskar Pelangi (Edisi Khusus)',
            'author' => 'Andrea Hirata',
            'category' => 'Sastra',
            'stock' => 10,
        ]);

        $response->assertRedirect(route('petugas.books'));
        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'title' => 'Laskar Pelangi (Edisi Khusus)',
            'stock' => 10,
            'available_stock' => 10,
        ]);
    }

    public function test_petugas_can_delete_book_without_active_loans(): void
    {
        $bookToDelete = Book::create([
            'title' => 'Buku Uji Hapus',
            'author' => 'Penulis Uji',
            'category' => 'Umum',
            'stock' => 2,
            'available_stock' => 2,
        ]);

        $response = $this->actingAs($this->petugas)->delete(route('petugas.books.destroy', $bookToDelete));

        $response->assertRedirect(route('petugas.books'));
        $this->assertDatabaseMissing('books', ['id' => $bookToDelete->id]);
    }

    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->siswa)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
