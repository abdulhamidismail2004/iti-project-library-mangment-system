<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Currently Borrowed Books') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 rounded p-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Currently Borrowed Books</h3>
                        <div class="text-sm text-gray-500">
                            Total: {{ $borrowedBooks->count() }} books
                        </div>
                    </div>

                    @if($borrowedBooks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($borrowedBooks as $record)
                                        <tr class="hover:bg-gray-50 {{ $record->due_date < now() ? 'bg-red-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($record->book->cover_image)
                                                        <img src="{{ asset('img/books/' . $record->book->cover_image) }}" 
                                                             alt="{{ $record->book->title }}" class="h-12 w-8 object-cover rounded mr-3">
                                                    @else
                                                        <div class="h-12 w-8 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $record->book->title }}</div>
                                                        <div class="text-sm text-gray-500">{{ $record->book->author }}</div>
                                                        <div class="text-xs text-gray-400">ISBN: {{ $record->book->isbn }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $record->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $record->user->email }}</div>
                                                @if($record->user->student_id)
                                                    <div class="text-xs text-gray-400">ID: {{ $record->user->student_id }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->borrowed_at->format('M d, Y') }}
                                                <div class="text-xs text-gray-500">{{ $record->borrowed_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="{{ $record->due_date < now() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                    {{ $record->due_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs {{ $record->due_date < now() ? 'text-red-500' : 'text-gray-500' }}">
                                                    {{ $record->due_date->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->due_date < now())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Overdue
                                                    </span>
                                                    <div class="text-xs text-red-600 mt-1">
                                                        {{ abs($record->due_date->diffInDays(now())) }} days overdue
                                                    </div>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Borrowed
                                                    </span>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $record->due_date->diffInDays(now()) }} days left
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <form method="POST" action="{{ route('admin.returnbook', $record->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                onclick="return confirm('Mark this book as returned?')"
                                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded text-xs font-medium">
                                                            Mark Returned
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('admin.userdetails', $record->user->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900">
                                                        View Student
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-yellow-800">Active Borrowings</div>
                                <div class="text-2xl font-bold text-yellow-900">
                                    {{ $borrowedBooks->where('due_date', '>=', now())->count() }}
                                </div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-red-800">Overdue Books</div>
                                <div class="text-2xl font-bold text-red-900">
                                    {{ $borrowedBooks->where('due_date', '<', now())->count() }}
                                </div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-blue-800">Total Borrowed</div>
                                <div class="text-2xl font-bold text-blue-900">{{ $borrowedBooks->count() }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No borrowed books</h3>
                            <p class="mt-1 text-sm text-gray-500">All books are currently available in the library.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.allbooks') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    View All Books
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>