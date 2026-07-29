<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'book_id',
        'borrowed_date',
        'returned_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'returned_date' => 'date',
        'due_date' => 'date',
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function book(){
        return $this->belongsTo(Book::class);
    }

    //Check if borrowing is overdue

    public function isOverdue(): bool{
        return $this->due_date < Carbon::today() && $this->status === 'borrowed';
    }
}
