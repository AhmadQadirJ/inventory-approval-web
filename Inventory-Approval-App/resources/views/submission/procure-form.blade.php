<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            <a href="{{ route('submission') }}" class="inline-flex items-center mb-4 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h2 class="text-2xl font-bold text-center">Form Pengajuan Pengadaan Barang</h2>
                    <form action="{{ route('submission.procure.store') }}" method="POST" class="mt-8 space-y-8">
                        @csrf
                        {{-- 1. Informasi Karyawan --}}
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">1</div>
                                <h3 class="ml-4 text-lg font-semibold">Informasi Karyawan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="nama_lengkap" value="Nama Lengkap*" />
                                    <x-text-input id="nama_lengkap" name="nama_lengkap" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="nip" value="ID Karyawan/NIP*" />
                                    <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="departemen" value="Departemen*" />
                                    <select id="departemen" name="departemen" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option>IT</option>
                                        <option>Finance</option>
                                        <option>Advertisement</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Detail Barang --}}
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">2</div>
                                <h3 class="ml-4 text-lg font-semibold">Detail Barang</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="nama_barang" value="Nama Barang*" />
                                    <x-text-input id="nama_barang" name="nama_barang" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="jumlah" value="Jumlah Barang*" />
                                    <x-text-input id="jumlah" name="jumlah" type="number" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="estimasi_harga" value="Estimasi Harga*" />
                                    <x-text-input id="estimasi_harga" name="estimasi_harga" type="text" placeholder="Rp. 0" class="mt-1 block w-full" required />
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                    const hargaInput = document.getElementById('estimasi_harga');
                                        hargaInput.addEventListener('input', function (e) {
                                            // hapus semua karakter non-angka
                                            let value = e.target.value.replace(/\D/g, '');

                                            // format ribuan pakai titik
                                            if (value) {
                                                e.target.value = new Intl.NumberFormat('id-ID').format(value);
                                            } else {
                                                e.target.value = '';
                                            }
                                        });
                                    });
                                </script>
                                <div>
                                    <x-input-label for="link_referensi" value="Link Referensi*" />
                                    <x-text-input id="link_referensi" name="link_referensi" type="url" placeholder="https://..." class="mt-1 block w-full" required />
                                </div>
                                <div class="md:col-span-2" x-data="{ count: 0 }" x-init="count = $refs.content.value.length">
                                    <x-input-label for="deskripsi_barang" value="Deskripsi Barang*" />
                                    <textarea x-ref="content" @input="count = $event.target.value.length" id="deskripsi_barang" name="deskripsi_barang" rows="3" maxlength="300" class="mt-1 block w-full border-gray-300 ..."></textarea>
                                    <div class="text-sm text-gray-500 mt-1 text-right" :class="{ 'text-red-500': count > 300 }">
                                        <span x-text="count"></span> / 300
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Detail Pengadaan --}}
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">3</div>
                                <h3 class="ml-4 text-lg font-semibold">Detail Pengadaan</h3>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="judul_pengadaan" value="Judul Tujuan Pengadaan*" />
                                    <x-text-input id="judul_pengadaan" name="judul_pengadaan" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label value="Tanggal Pemakaian*" />
                                    <div class="flex items-center space-x-4 mt-1">
                                        <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="block w-full" required />
                                        <span>sampai</span>
                                        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="block w-full" required />
                                    </div>
                                </div>
                                <div x-data="{ count: 0 }" x-init="count = $refs.content.value.length">
                                    <x-input-label for="deskripsi_pengadaan" value="Deskripsi Pengadaan*" />
                                    <textarea x-ref="content" @input="count = $event.target.value.length" id="deskripsi_pengadaan" name="deskripsi_pengadaan" rows="4" maxlength="300" class="mt-1 block w-full border-gray-300 ..."></textarea>
                                    <div class="text-sm text-gray-500 mt-1 text-right" :class="{ 'text-red-500': count > 300 }">
                                        <span x-text="count"></span> / 300
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button type="submit" class="w-full md:w-1/2 px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>