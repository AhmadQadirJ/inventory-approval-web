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

                {{-- Search & Filter --}}
                <div class="flex justify-between items-center mb-4">
                    <div class="w-1/2">
                        <label for="search" class="text-sm font-medium text-gray-700 mr-2">Search :</label>
                        <input type="text" id="search" class="inline-block w-2/3 border-gray-300 bg-gray-50 rounded-md shadow-sm">
                    </div>
                    <div class="flex items-center">
                        <label for="show_only_waiting" class="text-sm font-medium text-gray-700 mr-2">Show Only 'Waiting For Approval'</label>
                        <input type="checkbox" id="show_only_waiting" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    </div>
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
                                            else if ($submission->status == 'Accepted') $statusClass = 'bg-green-100 text-green-800';
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
                                            ($submission->status == 'Processed - Manager' && Auth::user()->role == 'Manager') ||
                                            ($submission->status == 'Processed - Finance' && Auth::user()->role == 'Finance') ||
                                            ($submission->status == 'Processed - COO' && Auth::user()->role == 'COO'))
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