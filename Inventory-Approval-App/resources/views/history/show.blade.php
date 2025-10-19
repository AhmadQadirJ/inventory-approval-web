<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8" x-data="{ modalOpen: false, modalNotes: '', modalUser: '' }">
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
                                <label class="font-medium text-gray-500">Branch</label>
                                <p class="text-gray-800 mt-1">{{ $submission->branch }}</p>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">

                            {{-- Tampilan jika proposalnya adalah PEMINJAMAN --}}
                            @if ($submission instanceof \App\Models\LendSubmission)
                                <div>
                                    <label class="font-medium text-gray-500">Nama Properti</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->inventory->nama }}</p>
                                </div>
                                <div>
                                    <label class="font-medium text-gray-500">Kode Properti</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->inventory->kode }}</p>
                                </div>
                                <div>
                                    <label class="font-medium text-gray-500">Branch Properti</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->inventory->branch }}</p>
                                </div>
                                
                                @if ($submission->inventory->kategori !== 'Ruangan')
                                    <div>
                                        <label class="font-medium text-gray-500">Brand Properti</label>
                                        <p class="text-gray-800 mt-1">{{ $submission->inventory->brand ?? '-' }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="font-medium text-gray-500">Jumlah yang Dipinjam</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->quantity }} Unit</p>
                                </div>

                            {{-- Tampilan jika proposalnya adalah PENGADAAN --}}
                            @elseif ($submission instanceof \App\Models\ProcureSubmission)
                                <div>
                                    <label class="font-medium text-gray-500">Nama Barang</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->item_name }}</p>
                                </div>
                                <div>
                                    <label class="font-medium text-gray-500">Jumlah Barang</label>
                                    <p class="text-gray-800 mt-1">{{ $submission->quantity }} Unit</p>
                                </div>
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
                                    <p class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $submission->item_description }}</p>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- 3. Detail Pengajuan --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">3</div>
                            <h3 class="ml-4 text-lg font-semibold">Detail {{ $submission->type == 'Peminjaman' ? 'Peminjaman' : 'Pengadaan' }}</h3>
                        </div>
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="font-medium text-gray-500">Judul Tujuan</label>
                                <p class="text-gray-800 mt-1">{{ $submission->purpose_title }}</p>
                            </div>

                            <div>
                                <label class="font-medium text-gray-500">Rentang Tanggal</label>
                                <p class="text-gray-800 mt-1">
                                    {{ \Carbon\Carbon::parse($submission->start_date)->format('d/m/Y') }}
                                    <span class="text-gray-500">sampai</span>
                                    {{ \Carbon\Carbon::parse($submission->end_date)->format('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Hanya tampilkan jam jika proposalnya adalah Peminjaman --}}
                            @if ($submission instanceof \App\Models\LendSubmission)
                                <div>
                                    <label class="font-medium text-gray-500">Jam Penggunaan (Setiap Hari)</label>
                                    <p class="text-gray-800 mt-1">
                                        {{ \Carbon\Carbon::parse($submission->start_time)->format('H:i') }}
                                        <span class="text-gray-500">sampai</span>
                                        {{ \Carbon\Carbon::parse($submission->end_time)->format('H:i') }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <label class="font-medium text-gray-500">Deskripsi</label>
                                <p class="text-gray-800">
                                    @if ($submission instanceof \App\Models\LendSubmission)
                                        {{ $submission->description }}
                                    @elseif ($submission instanceof \App\Models\ProcureSubmission)
                                        {{ $submission->procurement_description }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Timeline Status --}}
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">4</div>
                            <h3 class="ml-4 text-lg font-semibold">Timeline Status</h3>
                        </div>
                        <div class="mt-4">
                            <ul class="space-y-4">
                                {{-- Menampilkan semua riwayat yang TELAH SELESAI --}}
                                @forelse($submission->timelines->sortBy('created_at') as $timeline)
                                <li>
                                    <div class="flex items-start">
                                        <div class="h-5 w-5 rounded-full bg-green-500 flex-shrink-0 mt-1"></div>
                                        <div class="ml-4">
                                            @if($timeline->notes && $timeline->status !== 'Pending')
                                                {{-- Status bisa diklik jika ada notes --}}
                                                <button @click="modalOpen = true; modalNotes = `{{ addslashes($timeline->notes) }}`; modalUser = `{{ $timeline->user->name }} - {{ $timeline->user->role }}`" class="font-semibold text-left text-gray-800 hover:underline focus:outline-none">
                                                    {{ $timeline->status }}
                                                </button>
                                            @else
                                                {{-- Status tidak bisa diklik jika tidak ada notes --}}
                                                <p class="font-semibold text-gray-800">{{ $timeline->status }}</p>
                                            @endif
                                            <p class="text-sm text-gray-500">{{ $timeline->created_at->format('d/m/Y H:i') }} WIB</p>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li>Tidak ada riwayat status.</li>
                                @endforelse

                                {{-- Menampilkan status SAAT INI jika belum selesai --}}
                                @if(!Str::startsWith($submission->status, 'Accepted') && !Str::startsWith($submission->status, 'Rejected'))
                                    <li>
                                        <div class="flex items-start">
                                            <div class="h-5 w-5 rounded-full bg-gray-400 flex-shrink-0 mt-1 animate-pulse"></div>
                                            <div class="ml-4">
                                                <p class="font-semibold text-gray-800">{{ $submission->status }}</p>
                                                <p class="text-sm text-gray-500">On Processed</p>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-center space-x-4 pt-4 border-t">
                        <a href="{{ route('history') }}" class="px-8 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>

                        {{-- Tombol Print PDF Kondisional --}}
                        @if (Str::startsWith($submission->status, 'Accepted'))
                            @php
                                // Cek apakah halaman saat ini adalah bagian dari approval flow atau history flow
                                $printRoute = request()->routeIs('approval.show') 
                                    ? route('approval.print', $submission->proposal_id) 
                                    : route('history.print', $submission->proposal_id);
                            @endphp
                            <a href="{{ $printRoute }}" target="_blank" class="px-8 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">
                                Print PDF
                            </a>
                        @else
                            <button disabled title="Not Eligible" class="px-8 py-2 bg-gray-400 text-white font-semibold rounded-md cursor-not-allowed">
                                Print PDF
                            </button>
                        @endif

                        @php
                            $printDetailRoute = request()->routeIs('approval.show') 
                                ? route('approval.printDetail', $submission->proposal_id) 
                                : route('history.printDetail', $submission->proposal_id);
                        @endphp
                        <a href="{{ $printDetailRoute }}" target="_blank" class="px-8 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">
                            Print Detail
                        </a>
                    </div>
                </div>
            </div>
            {{-- Modal untuk Notes --}}
            <div x-show="modalOpen" @click.away="modalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <h3 class="font-bold text-lg" x-text="'Notes By: ' + modalUser"></h3>
                    <p class="py-4 whitespace-pre-wrap" x-text="modalNotes"></p>
                    <div class="text-right">
                        <button @click="modalOpen = false" class="px-4 py-2 bg-gray-200 rounded-md">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>