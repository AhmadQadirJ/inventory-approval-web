<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Welcome Section --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Welcome Back, {{ Auth::user()->name }}</h2>
                    <p class="mt-2 text-gray-600">Submit inventory requests and track the status of your request.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('submission') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Create Submission
                    </a>
                    <a href="{{ route('history') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        View
                    </a>
                </div>
            </div>

            {{-- Dashboard Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-4 bg-yellow-100 border border-yellow-300 rounded-lg">
                    <div class="text-sm font-medium text-yellow-800">Pending</div>
                    <div class="text-3xl font-bold text-yellow-900">{{ $pendingCount }}</div>
                </div>
                <div class="p-4 bg-green-100 border border-green-300 rounded-lg">
                    <div class="text-sm font-medium text-green-800">Accepted</div>
                    <div class="text-3xl font-bold text-green-900">{{ $acceptedCount }}</div>
                </div>
                <div class="p-4 bg-red-100 border border-red-300 rounded-lg">
                    <div class="text-sm font-medium text-red-800">Rejected</div>
                    <div class="text-3xl font-bold text-red-900">{{ $rejectedCount }}</div>
                </div>
                <div class="p-4 bg-blue-100 border border-blue-300 rounded-lg">
                    <div class="text-sm font-medium text-blue-800">Processed</div>
                    <div class="text-3xl font-bold text-blue-900">{{ $processedCount }}</div>
                </div>
            </div>

            {{-- Latest Submission Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest Submission</h3>
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
                                @forelse ($latestSubmissions as $submission)
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
                                            <a href="{{ route('history.show', $submission->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No recent submissions.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>