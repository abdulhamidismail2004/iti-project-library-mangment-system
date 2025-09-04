<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id', 
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime', 
        'returned_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Helper methods
    public function isOverdue()
    {
        if ($this->status === 'returned') {
            return false;
        }
        
        return Carbon::now()->isAfter($this->due_date);
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->status === 'returned') {
            return null;
        }
        
        $now = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);
        
        if ($now->isAfter($dueDate)) {
            return -$now->diffInDays($dueDate); // Negative for overdue
        }
        
        return $now->diffInDays($dueDate);
    }

    public function getDetailedStatusAttribute()
    {
        if ($this->status === 'returned') {
            return 'Returned';
        }
        
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        return 'Borrowed';
    }

    public function wasReturnedLate()
    {
        return $this->status === 'returned' && 
               $this->returned_at && 
               $this->due_date && 
               $this->returned_at->isAfter($this->due_date);
    }
}