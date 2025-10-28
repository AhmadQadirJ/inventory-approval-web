<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Submission History</h2>

                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Statistik Card --}}
                <div class="inline-block p-4 border border-gray-200 rounded-lg shadow-sm mb-6">
                    <div class="text-sm font-medium text-red-600">Waiting for Approval</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $waitingForApprovalCount }}</div>
                </div>

                {{-- Search and Filter Bar --}}
                <div class="mb-4">
                    <form action="{{ route('approval.index') }}" method="GET" id="filterForm"
                        class="flex flex-col md:flex-row md:items-end md:space-x-4">

                        {{-- Search Input --}}
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search:</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Type to search...">
                        </div>

                        {{-- Status Filter Dropdown --}}
                        <div class="mt-2 md:mt-0">
                            <label for="status_filter" class="block text-sm font-medium text-gray-700">Filter by Status:</label>
                            <select name="status_filter" id="status_filter"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="Pending"   @if(request('status_filter') == 'Pending') selected @endif>Show Only Pending</option>
                                <option value="Accepted"  @if(request('status_filter') == 'Accepted') selected @endif>Show Only Accepted</option>
                                <option value="Rejected"  @if(request('status_filter') == 'Rejected') selected @endif>Show Only Rejected</option>
                                <option value="Processed" @if(request('status_filter') == 'Processed') selected @endif>Show Only Processed</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-end space-x-2 mt-4 md:mt-0">
                            @if(request('search') || request('status_filter') || request('waiting'))
                                <a href="{{ route('approval.index') }}"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Checkbox "Show Only Waiting for Approval" --}}
                <div class="flex justify-end items-center mb-4">
                    <form action="{{ route('approval.index') }}" method="GET" id="waiting-filter-form">
                        {{-- Keep other filters --}}
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">

                        <label for="show_only_waiting" class="text-sm font-medium text-gray-700 mr-2">
                            Show Only 'Waiting For Approval'
                        </label>

                        <input type="checkbox" id="show_only_waiting" name="waiting" value="1"
                            @if(request('waiting')) checked @endif
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    </form>
                </div>

                {{-- Auto-submit script --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const filterForm = document.getElementById('filterForm');
                        const waitingForm = document.getElementById('waiting-filter-form');

                        // Submit on dropdown change
                        document.getElementById('status_filter').addEventListener('change', () => filterForm.submit());

                        // Submit on search input typing delay (0.8s)
                        const searchInput = document.getElementById('search');
                        let timeout = null;
                        searchInput.addEventListener('input', () => {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => filterForm.submit(), 800);
                        });

                        // Submit waiting checkbox immediately
                        document.getElementById('show_only_waiting').addEventListener('change', () => waitingForm.submit());
                    });
                </script>


                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->branch }}</td>
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
                                    {{-- Logika Tombol Aksi --}}
                                    @if ($submission->status == 'Pending' && Auth::user()->role == 'General Affair')
                                        <form action="{{ route('approval.act', $submission->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-md hover:bg-blue-600">
                                                Act
                                            </button>
                                        </form>
                                    @elseif (($submission->status == 'Processed - GA' && Auth::user()->role == 'General Affair') ||
                                            ($submission->status == 'Processed - COO/CHRD' && (Auth::user()->role == 'COO' || Auth::user()->role == 'CHRD')) ||
                                            ($submission->status == 'Processed - Finance' && Auth::user()->role == 'Finance') ||
                                            ($submission->status == 'Processed - CHRD' && Auth::user()->role == 'CHRD'))
                                        <a href="{{ route('approval.process', $submission->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Proceed</a>
                                    @else
                                        <a href="{{ route('approval.show', $submission->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Detail</a>
                                    @endif
                                </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No submissions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>