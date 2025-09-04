<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $totalBooks = Book::count();
        $totalUsers = User::where('usertype', 'student')->count();
        $borrowedBooks = BorrowRecord::where('status', 'borrowed')->count();
        $availableBooks = Book::where('available_copies', '>', 0)->count();
        
        return view('admin.dashboard', compact('totalBooks', 'totalUsers', 'borrowedBooks', 'availableBooks'));
    }

    // Books Management
    public function addbook()
    {
        return view('admin.addbook');
    }

    public function createbook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'category' => 'required|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $book = new Book();
        $book->title = $request->title;
        $book->author = $request->author;
        $book->isbn = $request->isbn;
        $book->category = $request->category;
        $book->description = $request->description;
        $book->total_copies = $request->total_copies;
        $book->available_copies = $request->total_copies;
        
        if($request->hasFile('cover_image')){
            $image = $request->cover_image;
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $book->cover_image = $imagename;
            $request->cover_image->move('img/books', $imagename);
        }
        
        $book->save();
        
        return redirect()->route('admin.addbook')->with('status', 'Book added successfully');
    }

    public function allbooks()
    {
        $books = Book::all();
        return view('admin.allbooks', compact('books'));
    }

    public function editbook($id)
    {
        $book = Book::findOrFail($id);
        return view('admin.editbook', compact('book'));
    }

    public function updatebook(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $id,
            'category' => 'required|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $book->title = $request->title;
        $book->author = $request->author;
        $book->isbn = $request->isbn;
        $book->category = $request->category;
        $book->description = $request->description;
        
        // Update available copies based on change in total copies
        $difference = $request->total_copies - $book->total_copies;
        $book->available_copies = max(0, $book->available_copies + $difference);
        $book->total_copies = $request->total_copies;
        
        if($request->hasFile('cover_image')){
            // Delete old image
            if($book->cover_image && file_exists(public_path('img/books/'.$book->cover_image))){
                unlink(public_path('img/books/'.$book->cover_image));
            }
            
            $image = $request->cover_image;
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $book->cover_image = $imagename;
            $request->cover_image->move('img/books', $imagename);
        }
        
        $book->save();
        
        return redirect()->route('admin.allbooks')->with('status', 'Book updated successfully');
    }

    public function deletebook($id)
    {
        $book = Book::findOrFail($id);
        
        // Check if book is currently borrowed
        $borrowedCount = BorrowRecord::where('book_id', $id)->where('status', 'borrowed')->count();
        if($borrowedCount > 0){
            return redirect()->route('admin.allbooks')->with('error', 'Cannot delete book. It is currently borrowed.');
        }
        
        // Delete cover image
        if($book->cover_image && file_exists(public_path('img/books/'.$book->cover_image))){
            unlink(public_path('img/books/'.$book->cover_image));
        }
        
        $book->delete();
        
        return redirect()->route('admin.allbooks')->with('status', 'Book deleted successfully');
    }

    // Users Management
    public function allusers()
    {
        $users = User::where('usertype', 'student')->get();
        return view('admin.allusers', compact('users'));
    }

    public function searchuser(Request $request)
    {
        $query = $request->search;
        $users = User::where('usertype', 'student')
                    ->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('student_id', 'LIKE', "%{$query}%");
                    })->get();
        
        return view('admin.allusers', compact('users', 'query'));
    }

    public function userdetails($id)
    {
        $user = User::findOrFail($id);
        $borrowRecords = BorrowRecord::with('book')->where('user_id', $id)->orderBy('created_at', 'desc')->get();
        
        return view('admin.userdetails', compact('user', 'borrowRecords'));
    }

    // Borrow Records Management
    public function borrowedbooks()
    {
        $borrowedBooks = BorrowRecord::with(['book', 'user'])
                                   ->where('status', 'borrowed')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('admin.borrowedbooks', compact('borrowedBooks'));
    }

    public function returnbook($id)
    {
        $borrowRecord = BorrowRecord::findOrFail($id);
        $borrowRecord->status = 'returned';
        $borrowRecord->returned_at = now();
        $borrowRecord->save();
        
        // Increase available copies
        $book = Book::find($borrowRecord->book_id);
        $book->available_copies = $book->available_copies + 1;
        $book->save();
        
        return redirect()->route('admin.borrowedbooks')->with('status', 'Book returned successfully');
    }

    public function borrowhistory()
    {
        $borrowHistory = BorrowRecord::with(['book', 'user'])
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('admin.borrowhistory', compact('borrowHistory'));
    }

    // Profile Management
    public function profile()
    {
        return view('admin.profile');
    }

    public function updateprofile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if($request->filled('password')){
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        return redirect()->route('admin.profile')->with('status', 'Profile updated successfully');
    }
}