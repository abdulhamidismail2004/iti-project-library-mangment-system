<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Library Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if(session('status'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-800 px-6 py-4 rounded-r-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-400 text-rose-800 px-6 py-4 rounded-r-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Content Area -->
            <div class="space-y-8">
                
                <!-- Active Borrowings Section -->
                <div class="bg-gradient-to-r from-slate-50 to-gray-100 rounded-xl shadow-md">
                    <div class="px-8 py-6">
                        <div class="flex items-center mb-6">
                            <h3 class="text-xl font-bold text-slate-800">My Active Borrowings</h3>
                        </div>
                        
                        @if($borrowedBooks->count() > 0)
                            <div class="grid gap-4">
                                @foreach($borrowedBooks as $record)
                                    <div class="bg-white rounded-lg p-5 shadow-sm border-l-4 border-indigo-400 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-center">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $record->book->title }}</h4>
                                                <p class="text-gray-600 mt-1">Author: {{ $record->book->author }}</p>
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-500">Return by: </span>
                                                    <span class="{{ $record->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                                        {{ $record->due_date ? $record->due_date->format('M d, Y') : 'No due date set' }}
                                                    </span>
                                                    @if($record->isOverdue())
                                                        <span class="ml-2 bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">OVERDUE</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <form method="POST" action="{{ route('student.returnbook', $record->id) }}">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium px-4 py-2 rounded-lg transition-colors"
                                                            onclick="return confirm('Confirm book return?')">
                                                        Return Book
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 text-lg">You haven't borrowed any books yet.</p>
                            </div>
                        @endif

                        <div class="mt-6 text-center">
                            <a href="{{ route('student.allbooks') }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg inline-flex items-center transition-colors">
                                Explore Library Collection
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Return History Section -->
                    <div class="lg:w-2/3 bg-white rounded-xl shadow-md">
                        <div class="px-8 py-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-6">Return History</h3>
                            
                            @if($borrowHistory->count() > 0)
                                <div class="space-y-4">
                                    @foreach($borrowHistory as $record)
                                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-emerald-400">
                                            <h4 class="font-semibold text-gray-900">{{ $record->book->title }}</h4>
                                            <p class="text-gray-600 text-sm mt-1">Written by {{ $record->book->author }}</p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                <span class="font-medium">Completed on:</span> 
                                                {{ $record->returned_at ? $record->returned_at->format('M d, Y') : 'Date unavailable' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No completed borrowings to display.</p>
                            @endif

                            <div class="mt-6">
                                <a href="{{ route('student.borrowhistory') }}" 
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    See Complete History →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Panel -->
                    <div class="lg:w-1/3 bg-white rounded-xl shadow-md">
                        <div class="px-8 py-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-6">Quick Stats</h3>
                            
                            <div class="space-y-6">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $borrowedBooks->count() }}</div>
                                    <div class="text-sm font-medium text-blue-800">Books on Loan</div>
                                </div>
                                
                                <div class="text-center p-4 bg-emerald-50 rounded-lg">
                                    <div class="text-3xl font-bold text-emerald-600 mb-2">{{ 3 - $borrowedBooks->count() }}</div>
                                    <div class="text-sm font-medium text-emerald-800">Available Slots</div>
                                </div>
                                
                                <div class="text-center p-4 bg-violet-50 rounded-lg">
                                    <div class="text-3xl font-bold text-violet-600 mb-2">{{ $borrowHistory->count() }}</div>
                                    <div class="text-sm font-medium text-violet-800">Books Returned</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>