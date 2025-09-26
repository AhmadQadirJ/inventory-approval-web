<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Form Edit Informasi --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                <form method="post" action="{{ route('user-management.update', $user->id) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')
                    {{-- Name, Email, Photo, Role --}}
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    </div>
                     <div>
                        <x-input-label for="profile_photo" :value="__('New Photo (Optional)')" />
                        <x-text-input id="profile_photo" name="profile_photo" type="file" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @if($user->role == $role) selected @endif>{{ $role }}</option>
                            @endforeach
                        </select>
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