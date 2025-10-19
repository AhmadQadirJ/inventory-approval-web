<x-app-layout>
    
    {{-- BLOK KODE BARU UNTUK MODAL ERROR --}}
    @if (session('error'))
        <div x-data="{ open: true }" x-show="open" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Approval Gagal</h3>
                <div class="mt-2 text-sm text-gray-600">
                    <p>{{ session('error') }}</p>
                </div>
                <div class="mt-4">
                    <button @click="open = false" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- AKHIR BLOK KODE BARU --}}

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('approval.index') }}" class="inline-flex items-center mb-4 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 space-y-8">
                    {{-- HEADER --}}
                    <div class="text-center border-b pb-4">
                        <h2 class="text-2xl font-bold">Approval Details</h2>
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
                                <label class="font-medium text-gray-500">Cabang</label>
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

                    {{-- (Timeline Status bisa ditambahkan di sini jika perlu, sama seperti di show.blade.php) --}}
                    
                    {{-- BAGIAN BARU: VERIFICATION & APPROVAL FORM --}}
                    <div class="border-t pt-8">
                        <h2 class="text-xl font-bold text-gray-800">Verification & Approval</h2>
                        <form method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="id" value="{{ $submission->id }}">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm" placeholder="Add verification notes here..."></textarea>
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <a href="{{ route('approval.index') }}" class="px-8 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
                                <div class="space-x-4">
                                    <button type="submit" formaction="{{ route('approval.approve', $submission->proposal_id) }}" class="px-8 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">Approve</button>
                                    <button type="submit" formaction="{{ route('approval.reject', $submission->proposal_id) }}" class="px-8 py-2 bg-yellow-500 text-white font-semibold rounded-md hover:bg-yellow-600">Reject</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </x-app-layout>