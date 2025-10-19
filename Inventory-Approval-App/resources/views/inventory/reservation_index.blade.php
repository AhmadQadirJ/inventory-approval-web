<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('inventory.show', $inventory) }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Detail Barang
            </a>

            <h1 class="text-3xl font-bold text-gray-800 mb-2">Cek Ketersediaan: {{ $inventory->nama }}</h1>
            <p class="text-lg text-gray-500 mb-6">Ketersediaan pada: <span class="font-bold text-gray-900">{{ $selectedDate->format('l, d F Y') }}</span></p>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- BAGIAN KIRI: KALENDER DAN SLOT WAKTU --}}
                <div class="lg:col-span-7 space-y-8">
                    {{-- Kalender --}}
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <a href="{{ route('inventory.reservation.index', ['inventory' => $inventory, 'date' => $selectedDate->copy()->subMonth()->format('Y-m-d')]) }}" class="p-2 rounded-full hover:bg-gray-100">&lt;</a>
                            <h4 class="text-xl font-semibold">{{ $selectedDate->format('F Y') }}</h4>
                            <a href="{{ route('inventory.reservation.index', ['inventory' => $inventory, 'date' => $selectedDate->copy()->addMonth()->format('Y-m-d')]) }}" class="p-2 rounded-full hover:bg-gray-100">&gt;</a>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center text-sm text-gray-500 mb-2">
                            <div>Sun</div> <div>Mon</div> <div>Tue</div> <div>Wed</div> <div>Thu</div> <div>Fri</div> <div>Sat</div>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            {{-- Tambahkan sel kosong untuk hari sebelum tanggal 1 --}}
                            @for ($i = 0; $i < $dateRange->getStartDate()->dayOfWeek; $i++)
                                <div></div>
                            @endfor

                            @foreach ($dateRange as $date)
                                <a href="{{ route('inventory.reservation.index', ['inventory' => $inventory, 'date' => $date->format('Y-m-d')]) }}"
                                   class="py-2 rounded-full text-center hover:bg-gray-200
                                          @if($date->isSameDay($selectedDate)) bg-red-600 text-white hover:bg-red-700 @endif
                                          @if($date->isToday() && !$date->isSameDay($selectedDate)) border border-red-500 @endif">
                                    {{ $date->day }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Slot Waktu --}}
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="text-xl font-semibold mb-4">Slot Waktu Tersedia</h4>
                        <div class="border rounded-md">
                            <div class="grid grid-cols-5">
                                @foreach ($availableSlots as $slot)
                                    @if ($slot['is_booked'])
                                        {{-- Slot Terisi (Biru) --}}
                                        <div class="col-span-5 p-3 border-b border-l-4 border-blue-500 bg-blue-100 flex justify-between items-center">
                                            <p class="font-medium text-blue-800">{{ $slot['time_start'] }}</p>
                                            <p class="text-sm font-semibold text-blue-700">Sisa: {{ $slot['available_count'] }}</p>
                                        </div>
                                    @else
                                        {{-- Slot Kosong --}}
                                        <div class="col-span-5 p-3 border-b">
                                            <p class="font-medium">{{ $slot['time_start'] }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN KANAN: HISTORY RESERVASI --}}
                <div class="lg:col-span-5">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold">Riwayat Peminjaman Aktif</h4>
                        <form action="{{ route('inventory.reservation.index', $inventory) }}" method="GET" id="active-today-form-reservation">
                            {{-- Kirim tanggal yang sedang dipilih agar tidak hilang saat filter --}}
                            <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                            <div class="flex items-center">
                                <label for="active_today_reservation" class="text-sm font-medium text-gray-700 mr-2">Show Only Active Today</label>
                                <input type="checkbox" id="active_today_reservation" name="active_today" value="1"
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