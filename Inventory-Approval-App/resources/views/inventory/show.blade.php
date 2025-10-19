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

                            {{-- Tombol dipindahkan ke sini --}}
                            <div class="mt-4">
                                <a href="{{ route('inventory.reservation.index', $inventory) }}" 
                                class="inline-flex items-center justify-center w-full px-4 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-wider hover:bg-red-700 transition ease-in-out duration-150">
                                    Check Availability
                                </a>
                            </div>
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-800">Riwayat Peminjaman Aktif</h3>
                            <form action="{{ route('inventory.show', $inventory) }}" method="GET" id="active-today-form-show">
                                <div class="flex items-center">
                                    <label for="active_today_show" class="text-sm font-medium text-gray-700 mr-2">Show Only Active Today</label>
                                    <input type="checkbox" id="active_today_show" name="active_today" value="1"
                                        onchange="this.form.submit()" @if(request('active_today')) checked @endif
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                </div>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($reservationHistory as $history)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $history['proposal_id'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['user_name'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['branch'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['department'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['purpose_title'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['period'] }} ({{ $history['time'] }})</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $history['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada peminjaman aktif saat ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>