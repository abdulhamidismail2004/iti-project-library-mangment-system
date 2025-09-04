<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
        'student_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function currentBorrowedBooks()
    {
        return $this->hasMany(BorrowRecord::class)->where('status', 'borrowed');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    public function isStudent()
    {
        return $this->usertype === 'student';
    }

    public function canBorrowMore()
    {
        return $this->currentBorrowedBooks()->count() < 3;
    }

    public function getBorrowedBooksCountAttribute()
    {
        return $this->currentBorrowedBooks()->count();
    }
}