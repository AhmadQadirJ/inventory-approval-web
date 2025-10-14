<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('inventory') }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="md:flex md:space-x-8">
                        <div class="md:w-1/3 mb-6 md:mb-0">
                            <img src="{{ $inventory->gambar ? asset('storage/' . $inventory->gambar) : 'https://via.placeholder.com/300' }}" alt="{{ $inventory->nama }}" class="rounded-lg w-full object-cover">
                        </div>
                        <div class="md:w-2/3">
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">{{ $inventory->kategori }}</span>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $inventory->nama }}</h2>
                            <p class="text-lg text-gray-500 font-semibold">{{ $inventory->kode }}</p>
                            <p class="mt-4 text-gray-600">{{ $inventory->deskripsi }}</p>

                            <div class="mt-6 border-t pt-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <strong class="text-gray-500">Branch:</strong>
                                        <p>{{ $inventory->branch }}</p>
                                    </div>
                                    <div>
                                        <strong class="text-gray-500">Quantity:</strong>
                                        <p>{{ $inventory->qty }}</p>
                                    </div>
                                    @if ($inventory->kategori !== 'Ruangan')
                                        <div>
                                            <strong class="text-gray-500">Brand:</strong>
                                            <p>{{ $inventory->brand ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <strong class="text-gray-500">Harga:</strong>
                                            <p>Rp {{ number_format($inventory->harga ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <strong class="text-gray-500">Tahun Beli:</strong>
                                            <p>{{ $inventory->tahun_beli ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <strong class="text-gray-500">Vendor:</strong>
                                            <a href="{{ $inventory->vendor_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $inventory->nama_vendor ?? '-' }}</a>
                                        </div>
                                        <div class="mt-6">
                                            <a href="{{ route('inventory.reservation.index', $inventory) }}" 
                                            class="inline-flex items-center px-4 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-wider hover:bg-red-700 transition ease-in-out duration-150">
                                                Check Availability
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>