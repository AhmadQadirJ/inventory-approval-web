<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Inventory List</h2>
                    {{-- Tombol "Add Item" hanya muncul untuk GA dan Admin --}}
                    @if (in_array(Auth::user()->role, ['General Affair', 'Admin']))
                        {{-- Diperbarui untuk menunjuk ke route 'inventory.create' --}}
                        <a href="{{ route('inventory.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            + Add New Item
                        </a>
                    @endif
                </div>

                {{-- Menampilkan pesan sukses setelah create/update/delete --}}
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- Filter & Search Bar --}}
                <div class="mb-4">
                    <form action="{{ route('inventory') }}" method="GET" id="filterForm"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search (Name, Code, Brand)</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Type to search...">
                        </div>

                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="kategori" id="kategori"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @if(request('kategori') == $category) selected @endif>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="branch" class="block text-sm font-medium text-gray-700">Branch</label>
                            <select name="branch" id="branch"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch }}" @if(request('branch') == $branch) selected @endif>{{ $branch }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-4 flex items-center space-x-2">
                            <a href="{{ route('inventory') }}"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Reset</a>
                        </div>
                    </form>
                </div>

                {{-- Auto-submit script --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const form = document.getElementById('filterForm');

                        // Untuk dropdown filter
                        form.querySelectorAll('select').forEach(select => {
                            select.addEventListener('change', () => form.submit());
                        });

                        // Untuk search input (auto-submit setelah user berhenti mengetik 0.8s)
                        const searchInput = document.getElementById('search');
                        let timeout = null;
                        searchInput.addEventListener('input', () => {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => form.submit(), 800);
                        });
                    });
                </script>

                {{-- Tampilan Grid Kartu --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse ($inventories as $item)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-shadow duration-300 hover:shadow-md flex flex-col">
                            {{-- Bagian Gambar --}}
                            <a href="{{ route('inventory.show', $item) }}" class="block">
                                <div class="aspect-video bg-gray-100">
                                    <img class="object-cover w-full h-full" 
                                        src="{{ $item->gambar ? asset('storage/' . $item->gambar) : 'https://via.placeholder.com/400x225.png/f3f4f6?text=No+Image' }}" 
                                        alt="{{ $item->nama }}">
                                </div>
                            </a>
                            
                            {{-- Bagian Konten --}}
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">{{ $item->kategori }}</span>
                                    <span class="text-xs text-gray-500">{{ $item->branch }}</span>
                                </div>

                                <h3 class="text-base font-bold text-gray-800 truncate" title="{{ $item->nama }}">{{ $item->nama }}</h3>
                                <p class="text-sm text-gray-500">{{ $item->kode }}</p>

                                {{-- Spacer untuk mendorong tombol ke bawah --}}
                                <div class="flex-grow"></div>

                                {{-- Tombol Aksi --}}
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    @if (in_array(Auth::user()->role, ['General Affair', 'Admin']))
                                        <a href="{{ route('inventory.edit', $item) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                            Edit
                                        </a>
                                    @else
                                        <a href="{{ route('inventory.show', $item) }}" class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">No inventory items found.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Links --}}
                <div class="mt-4">
                    {{ $inventories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>