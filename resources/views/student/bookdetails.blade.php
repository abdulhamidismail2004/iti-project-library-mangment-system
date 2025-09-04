<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Book Information
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert Messages -->
            @if (session('status'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-800 p-5 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <span class="text-lg mr-2">✓</span>
                        {{ session('status') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-400 text-rose-800 p-5 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <span class="text-lg mr-2">⚠</span>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Main Content Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    
                    <!-- Book Cover Section -->
                    <div class="lg:w-2/5 bg-gradient-to-br from-slate-100 to-gray-200 p-8 flex items-center justify-center">
                        <div class="relative">
                            @if ($book->cover_image)
                                <img src="{{ asset('img/books/' . $book->cover_image) }}"
                                     alt="{{ $book->title }}"
                                     class="w-80 h-96 object-cover rounded-lg shadow-2xl">
                            @else
                                <div class="w-80 h-96 bg-gradient-to-br from-gray-300 to-gray-400 flex flex-col items-center justify-center rounded-lg shadow-2xl">
                                    <span class="text-8xl mb-4 opacity-70">📖</span>
                                    <span class="text-sm text-gray-600 font-medium">Cover Not Available</span>
                                </div>
                            @endif
                            
                            <!-- Availability Indicator -->
                            <div class="absolute -top-3 -right-3">
                                @if ($book->available_copies > 0)
                                    <div class="bg-emerald-500 text-white px-4 py-2 rounded-full shadow-lg">
                                        <span class="font-bold text-sm">{{ $book->available_copies }} Available</span>
                                    </div>
                                @else
                                    <div class="bg-red-500 text-white px-4 py-2 rounded-full shadow-lg">
                                        <span class="font-bold text-sm">Out of Stock</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Book Information Section -->
                    <div class="lg:w-3/5 p-8 lg:p-12">
                        <div class="space-y-6">
                            
                            <!-- Title and Category -->
                            <div>
                                <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">{{ $book->title }}</h1>
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 mb-4">
                                    {{ $book->category }}
                                </span>
                            </div>

                            <!-- Author Information -->
                            <div class="border-l-4 border-blue-400 pl-6">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Written By</h3>
                                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $book->author }}</p>
                            </div>

                            <!-- Book Metadata -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">ISBN Number</h4>
                                    <p class="text-lg font-mono text-gray-900">{{ $book->isbn }}</p>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Library Copies</h4>
                                    <p class="text-lg font-semibold text-gray-900">{{ $book->available_copies }} of {{ $book->total_copies }}</p>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($book->description)
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Synopsis</h3>
                                    <p class="text-gray-700 leading-relaxed">{{ $book->description }}</p>
                                </div>
                            @else
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Synopsis</h3>
                                    <p class="text-gray-500 italic">No synopsis provided for this title.</p>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="border-t pt-8">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    @if ($userHasBorrowed)
                                        <div class="flex-1 bg-amber-50 border-2 border-amber-200 rounded-lg p-4 text-center">
                                            <p class="text-amber-800 font-semibold flex items-center justify-center">
                                                <span class="mr-2">📚</span>
                                                Already in your collection
                                            </p>
                                        </div>
                                    @elseif ($book->available_copies > 0)
                                        <form method="POST" action="{{ route('student.borrowbook') }}" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <button type="submit"
                                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                                <span class="flex items-center justify-center">
                                                    <span class="mr-2">📖</span>
                                                    Borrow This Book
                                                </span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex-1 bg-gray-100 rounded-lg p-4 text-center">
                                            <button disabled class="w-full bg-gray-400 text-gray-600 font-bold py-4 px-8 rounded-lg cursor-not-allowed">
                                                <span class="flex items-center justify-center">
                                                    <span class="mr-2">❌</span>
                                                    Currently Unavailable
                                                </span>
                                            </button>
                                        </div>
                                    @endif
                                    
                                    <div class="sm:w-auto">
                                        <a href="{{ route('student.allbooks') }}"
                                           class="block w-full sm:w-auto bg-slate-600 hover:bg-slate-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-200 text-center">
                                            <span class="flex items-center justify-center">
                                                <span class="mr-2">←</span>
                                                Browse Collection
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>