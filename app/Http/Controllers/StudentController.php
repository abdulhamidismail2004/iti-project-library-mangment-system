<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // Student Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $borrowedBooks = BorrowRecord::with('book')
                                   ->where('user_id', $user->id)
                                   ->where('status', 'borrowed')
                                   ->get();
        
        $borrowHistory = BorrowRecord::with('book')
                                   ->where('user_id', $user->id)
                                   ->where('status', 'returned')
                                   ->orderBy('returned_at', 'desc')
                                   ->take(5)
                                   ->get();
        
        return view('student.dashboard', compact('borrowedBooks', 'borrowHistory'));
    }

    // Browse Books
    public function allbooks(Request $request)
    {
        $query = Book::query();
        
        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }
        
        if($request->filled('category') && $request->category !== 'all'){
            $query->where('category', $request->category);
        }
        
        $books = $query->get();
        $categories = Book::distinct()->pluck('category');
        
        return view('student.allbooks', compact('books', 'categories'));
    }

    public function bookdetails($id)
    {
        $book = Book::findOrFail($id);
        $userHasBorrowed = BorrowRecord::where('user_id', Auth::id())
                                     ->where('book_id', $id)
                                     ->where('status', 'borrowed')
                                     ->exists();
        
        return view('student.bookdetails', compact('book', 'userHasBorrowed'));
    }

    // Borrow Book
    public function borrowbook(Request $request)
    {
        $bookId = $request->book_id;
        $userId = Auth::id();
        
        // Check if book exists and is available
        $book = Book::findOrFail($bookId);
        if($book->available_copies <= 0){
            return redirect()->back()->with('error', 'Book is not available');
        }
        
        // Check if user already borrowed this book
        $existingRecord = BorrowRecord::where('user_id', $userId)
                                    ->where('book_id', $bookId)
                                    ->where('status', 'borrowed')
                                    ->exists();
        
        if($existingRecord){
            return redirect()->back()->with('error', 'You have already borrowed this book');
        }
        
        // Check if user has reached borrowing limit (3 books)
        $userBorrowedCount = BorrowRecord::where('user_id', $userId)
                                       ->where('status', 'borrowed')
                                       ->count();
        
        if($userBorrowedCount >= 3){
            return redirect()->back()->with('error', 'You have reached your borrowing limit (3 books)');
        }
        
        // Create borrow record
        $borrowRecord = new BorrowRecord();
        $borrowRecord->user_id = $userId;
        $borrowRecord->book_id = $bookId;
        $borrowRecord->borrowed_at = now();
        $borrowRecord->due_date = now()->addDays(14); // 2 weeks borrowing period
        $borrowRecord->status = 'borrowed';
        $borrowRecord->save();
        
        // Decrease available copies
        $book->available_copies = $book->available_copies - 1;
        $book->save();
        
        return redirect()->route('student.dashboard')->with('status', 'Book borrowed successfully! Due date: ' . now()->addDays(14)->format('M d, Y'));
    }

    // Return Book
    public function returnbook($id)
    {
        $borrowRecord = BorrowRecord::findOrFail($id);
        
        // Check if the record belongs to the authenticated user
        if($borrowRecord->user_id != Auth::id()){
            return redirect()->back()->with('error', 'Unauthorized action');
        }
        
        $borrowRecord->status = 'returned';
        $borrowRecord->returned_at = now();
        $borrowRecord->save();
        
        // Increase available copies
        $book = Book::find($borrowRecord->book_id);
        $book->available_copies = $book->available_copies + 1;
        $book->save();
        
        return redirect()->route('student.dashboard')->with('status', 'Book returned successfully');
    }

    // Borrow History
    public function borrowhistory()
    {
        $borrowHistory = BorrowRecord::with('book')
                                   ->where('user_id', Auth::id())
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('student.borrowhistory', compact('borrowHistory'));
    }

    // Profile Management
    public function profile()
    {
        return view('student.profile');
    }

    public function updateprofile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'student_id' => 'nullable|string|max:255|unique:users,student_id,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->student_id = $request->student_id;
        
        if($request->filled('password')){
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        return redirect()->route('student.profile')->with('status', 'Profile updated successfully');
    }
}