<nav x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center"> {{-- Pembungkus untuk Logo dan Menu --}}
                {{-- Logo --}}
                <div class="shrink-0 flex items-center pr-8"> {{-- Padding kanan agar tidak terlalu dekat dengan menu --}}
                    <a href="{{ route('dashboard') }}" class="text-3xl font-extrabold text-gray-900">
                        INV<span class="text-red-600">.</span>
                    </a>
                </div>

                {{-- Menu Navigasi --}}
                <div class="hidden sm:flex h-full items-center"> {{-- Menggunakan h-full dan items-center untuk vertikal align --}}
                    {{-- Home --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-6 h-full flex items-center">
                        Home
                    </x-nav-link>
                    <div class="h-8 border-l border-gray-300 mx-2"></div> {{-- Pemisah vertikal --}}

                    {{-- Submission --}}
                    <x-nav-link :href="route('submission')" :active="request()->routeIs('submission')" class="px-6 h-full flex items-center">
                        Submission
                    </x-nav-link>
                    <div class="h-8 border-l border-gray-300 mx-2"></div> {{-- Pemisah vertikal --}}

                    {{-- History --}}
                    <x-nav-link :href="route('history')" :active="request()->routeIs('history')" class="px-6 h-full flex items-center">
                        History
                    </x-nav-link>
                    <div class="h-8 border-l border-gray-300 mx-2"></div> {{-- Pemisah vertikal --}}

                    {{-- Inventory --}}
                    <x-nav-link :href="route('inventory')" :active="request()->routeIs('inventory')" class="px-6 h-full flex items-center">
                        Inventory
                    </x-nav-link>

                    {{-- Menu Conditional (Approval/User Management) --}}
                    @if (in_array(Auth::user()->role, ['General Affair', 'Manager', 'Finance', 'COO']))
                        <div class="h-8 border-l border-gray-300 mx-2"></div> {{-- Pemisah vertikal --}}
                        <x-nav-link :href="route('approval')" :active="request()->routeIs('approval')" class="px-6 h-full flex items-center">
                            Approval
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->role == 'Admin')
                        <div class="h-8 border-l border-gray-300 mx-2"></div> {{-- Pemisah vertikal --}}
                        <x-nav-link :href="route('user-management')" :active="request()->routeIs('user-management')" class="px-6 h-full flex items-center">
                            User Management
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Bagian Kanan: Foto Profil dan Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out">
                            {{-- Foto Profil --}}
                            @if (Auth::user()->profile_photo_path)
                            {{-- Jika punya foto, tampilkan dari storage --}}
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}">
                            @else
                                {{-- Jika tidak punya, tampilkan avatar default --}}
                                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                            @endif
                            {{-- Nama User (Opsional, sesuai gambar) --}}
                            {{-- <div>{{ Auth::user()->name }}</div> --}}
                            {{-- Ikon Dropdown --}}
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-gray-700 hover:bg-gray-100">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger (Tidak berubah banyak) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Navigation (Mobile) --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
             <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Home
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('submission')" :active="request()->routeIs('submission')">
                Submission
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('history')" :active="request()->routeIs('history')">
                History
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inventory')" :active="request()->routeIs('inventory')">
                Inventory
            </x-responsive-nav-link>

            @if (in_array(Auth::user()->role, ['General Affair', 'Manager', 'Finance', 'COO']))
                <x-responsive-nav-link :href="route('approval')" :active="request()->routeIs('approval')">
                    Approval
                </x-responsive-nav-link>
            @endif

             @if (Auth::user()->role == 'Admin')
                <x-responsive-nav-link :href="route('user-management')" :active="request()->routeIs('user-management')">
                    User Management
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>