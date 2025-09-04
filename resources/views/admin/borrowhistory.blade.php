<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Borrow History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Complete Borrow History</h3>
                        <div class="text-sm text-gray-500">
                            Total Records: {{ $borrowHistory->count() }}
                        </div>
                    </div>

                    @if($borrowHistory->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($borrowHistory as $record)
                                        <tr class="hover:bg-gray-50">
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
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $record->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $record->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->borrowed_at->format('M d, Y') }}
                                                <div class="text-xs text-gray-500">{{ $record->borrowed_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->due_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->status == 'borrowed')
                                                    @if($record->due_date < now())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Overdue
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Borrowed
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Returned
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($record->returned_at)
                                                    {{ $record->returned_at->format('M d, Y') }}
                                                    <div class="text-xs text-gray-500">{{ $record->returned_at->format('h:i A') }}</div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($record->returned_at)
                                                    @php
                                                        $days = $record->borrowed_at->diffInDays($record->returned_at);
                                                        $isLate = $record->returned_at > $record->due_date;
                                                    @endphp
                                                    <div class="{{ $isLate ? 'text-red-600' : 'text-gray-900' }}">
                                                        {{ $days }} days
                                                    </div>
                                                    @if($isLate)
                                                        <div class="text-xs text-red-500">
                                                            {{ $record->due_date->diffInDays($record->returned_at) }} days late
                                                        </div>
                                                    @endif
                                                @else
                                                    @php
                                                        $days = $record->borrowed_at->diffInDays(now());
                                                    @endphp
                                                    <div class="{{ $record->due_date < now() ? 'text-red-600' : 'text-gray-900' }}">
                                                        {{ $days }} days
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-blue-800">Total Records</div>
                                <div class="text-2xl font-bold text-blue-900">{{ $borrowHistory->count() }}</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-yellow-800">Currently Borrowed</div>
                                <div class="text-2xl font-bold text-yellow-900">
                                    {{ $borrowHistory->where('status', 'borrowed')->count() }}
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-green-800">Returned</div>
                                <div class="text-2xl font-bold text-green-900">
                                    {{ $borrowHistory->where('status', 'returned')->count() }}
                                </div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="text-sm font-medium text-red-800">Overdue</div>
                                <div class="text-2xl font-bold text-red-900">
                                    {{ $borrowHistory->where('status', 'borrowed')->where('due_date', '<', now())->count() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No borrowing history</h3>
                            <p class="mt-1 text-sm text-gray-500">No books have been borrowed yet.</p>
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