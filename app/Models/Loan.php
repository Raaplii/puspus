<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if the active loan has passed its due date.
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'dikembalikan') {
            return $this->return_date ? $this->return_date->gt($this->due_date) : false;
        }

        return Carbon::now()->startOfDay()->gt($this->due_date->startOfDay());
    }

    /**
     * Number of days difference between today and due date.
     * Positive = days left, Negative = overdue days, 0 = today.
     */
    public function daysRemaining(): int
    {
        $today = Carbon::now()->startOfDay();
        $due = $this->due_date->copy()->startOfDay();

        return (int) $today->diffInDays($due, false);
    }

    /**
     * Human readable deadline label and badge styling info.
     *
     * @return array{label: string, badge_class: string, is_urgent: bool}
     */
    public function getDeadlineBadge(): array
    {
        if ($this->status === 'dikembalikan') {
            if ($this->return_date && $this->return_date->gt($this->due_date)) {
                $lateDays = $this->return_date->diffInDays($this->due_date);

                return [
                    'label' => "Dikembalikan (Terlambat {$lateDays} hari)",
                    'badge_class' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'is_urgent' => false,
                ];
            }

            return [
                'label' => 'Dikembalikan (Tepat Waktu)',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'is_urgent' => false,
            ];
        }

        $diff = $this->daysRemaining();

        if ($diff < 0) {
            $abs = abs($diff);

            return [
                'label' => "Terlambat {$abs} hari!",
                'badge_class' => 'bg-rose-100 text-rose-800 border-rose-300 animate-pulse font-semibold',
                'is_urgent' => true,
            ];
        }

        if ($diff === 0) {
            return [
                'label' => 'Jatuh tempo hari ini!',
                'badge_class' => 'bg-amber-100 text-amber-800 border-amber-400 font-semibold',
                'is_urgent' => true,
            ];
        }

        if ($diff <= 2) {
            return [
                'label' => "Tenggat: Sisa {$diff} hari lagi",
                'badge_class' => 'bg-amber-50 text-amber-800 border-amber-300',
                'is_urgent' => true,
            ];
        }

        return [
            'label' => "Tenggat: Sisa {$diff} hari lagi",
            'badge_class' => 'bg-sky-50 text-sky-800 border-sky-300',
            'is_urgent' => false,
        ];
    }
}
