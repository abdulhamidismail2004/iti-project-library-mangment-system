<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author', 
        'isbn',
        'category',
        'description',
        'cover_image',
        'total_copies',
        'available_copies',
    ];

    // Relationships
    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function currentBorrowers()
    {
        return $this->hasMany(BorrowRecord::class)->where('status', 'borrowed');
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->available_copies > 0;
    }

    public function getStatusAttribute()
    {
        return $this->available_copies > 0 ? 'Available' : 'Not Available';
    }

    public function getBorrowedCountAttribute()
    {
        return $this->total_copies - $this->available_copies;
    }
}