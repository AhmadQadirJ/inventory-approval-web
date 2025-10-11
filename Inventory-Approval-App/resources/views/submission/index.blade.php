<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    <span class="font-medium">Success!</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 text-center">
                    <h2 class="text-2xl font-bold">Create a new submission</h2>
                    <p class="text-gray-500 mt-2">Choose the submission type you want to create</p>

                    <div class="mt-8">
                        <h3 class="text-xl font-semibold">Submission Type</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                            {{-- Asset Lending Card --}}
                            <a href="{{ route('submission.lend.create') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 text-center transition">
                                <div class="flex justify-center mb-4">
                                    <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center">
                                        {{-- Ganti dengan SVG Ikon Dokumen --}}
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                </div>
                                <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900">Asset Lending</h5>
                                <p class="font-normal text-sm text-gray-700">Pinjam barang yang sudah tersedia dalam inventaris untuk kebutuhan sementara</p>
                            </a>

                            {{-- Asset Procurement Card --}}
                            <a href="{{ route('submission.procure.create') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 text-center transition">
                                <div class="flex justify-center mb-4">
                                    <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center">
                                        {{-- Ganti dengan SVG Ikon Keranjang Belanja --}}
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                </div>
                                <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900">Asset Procurement</h5>
                                <p class="font-normal text-sm text-gray-700">Ajukan pembelian barang baru yang belum tersedia dalam inventaris</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>