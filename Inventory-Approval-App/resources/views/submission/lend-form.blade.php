<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('submission') }}" class="inline-flex items-center mb-4 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h2 class="text-2xl font-bold text-center">Form Pengajuan Peminjaman Barang</h2>
                    <form action="{{ route('submission.lend.store') }}" method="POST" class="mt-8 space-y-8">
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
                                    <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="nip" value="ID Karyawan/NIP*" />
                                    <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="departemen" value="Departemen*" />
                                    <select id="departemen" name="departemen" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option>Operational</option>
                                        <option>Human Resources</option>
                                        <option>Finance & Acc Tax</option>
                                        <option>Technology</option>
                                        <option>Marketing & Creative</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Detail Barang --}}
                        <div class="p-6 rounded-lg border" 
                            x-data="{
                                branches: {{ json_encode(['Bandung', 'Jakarta', 'Surabaya']) }},
                                selectedBranch: '',
                                categories: [],
                                selectedCategory: '',
                                items: [],
                                selectedItem: null,
                                search: '',
                                isLoadingCategories: false,
                                isLoadingItems: false,

                                fetchCategories() {
                                    if (!this.selectedBranch) return;
                                    this.isLoadingCategories = true;
                                    this.categories = []; this.selectedCategory = ''; this.items = []; this.selectedItem = null;
                                    fetch(`/api/inventory/categories?branch=${this.selectedBranch}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.categories = data;
                                            this.isLoadingCategories = false;
                                        });
                                },
                                fetchItems() {
                                    if (!this.selectedCategory) return;
                                    this.isLoadingItems = true;
                                    this.items = []; this.selectedItem = null;
                                    fetch(`/api/inventory/items?branch=${this.selectedBranch}&kategori=${this.selectedCategory}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.items = data;
                                            this.isLoadingItems = false;
                                        });
                                },
                                get filteredItems() {
                                    if (this.search === '') {
                                        return this.items;
                                    }
                                    return this.items.filter(item => 
                                        item.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                                        item.kode.toLowerCase().includes(this.search.toLowerCase())
                                    );
                                }
                            }">

                            <div class="flex items-center mb-4">
                                <span class="bg-red-600 text-white rounded-full h-8 w-8 flex items-center justify-center font-bold">2</span>
                                <h3 class="ml-4 text-lg font-semibold text-gray-800">Detail Barang</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Dropdown 1: Branch --}}
                                <div>
                                    <x-input-label for="branch" value="Pilih Branch*" />
                                    <select id="branch" x-model="selectedBranch" @change="fetchCategories()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Pilih Branch --</option>
                                        <template x-for="branch in branches" :key="branch">
                                            <option :value="branch" x-text="branch"></option>
                                        </template>
                                    </select>
                                </div>

                                {{-- Dropdown 2: Kategori --}}
                                <div>
                                    <x-input-label for="kategori" value="Pilih Kategori Properti*" />
                                    <select id="kategori" x-model="selectedCategory" @change="fetchItems()" :disabled="!selectedBranch || isLoadingCategories" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                                        <option value="" x-show="!isLoadingCategories">-- Pilih Kategori --</option>
                                        <option value="" x-show="isLoadingCategories">Memuat...</option>
                                        <template x-for="category in categories" :key="category.kategori">
                                            <option :value="category.kategori" x-text="category.kategori"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            {{-- Dropdown 3: Properti (Searchable Dropdown) --}}
                            <div class="mt-6" x-data="{ open: false }">
                                <x-input-label for="item_search" value="Cari & Pilih Properti*" />

                                <div x-show="selectedCategory && !isLoadingItems" class="relative">
                                    {{-- Input yang terlihat oleh user --}}
                                    <input 
                                        @click="open = true" 
                                        readonly
                                        :value="selectedItem ? items.find(i => i.id === selectedItem)?.nama + ' (' + items.find(i => i.id === selectedItem)?.kode + ')' : ''"
                                        placeholder="-- Klik untuk memilih properti --" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm cursor-pointer bg-white">

                                    {{-- Dropdown yang berisi search dan hasil --}}
                                    <div x-show="open" @click.away="open = false" x-cloak
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">

                                        {{-- Search Bar di dalam dropdown --}}
                                        <input type="text" x-model="search" placeholder="Ketik untuk mencari..." 
                                            class="w-full p-2 border-b focus:ring-0 focus:border-indigo-300">

                                        {{-- Hasil Pencarian --}}
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <div @click="selectedItem = item.id; open = false"
                                                :class="{ 'bg-indigo-100': selectedItem === item.id }"
                                                class="p-2 hover:bg-gray-100 cursor-pointer">
                                                <span class="font-semibold" x-text="item.nama"></span>
                                                <span class="text-sm text-gray-500 ml-2" x-text="`(${item.kode})`"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredItems.length === 0" class="p-2 text-center text-gray-500">
                                            Tidak ada properti yang cocok.
                                        </div>
                                    </div>
                                </div>

                                <div x-show="isLoadingItems" class="mt-1 text-sm text-gray-500">
                                    Memuat properti...
                                </div>

                                {{-- Hidden input untuk mengirim ID barang yang dipilih --}}
                                <input type="hidden" name="inventory_id" x-model="selectedItem">
                            </div>

                            {{-- Input Jumlah Barang --}}
                            <div class="mt-6">
                                <x-input-label for="quantity" value="Jumlah Barang*" />
                                <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" required />
                            </div>
                        </div>

                        {{-- 3. Detail Peminjaman --}}
                        <div class="p-6 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">3</div>
                                <h3 class="ml-4 text-lg font-semibold">Detail Peminjaman</h3>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="judul_peminjaman" value="Judul Tujuan Peminjaman*" />
                                    <x-text-input id="judul_peminjaman" name="judul_peminjaman" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <div>
                                        <x-input-label value="Periode Peminjaman*" />
                                        <div class="mt-1 space-y-4">
                                            {{-- Baris untuk Rentang Tanggal --}}
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Rentang Tanggal</label>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="block w-full" required />
                                                    <span class="text-gray-500">s/d</span>
                                                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="block w-full" required />
                                                </div>
                                            </div>

                                            {{-- Baris untuk Rentang Jam --}}
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Jam Penggunaan (Setiap Hari)</label>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <x-text-input id="start_time" name="start_time" type="time" class="block w-full" required />
                                                    <span class="text-gray-500">s/d</span>
                                                    <x-text-input id="end_time" name="end_time" type="time" class="block w-full" required />
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Rentang jam yang Anda pilih akan berlaku untuk setiap hari dalam periode tanggal peminjaman.
                                        </p>
                                    </div>
                                </div>
                                <div x-data="{ count: 0 }" x-init="count = $refs.content.value.length">
                                    <x-input-label for="deskripsi_peminjaman" value="Deskripsi Peminjaman*" />
                                    <textarea x-ref="content" @input="count = $event.target.value.length" id="deskripsi_peminjaman" name="deskripsi_peminjaman" rows="4" maxlength="300" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
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