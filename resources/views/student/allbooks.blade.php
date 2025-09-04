<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Library Collection
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Enhanced Search Interface -->
            <div class="bg-gradient-to-r from-indigo-50 via-white to-purple-50 rounded-xl shadow-lg mb-8">
                <div class="px-8 py-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Find Your Next Book</h3>
                    <form method="GET" action="{{ route('student.allbooks') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:gap-6">
                        <div class="flex-grow">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Title or Author</label>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Enter keywords..." 
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-0 transition-colors">
                        </div>
                        <div class="lg:w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Genre</label>
                            <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-0 transition-colors">
                                <option value="all">Show All Genres</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" 
                                    class="w-full lg:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors shadow-md hover:shadow-lg">
                                Search Collection
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Book Collection Display -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($books as $book)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="p-6">
                            <!-- Book Cover Section -->
                            <div class="relative mb-6">
                                @if($book->cover_image)
                                    <img src="{{ asset('img/books/'.$book->cover_image) }}" 
                                         alt="{{ $book->title }}" 
                                         class="w-full h-56 object-cover rounded-lg shadow-sm">
                                @else
                                    <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center rounded-lg">
                                        <div class="text-center">
                                            <span class="text-5xl mb-2 block">📖</span>
                                            <span class="text-xs text-gray-500">No Cover</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Availability Badge -->
                                <div class="absolute top-3 right-3">
                                    @if($book->available_copies > 0)
                                        <span class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                                            {{ $book->available_copies }} Available
                                        </span>
                                    @else
                                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                                            Unavailable
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Book Information -->
                            <div class="space-y-3 mb-6">
                                <h3 class="font-bold text-lg text-gray-900 line-clamp-2 leading-tight">{{ $book->title }}</h3>
                                <p class="text-gray-600 font-medium">{{ $book->author }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $book->category }}
                                    </span>
                                    <span class="text-sm text-gray-500 font-medium">
                                        {{ $book->available_copies }}/{{ $book->total_copies }} copies
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <a href="{{ route('student.bookdetails', $book->id) }}" 
                                   class="block w-full bg-slate-600 hover:bg-slate-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition-colors">
                                    More Details
                                </a>
                                
                                @if($book->available_copies > 0)
                                    <form method="POST" action="{{ route('student.borrowbook') }}">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit" 
                                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md hover:shadow-lg">
                                            Borrow Now
                                        </button>
                                    </form>
                                @else
                                    <button disabled 
                                            class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-4 rounded-lg cursor-not-allowed">
                                        Currently Unavailable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-16">
                            <div class="text-8xl mb-4">📚</div>
                            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Books Found</h3>
                            <p class="text-gray-500 mb-6">Try adjusting your search criteria or browse different categories.</p>
                            <a href="{{ route('student.allbooks') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                Browse All Books
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>