<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('inventory.show', $inventory) }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Detail Barang
            </a>

            <h1 class="text-3xl font-bold text-gray-800 mb-6">Cek Ketersediaan: {{ $inventory->nama }}</h1>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- BAGIAN KIRI: KALENDER DAN SLOT WAKTU --}}
                <div class="lg:col-span-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="text-xl font-semibold mb-4">Slot Waktu Tersedia (Pilih Tanggal)</h4>
                        
                        {{-- Form untuk memilih tanggal dan me-reload halaman --}}
                        <form action="{{ route('inventory.reservation.index', $inventory) }}" method="GET" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal yang Dipilih:</label>
                            <input type="date" name="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                                   value="{{ $selectedDate->format('Y-m-d') }}"
                                   onchange="this.form.submit()">
                        </form>
                        
                        <h5 class="text-lg font-bold mt-4">Ketersediaan pada: {{ $selectedDate->format('l, d F Y') }}</h5>
                        
                        {{-- List Slot Waktu --}}
                        <ul class="divide-y divide-gray-200 mt-3">
                            @foreach ($availableSlots as $slot)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-md font-medium text-gray-900">{{ $slot['time_start'] }} - {{ $slot['time_end'] }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @if ($slot['is_available'])
                                            <span class="text-sm font-semibold text-green-600">Tersedia ({{ $slot['available_count'] }} unit)</span>
                                            <a href="{{ route('submission.lend.create', ['inventory_id' => $inventory->id]) }}" class="px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Pesan</a> 
                                        @else
                                            <span class="text-sm font-semibold text-red-600">Penuh ({{ $slot['available_count'] }} unit tersisa)</span>
                                            <button class="px-3 py-1 bg-gray-200 text-gray-500 text-sm rounded-md" disabled>Penuh</button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- BAGIAN KANAN: HISTORY RESERVASI (Seperti Gambar Anda) --}}
                <div class="lg:col-span-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="text-xl font-semibold mb-4">Riwayat Peminjaman Aktif (History)</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($reservationHistory as $history)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $history['proposal_id'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['user_name'] }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $history['period'] }} ({{ $history['time'] }})</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                      {{ $history['status'] == 'Accepted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $history['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada peminjaman aktif/pending saat ini.</td>
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