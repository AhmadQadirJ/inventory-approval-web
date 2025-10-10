<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('inventory') }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>
            
            <div class="bg-white p-8 rounded-lg shadow-sm mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Item: {{ $inventory->nama }}</h2>
                <form action="{{ route('inventory.update', $inventory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @include('inventory._form', ['inventory' => $inventory])
                </form>
            </div>
             <div class="bg-white p-8 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium text-red-600">Delete Item</h3>
                <p class="mt-1 text-sm text-gray-600">Once this item is deleted, all of its data will be permanently removed.</p>
                 <form method="post" action="{{ route('inventory.destroy', $inventory) }}" class="mt-6">
                    @csrf
                    @method('delete')
                    <x-danger-button onclick="return confirm('Are you sure you want to delete this item?');">{{ __('Delete Item') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>