<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Submission History</h2>

                {{-- Statistik Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="p-4 bg-yellow-100 border border-yellow-300 rounded-lg shadow-sm">
                        <div class="text-sm font-medium text-yellow-800">Pending</div>
                        <div class="text-3xl font-bold text-yellow-900">{{ $pendingCount }}</div>
                    </div>
                    <div class="p-4 bg-green-100 border border-green-300 rounded-lg shadow-sm">
                        <div class="text-sm font-medium text-green-800">Accepted</div>
                        <div class="text-3xl font-bold text-green-900">{{ $acceptedCount }}</div>
                    </div>
                    <div class="p-4 bg-red-100 border border-red-300 rounded-lg shadow-sm">
                        <div class="text-sm font-medium text-red-800">Rejected</div>
                        <div class="text-3xl font-bold text-red-900">{{ $rejectedCount }}</div>
                    </div>
                    <div class="p-4 bg-blue-100 border border-blue-300 rounded-lg shadow-sm">
                        <div class="text-sm font-medium text-blue-800">Processed</div>
                        <div class="text-3xl font-bold text-blue-900">{{ $processedCount }}</div>
                    </div>
                </div>

                {{-- Search and Filter Bar --}}
                <div class="mb-4">
                    <form action="{{ route('history') }}" method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-4">
                        {{-- Search Input --}}
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search :</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Status Filter Dropdown --}}
                        <div class="mt-2 md:mt-0">
                            <label for="status_filter" class="block text-sm font-medium text-gray-700">Filter by Status:</label>
                            <select name="status_filter" id="status_filter" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="Pending"   @if(request('status_filter') == 'Pending') selected @endif>Show Only Pending</option>
                                <option value="Accepted"  @if(request('status_filter') == 'Accepted') selected @endif>Show Only Accepted</option>
                                <option value="Rejected"  @if(request('status_filter') == 'Rejected') selected @endif>Show Only Rejected</option>
                                <option value="Processed" @if(request('status_filter') == 'Processed') selected @endif>Show Only Processed</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-end space-x-2 mt-4 md:mt-0">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">Search</button>
                            @if(request('search') || request('status_filter'))
                                <a href="{{ route('history') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($submissions as $submission)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->item }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->purpose }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClass = '';
                                            if ($submission->status == 'Pending') $statusClass = 'bg-yellow-100 text-yellow-800';
                                            else if (($submission->status == 'Accepted - CHRD' || $submission->status == 'Accepted - COO' || $submission->status == 'Accepted')) $statusClass = 'bg-green-100 text-green-800';
                                            else if (Str::startsWith($submission->status, 'Rejected')) $statusClass = 'bg-red-100 text-red-800';
                                            else if (Str::startsWith($submission->status, 'Processed')) $statusClass = 'bg-blue-100 text-blue-800';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $submission->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('history.show', $submission->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No submission history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>