<div x-data="{ kategori: '{{ old('kategori', $inventory->kategori ?? 'Elektronik') }}' }" class="space-y-6">
    {{-- NAMA --}}
    <div>
        <x-input-label for="nama" value="Nama Barang*" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $inventory->nama ?? '')" required />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- KATEGORI --}}
        <div>
            <x-input-label for="kategori" value="Kategori*" />
            <select x-model="kategori" id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="Elektronik">Elektronik</option>
                <option value="Non Elektronik">Non Elektronik</option>
                <option value="Ruangan">Ruangan</option>
            </select>
        </div>

        {{-- BRANCH --}}
        <div>
            <x-input-label for="branch" value="Branch*" />
            <select id="branch" name="branch" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="Bandung" @if(old('branch', $inventory->branch ?? '') == 'Bandung') selected @endif>Bandung</option>
                <option value="Jakarta" @if(old('branch', $inventory->branch ?? '') == 'Jakarta') selected @endif>Jakarta</option>
                <option value="Surabaya" @if(old('branch', $inventory->branch ?? '') == 'Surabaya') selected @endif>Surabaya</option>
            </select>
        </div>
    </div>

    {{-- Form yang bisa disembunyikan --}}
    <div x-show="kategori !== 'Ruangan'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- BRAND --}}
            <div>
                <x-input-label for="brand" value="Brand" />
                <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand', $inventory->brand ?? '')" />
            </div>
             {{-- HARGA --}}
            <div>
                <x-input-label for="harga" value="Harga" />
                <x-text-input id="harga" name="harga" type="number" class="mt-1 block w-full" :value="old('harga', $inventory->harga ?? '')" />
            </div>
            {{-- TAHUN BELI --}}
            <div>
                <x-input-label for="tahun_beli" value="Tahun Beli" />
                <x-text-input id="tahun_beli" name="tahun_beli" type="number" placeholder="YYYY" class="mt-1 block w-full" :value="old('tahun_beli', $inventory->tahun_beli ?? '')" />
            </div>
             {{-- NAMA VENDOR --}}
            <div>
                <x-input-label for="nama_vendor" value="Nama Vendor" />
                <x-text-input id="nama_vendor" name="nama_vendor" type="text" class="mt-1 block w-full" :value="old('nama_vendor', $inventory->nama_vendor ?? '')" />
            </div>
        </div>
         {{-- VENDOR LINK --}}
        <div>
            <x-input-label for="vendor_link" value="Link Vendor" />
            <x-text-input id="vendor_link" name="vendor_link" type="url" placeholder="https://..." class="mt-1 block w-full" :value="old('vendor_link', $inventory->vendor_link ?? '')" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- QTY --}}
        <div>
            <x-input-label for="qty" value="Quantity*" />
            <x-text-input id="qty" name="qty" type="number" class="mt-1 block w-full" :value="old('qty', $inventory->qty ?? 0)" required />
        </div>
        {{-- GAMBAR --}}
        <div>
             <x-input-label for="gambar" value="Gambar (Opsional)" />
             <input id="gambar" name="gambar" type="file" class="mt-1 block w-full">
        </div>
    </div>

    {{-- DESKRIPSI --}}
    <div>
        <x-input-label for="deskripsi" value="Deskripsi" />
        <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi', $inventory->deskripsi ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ isset($inventory) ? 'Update Item' : 'Save Item' }}</x-primary-button>
        <a href="{{ route('inventory') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
    </div>
</div>