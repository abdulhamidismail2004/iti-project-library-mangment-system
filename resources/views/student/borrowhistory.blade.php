<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reading History
        </h2>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 to-slate-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Success Messages -->
            @if (session('status'))
                <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-400 rounded-r-xl shadow-sm">
                    <div class="flex items-center p-6">
                        <div class="flex-shrink-0">
                            <span class="text-emerald-500 text-xl">✓</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-emerald-800 font-medium">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Header with Stats -->
            <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                    <h3 class="text-2xl font-bold text-white mb-2">My Library Journey</h3>
                    <p class="text-indigo-100">Track all your borrowed books and reading progress</p>
                </div>
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $borrowHistory->where('status', 'returned')->count() }}</div>
                            <div class="text-sm text-gray-600 font-medium">Books Completed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600">{{ $borrowHistory->where('status', 'borrowed')->count() }}</div>
                            <div class="text-sm text-gray-600 font-medium">Currently Reading</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $borrowHistory->count() }}</div>
                            <div class="text-sm text-gray-600 font-medium">Total Borrowed</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Content -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                @if ($borrowHistory->isEmpty())
                    <!-- Empty State -->
                    <div class="text-center py-16 px-8">
                        <div class="text-8xl mb-6">📚</div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">No Reading History Yet</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            Start your reading journey by browsing our collection and borrowing your first book!
                        </p>
                        <a href="{{ route('student.allbooks') }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            <span class="mr-2">📖</span>
                            Explore Books
                        </a>
                    </div>
                @else
                    <!-- History Records -->
                    <div class="px-8 py-6">
                        <h4 class="text-xl font-semibold text-gray-800 mb-6">Borrowing Records</h4>
                        <div class="space-y-4">
                            @foreach ($borrowHistory as $record)
                                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-all duration-200">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                        
                                        <!-- Book Information -->
                                        <div class="flex-grow">
                                            <h5 class="text-lg font-semibold text-gray-900 mb-2">{{ $record->book->title }}</h5>
                                            <p class="text-gray-600 mb-3">by {{ $record->book->author }}</p>
                                            
                                            <!-- Date Information Grid -->
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="block text-gray-500 font-medium mb-1">Borrowed On</span>
                                                    <span class="text-gray-800 font-semibold">{{ $record->borrowed_at->format('M d, Y') }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-gray-500 font-medium mb-1">Due Date</span>
                                                    <span class="text-gray-800 font-semibold">{{ $record->due_date->format('M d, Y') }}</span>
                                                </div>
                                                <div>
                                                    <span class="block text-gray-500 font-medium mb-1">Return Date</span>
                                                    <span class="text-gray-800 font-semibold">
                                                        {{ $record->returned_at ? $record->returned_at->format('M d, Y') : 'Not returned yet' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="flex-shrink-0">
                                            @if ($record->status == 'borrowed')
                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                                                    Currently Borrowed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                                                    Returned
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navigation Footer -->
            <div class="mt-8 text-center">
                <a href="{{ route('student.dashboard') }}"
                   class="inline-flex items-center px-8 py-3 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
                    <span class="mr-2">←</span>
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>