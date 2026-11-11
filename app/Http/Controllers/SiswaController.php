<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Display student dashboard with active loans and deadlines summary.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Active loans with due dates
        $activeLoans = Loan::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->orderBy('due_date', 'asc')
            ->get();

        // History of returned books
        $returnedLoans = Loan::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dikembalikan')
            ->orderByDesc('return_date')
            ->take(5)
            ->get();

        // Featured / recommended books
        $availableBooks = Book::where('available_stock', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Counts
        $activeCount = $activeLoans->count();
        $returnedCount = Loan::where('user_id', $user->id)->where('status', 'dikembalikan')->count();
        $overdueCount = $activeLoans->filter(fn ($loan) => $loan->daysRemaining() < 0)->count();

        return view('siswa.dashboard', compact(
            'user',
            'activeLoans',
            'returnedLoans',
            'availableBooks',
            'activeCount',
            'returnedCount',
            'overdueCount'
        ));
    }

    /**
     * Display the book catalog for students to browse and borrow.
     */
    public function catalog(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Book::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (! empty($category) && $category !== 'Semua') {
            $query->where('category', $category);
        }

        $books = $query->orderBy('title', 'asc')->paginate(12)->withQueryString();

        // Active borrowed book IDs by this user (to disable button or indicate "sedang dipinjam")
        $borrowedBookIds = Loan::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->pluck('book_id')
            ->toArray();

        $categories = Book::select('category')->distinct()->pluck('category');

        return view('siswa.catalog', compact('books', 'categories', 'search', 'category', 'borrowedBookIds'));
    }

    /**
     * Action to borrow a book.
     */
    public function borrow(Request $request, Book $book): RedirectResponse
    {
        $user = Auth::user();

        // 1. Check max active loans limit (e.g. 3)
        $activeLoansCount = Loan::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->count();

        if ($activeLoansCount >= 3) {
            return back()->with('error', 'Gagal meminjam: Anda sudah mencapai batas maksimal peminjaman (3 buku). Silakan kembalikan buku terlebih dahulu.');
        }

        // 2. Check if student already borrows this specific book
        $alreadyBorrowed = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'dipinjam')
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('error', "Gagal meminjam: Anda sedang meminjam buku '{$book->title}'. Harap kembalikan buku ini sebelum meminjam kembali.");
        }

        // 3. Check stock
        if ($book->available_stock < 1) {
            return back()->with('error', "Maaf, stok buku '{$book->title}' sedang kosong/habis dipinjam.");
        }

        // Validate duration (default 7 days, allowed 3, 7, 14 days)
        $days = (int) $request->input('duration', 7);
        if (! in_array($days, [3, 7, 14], true)) {
            $days = 7;
        }

        $today = Carbon::today();
        $dueDate = Carbon::today()->addDays($days);

        DB::transaction(function () use ($user, $book, $today, $dueDate) {
            // Decrement available stock
            $book->decrement('available_stock');

            // Create loan record
            Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'loan_date' => $today->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'dipinjam',
            ]);
        });

        $formattedDueDate = $dueDate->translatedFormat('d F Y');

        return redirect()->route('siswa.tenggat')->with('success', "Buku '{$book->title}' berhasil dipinjam! Tenggat waktu pengembalian adalah {$formattedDueDate} (durasi {$days} hari).");
    }

    /**
     * Action to return a book.
     */
    public function returnBook(Request $request, Loan $loan): RedirectResponse
    {
        $user = Auth::user();

        // Verify ownership
        if ($loan->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak mengembalikan peminjaman ini.');
        }

        if ($loan->status === 'dikembalikan') {
            return back()->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        $today = Carbon::today();

        DB::transaction(function () use ($loan, $today) {
            $loan->update([
                'status' => 'dikembalikan',
                'return_date' => $today->toDateString(),
            ]);

            // Restore book stock
            $loan->book()->increment('available_stock');
        });

        $returnDateFormatted = $today->translatedFormat('d F Y');
        $message = "Buku '{$loan->book->title}' berhasil dikembalikan pada {$returnDateFormatted}. Terima kasih!";

        if ($loan->return_date && $loan->return_date->gt($loan->due_date)) {
            $diff = $loan->return_date->diffInDays($loan->due_date);
            $message .= " (Catatan: Pengembalian terlambat {$diff} hari dari tenggat waktu).";
        }

        return redirect()->route('siswa.tenggat')->with('success', $message);
    }

    /**
     * Dedicated page for student to view loan deadlines and history.
     */
    public function deadlines(): View
    {
        $user = Auth::user();

        $activeLoans = Loan::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->orderBy('due_date', 'asc')
            ->get();

        $historyLoans = Loan::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'dikembalikan')
            ->orderByDesc('return_date')
            ->paginate(10);

        return view('siswa.deadlines', compact('user', 'activeLoans', 'historyLoans'));
    }
}
