<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center mb-4 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 space-y-8">
                    {{-- HEADER --}}
                    <div class="text-center border-b pb-4">
                        <h2 class="text-2xl font-bold">Submission Details</h2>
                    </div>

                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Admission ID :</span>
                            <span class="font-bold text-gray-800">{{ $submission->proposal_id }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status :</span>
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
                        </div>
                    </div>

                    {{-- 1. Informasi Karyawan --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">1</div>
                            <h3 class="ml-4 text-lg font-semibold">Informasi Karyawan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                            <div>
                                <label class="font-medium text-gray-500">Nama Lengkap</label>
                                <p class="text-gray-800 mt-1">{{ $submission->full_name }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">ID Karyawan/NIP</label>
                                <p class="text-gray-800 mt-1">{{ $submission->employee_id }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">Departemen</label>
                                <p class="text-gray-800 mt-1">{{ $submission->department }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">Tipe Pengajuan</label>
                                <p class="text-gray-800 mt-1">{{ $submission->type }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Detail Barang --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">2</div>
                            <h3 class="ml-4 text-lg font-semibold">Detail Barang</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                            <div>
                                <label class="font-medium text-gray-500">Nama Barang</label>
                                <p class="text-gray-800 mt-1">{{ $submission->item_name }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">Jumlah Barang</label>
                                <p class="text-gray-800 mt-1">{{ $submission->quantity }} Unit</p>
                            </div>
                            {{-- Kolom khusus Pengadaan --}}
                            @if ($submission->type == 'Pengadaan')
                                <div>
                                    <label class="font-medium text-gray-500">Estimasi Harga</label>
                                    <p class="text-gray-800 mt-1">Rp {{ number_format($submission->estimated_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-span-full">
                                    <label class="font-medium text-gray-500">Link Referensi</label>
                                    <a href="{{$submission->reference_link}}" target="_blank" class="text-blue-600 hover:underline mt-1 block truncate">{{ $submission->reference_link }}</a>
                                </div>
                                <div class="col-span-full">
                                    <label class="font-medium text-gray-500">Deskripsi Barang</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->item_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Detail Pengajuan --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">3</div>
                            <h3 class="ml-4 text-lg font-semibold">Detail {{ $submission->type }}</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                            <div class="md:col-span-1">
                                <label class="font-medium text-gray-500">Judul Tujuan {{ $submission->type }}</label>
                                <p class="text-gray-800 mt-1">{{ $submission->purpose_title }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">Dari Tanggal</label>
                                <p class="text-gray-800 mt-1">{{ \Carbon\Carbon::parse($submission->start_date)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="font-medium text-gray-500">Sampai Tanggal</label>
                                <p class="text-gray-800 mt-1">{{ \Carbon\Carbon::parse($submission->end_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-span-full">
                                <label class="font-medium text-gray-500">Deskripsi {{ $submission->type }}</label>
                                <p class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $submission->type == 'Peminjaman' ? $submission->description : $submission->procurement_description }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Timeline Status (Versi Sederhana) --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">4</div>
                            <h3 class="ml-4 text-lg font-semibold">Timeline Status</h3>
                        </div>
                         <div class="mt-4">
                            <ul>
                                <li class="flex items-start mb-4">
                                    <div class="h-5 w-5 rounded-full bg-green-500 flex-shrink-0"></div>
                                    <div class="ml-4">
                                        <p class="font-semibold">Pengajuan disubmit</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($submission->created_at)->format('d/m/Y H:i') }} WIB</p>
                                    </div>
                                </li>
                                 <li class="flex items-start">
                                    <div class="h-5 w-5 rounded-full {{ $submission->status == 'Pending' ? 'bg-red-500' : 'bg-green-500' }}"></div>
                                    <div class="ml-4">
                                        <p class="font-semibold">{{ $submission->status }}</p>
                                        <p class="text-sm text-gray-500">On Progressed</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-center space-x-4 pt-4 border-t">
                        <a href="{{ route('history') }}" class="px-8 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>

                        {{-- Tombol Print PDF Kondisional --}}
                        @if ($submission->status == 'Accepted')
                            {{-- Versi Aktif jika status "Accepted" --}}
                            <button class="px-8 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">
                                Print PDF
                            </button>
                        @else
                            {{-- Versi Nonaktif (Disabled) jika status BUKAN "Accepted" --}}
                            <button disabled title="Not Eligible" class="px-8 py-2 bg-gray-400 text-white font-semibold rounded-md cursor-not-allowed">
                                Print PDF
                            </button>
                        @endif

                        <button class="px-8 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">Print Detail</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>