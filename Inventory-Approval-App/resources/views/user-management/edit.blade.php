<x-app-layout>
    <div class="py-12">
        
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center mb-4 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Return
            </a>
            {{-- Form Edit Informasi --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" x-data="{ selectedRole: '{{ old('role', $user->role) }}' }">
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                
                <form method="post" action="{{ route('user-management.update', $user->id) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')
                    
                    {{-- Name, Email, NIP, Role --}}
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    </div>
                    <div>
                        <x-input-label for="nip" :value="__('NIP')" />
                        <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip', $user->nip)" />
                        <x-input-error class="mt-2" :messages="$errors->get('nip')" />
                    </div>
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" x-model="selectedRole" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Blok Kondisional 1: Branch Karyawan --}}
                    <div x-show="selectedRole === 'Karyawan'" x-transition class="space-y-6 border-t pt-4">
                        <div>
                            <x-input-label for="branch_karyawan" :value="__('Branch Karyawan')" />
                            <select id="branch_karyawan" name="branch" :disabled="selectedRole !== 'Karyawan'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Branch --</option>
                                @foreach ($karyawan_branches as $branch)
                                    <option value="{{ $branch }}" @if(old('branch', $user->branch) == $branch) selected @endif>{{ $branch }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('branch')" />
                        </div>
                    </div>

                    {{-- Blok Kondisional 2: Branch Management --}}
                    <div x-show="['General Affair', 'Finance', 'COO', 'CHRD'].includes(selectedRole)" x-transition class="border-t pt-4">
                        <div>
                            <x-input-label for="branch_management" :value="__('Branch Management')" />
                            <select id="branch_management" name="branch" :disabled="!['General Affair', 'Finance', 'COO', 'CHRD'].includes(selectedRole)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Branch --</Toption>
                                @foreach ($management_branches as $branch)
                                    <option value="{{ $branch }}" @if(old('branch', $user->branch) == $branch) selected @endif>{{ $branch }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('branch')" />
                        </div>
                    </div>
                    
                    {{-- Blok Kondisional 3: Departemen (untuk Karyawan, GA, Finance) --}}
                    <div x-show="['Karyawan', 'General Affair', 'Finance'].includes(selectedRole)" x-transition class="space-y-6 border-t pt-4">
                        <div>
                            <x-input-label for="department" :value="__('Departemen')" />
                            <select id="department" name="department" :disabled="!['Karyawan', 'General Affair', 'Finance'].includes(selectedRole)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach ($karyawan_departments as $dept)
                                    <option value="{{ $dept }}" @if(old('department', $user->department) == $dept) selected @endif>{{ $dept }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('department')" />
                        </div>
                    </div>

                    {{-- Foto & Tombol Save --}}
                    <div class="border-t pt-6">
                        <x-input-label for="profile_photo" :value="__('New Photo (Optional)')" />
                        <x-text-input id="profile_photo" name="profile_photo" type="file" class="mt-1 block w-full" />
                    </div>
                    <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                </form>
            </div>

            {{-- Form Update Password --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                 <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
                 <form method="post" action="{{ route('user-management.update', $user->id) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="password" :value="__('New Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>
                    <x-primary-button>{{ __('Update Password') }}</x-primary-button>
                </form>
            </div>

            {{-- Form Delete User --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-red-600">Delete User</h2>
                <p class="mt-1 text-sm text-gray-600">Once this account is deleted, all of its resources and data will be permanently deleted.</p>
                 <form method="post" action="{{ route('user-management.destroy', $user->id) }}" class="mt-6">
                    @csrf
                    @method('delete')
                    <x-danger-button onclick="return confirm('Are you sure you want to delete this user?');">{{ __('Delete Account') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>