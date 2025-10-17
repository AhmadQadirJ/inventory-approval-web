<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-800">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Create Account<span class="text-red-600">.</span>
                </h2>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="email" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Type Your Email"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="name" value="Username" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="name" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Type Your Username"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="NIP" value="NIP Number" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="NIP" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="integer" name="NIP" :value="old('NIP')" required autocomplete="username" placeholder="Type Your NIP Number"/>
                    <x-input-error :messages="$errors->get('NIP')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" value="Role" class="text-sm font-medium text-gray-700" />
                    <select id="role" name="role" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500">
                        <option value="Karyawan">Karyawan</option>
                        <option value="General Affair">General Affair</option>
                        <option value="Finance">Finance</option>
                        <option value="COO">COO</option>
                        <option value="CHRD">CHRD</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" value="Password" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="password" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password" required autocomplete="new-password" placeholder="Type Your Password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Confirm Password" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="password_confirmation" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Your Password"/>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>


                <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Sign Up
                </button>
            </form>

            <p class="text-sm text-center text-gray-600">
                Already Have An Account?
                <a href="{{ route('login') }}" class="font-medium text-red-600 hover:text-red-500">
                    SIGN IN
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>