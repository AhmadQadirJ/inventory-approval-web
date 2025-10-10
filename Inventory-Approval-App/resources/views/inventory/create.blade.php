<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('inventory') }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>

            <div class="bg-white p-8 rounded-lg shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Item to Inventory</h2>
                <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('inventory._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>