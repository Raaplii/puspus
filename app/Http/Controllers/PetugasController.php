<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasController extends Controller
{
    /**
     * Dashboard overview for librarian / officer.
     */
    public function dashboard(): View
    {
        $today = Carbon::today();

        // Key stats
        $totalStudents = User::where('role', 'siswa')->count();
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $activeLoansCount = Loan::where('status', 'dipinjam')->count();
        $returnedLoansCount = Loan::where('status', 'dikembalikan')->count();

        // Overdue count (active loans where due_date < today)
        $overdueCount = Loan::where('status', 'dipinjam')
            ->where('due_date', '<', $today)
            ->count();

        // Due today count
        $dueTodayCount = Loan::where('status', 'dipinjam')
            ->whereDate('due_date', $today)
            ->count();

        // Urgent loans (overdue or due within 3 days)
        $urgentLoans = Loan::with(['user', 'book'])
            ->where('status', 'dipinjam')
            ->where('due_date', '<=', $today->copy()->addDays(3))
            ->orderBy('due_date', 'asc')
            ->take(6)
            ->get();

        // Recent returns by students
        $recentReturns = Loan::with(['user', 'book'])
            ->where('status', 'dikembalikan')
            ->orderByDesc('return_date')
            ->take(6)
            ->get();

        // Recent books added for students
        $recentBooks = Book::latest()->take(4)->get();

        return view('petugas.dashboard', compact(
            'totalStudents',
            'totalBooks',
            'totalStock',
            'activeLoansCount',
            'returnedLoansCount',
            'overdueCount',
            'dueTodayCount',
            'urgentLoans',
            'recentReturns',
            'recentBooks'
        ));
    }

    /**
     * Supervise students who have returned books (Mengawasi Siswa yang Sudah Mengumpulkan).
     */
    public function returns(Request $request): View
    {
        $search = $request->input('search');
        $timeliness = $request->input('timeliness'); // 'all', 'on_time', 'late'

        $query = Loan::with(['user', 'book'])
            ->where('status', 'dikembalikan');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        if ($timeliness === 'on_time') {
            $query->whereColumn('return_date', '<=', 'due_date');
        } elseif ($timeliness === 'late') {
            $query->whereColumn('return_date', '>', 'due_date');
        }

        $returns = $query->orderByDesc('return_date')->paginate(15)->withQueryString();

        // Summary counts
        $totalReturned = Loan::where('status', 'dikembalikan')->count();
        $onTimeCount = Loan::where('status', 'dikembalikan')->whereColumn('return_date', '<=', 'due_date')->count();
        $lateCount = Loan::where('status', 'dikembalikan')->whereColumn('return_date', '>', 'due_date')->count();

        return view('petugas.returns', compact('returns', 'search', 'timeliness', 'totalReturned', 'onTimeCount', 'lateCount'));
    }

    /**
     * Supervise active loans and see loan deadlines (Melihat Tenggat Waktu Buku yang Dipinjam).
     */
    public function deadlines(Request $request): View
    {
        $today = Carbon::today();
        $search = $request->input('search');
        $filter = $request->input('filter', 'all'); // 'all', 'overdue', 'due_today', 'urgent', 'safe'

        $query = Loan::with(['user', 'book'])
            ->where('status', 'dipinjam');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        if ($filter === 'overdue') {
            $query->where('due_date', '<', $today);
        } elseif ($filter === 'due_today') {
            $query->whereDate('due_date', $today);
        } elseif ($filter === 'urgent') {
            $query->where('due_date', '>=', $today)
                ->where('due_date', '<=', $today->copy()->addDays(3));
        } elseif ($filter === 'safe') {
            $query->where('due_date', '>', $today->copy()->addDays(3));
        }

        $loans = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();

        // Counts for badge navigation
        $totalActive = Loan::where('status', 'dipinjam')->count();
        $overdueCount = Loan::where('status', 'dipinjam')->where('due_date', '<', $today)->count();
        $dueTodayCount = Loan::where('status', 'dipinjam')->whereDate('due_date', $today)->count();
        $urgentCount = Loan::where('status', 'dipinjam')->where('due_date', '>=', $today)->where('due_date', '<=', $today->copy()->addDays(3))->count();

        return view('petugas.deadlines', compact(
            'loans',
            'search',
            'filter',
            'totalActive',
            'overdueCount',
            'dueTodayCount',
            'urgentCount'
        ));
    }

    /**
     * View books catalog & inventory for librarian.
     */
    public function books(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status'); // 'all', 'available', 'empty'

        $query = Book::with(['activeLoans.user']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (! empty($category) && $category !== 'Semua') {
            $query->where('category', $category);
        }

        if ($status === 'available') {
            $query->where('available_stock', '>', 0);
        } elseif ($status === 'empty') {
            $query->where('available_stock', '<=', 0);
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = Book::select('category')->distinct()->pluck('category');
        $totalBooks = Book::count();
        $totalAvailableStock = Book::sum('available_stock');
        $totalStock = Book::sum('stock');

        return view('petugas.books', compact('books', 'search', 'category', 'status', 'categories', 'totalBooks', 'totalAvailableStock', 'totalStock'));
    }

    /**
     * Show form to add a new book for students to borrow.
     */
    public function createBook(): View
    {
        $existingCategories = Book::select('category')->distinct()->pluck('category');

        return view('petugas.create_book', compact('existingCategories'));
    }

    /**
     * Store new book into library inventory for students.
     */
    public function storeBook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2030'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis/pengarang wajib diisi.',
            'category.required' => 'Kategori buku wajib diisi.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.min' => 'Jumlah stok minimal adalah 1 buku.',
        ]);

        $data['available_stock'] = $data['stock'];

        $book = Book::create($data);

        return redirect()->route('petugas.books')->with('success', "Buku '{$book->title}' berhasil ditambahkan! Siswa kini dapat melihat dan meminjam buku ini di katalog perpustakaan.");
    }

    /**
     * Show form to edit book details.
     */
    public function editBook(Book $book): View
    {
        $existingCategories = Book::select('category')->distinct()->pluck('category');

        return view('petugas.edit_book', compact('book', 'existingCategories'));
    }

    /**
     * Update book details in inventory.
     */
    public function updateBook(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2030'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis/pengarang wajib diisi.',
            'category.required' => 'Kategori buku wajib diisi.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.min' => 'Jumlah stok minimal adalah 1 buku.',
        ]);

        $activeLoansCount = $book->activeLoans()->count();
        if ($data['stock'] < $activeLoansCount) {
            return back()->withInput()->with('error', "Gagal update: Stok baru ({$data['stock']}) tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam siswa saat ini ({$activeLoansCount} buku).");
        }

        $data['available_stock'] = max(0, $data['stock'] - $activeLoansCount);

        $book->update($data);

        return redirect()->route('petugas.books')->with('success', "Data buku '{$book->title}' berhasil diperbarui!");
    }

    /**
     * Delete book from catalog.
     */
    public function destroyBook(Book $book): RedirectResponse
    {
        if ($book->activeLoans()->exists()) {
            return back()->with('error', "Buku '{$book->title}' tidak dapat dihapus karena saat ini sedang dipinjam oleh siswa!");
        }

        $title = $book->title;
        $book->delete();

        return redirect()->route('petugas.books')->with('success', "Buku '{$title}' berhasil dihapus dari katalog perpustakaan.");
    }

    /**
     * Officer manual return confirmation (if a student returns book directly to librarian).
     */
    public function confirmReturn(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status === 'dikembalikan') {
            return back()->with('error', 'Buku ini sudah tercatat dikembalikan.');
        }

        $today = Carbon::today();

        DB::transaction(function () use ($loan, $today) {
            $loan->update([
                'status' => 'dikembalikan',
                'return_date' => $today->toDateString(),
            ]);

            $loan->book()->increment('available_stock');
        });

        return back()->with('success', "Pengembalian buku '{$loan->book->title}' oleh {$loan->user->name} berhasil dikonfirmasi!");
    }
}
