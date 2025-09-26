<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">User Management</h2>

                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="my-4">
                    <form action="{{ route('user-management.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end md:space-x-4">
                        {{-- Search Input --}}
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search by Name/Email:</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Role Filter Dropdown --}}
                        <div class="mt-2 md:mt-0">
                            <label for="role_filter" class="block text-sm font-medium text-gray-700">Filter by Role:</label>
                            <select name="role_filter" id="role_filter" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Roles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @if(request('role_filter') == $role) selected @endif>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-end space-x-2 mt-4 md:mt-0">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">Filter</button>
                            <a href="{{ route('user-management.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Reset</a>
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}">
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->role }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('user-management.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>