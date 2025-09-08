<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-800">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Welcome Back<span class="text-red-600">.</span>
                </h2>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="email" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Type Your Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <x-input-label for="password" value="Password" class="text-sm font-medium text-gray-700"/>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-gray-600 rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        @endif
                    </div>
                    <x-text-input id="password" class="block w-full px-3 py-2 mt-1 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password" required autocomplete="current-password" placeholder="Type Your Password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="text-red-600 border-gray-300 rounded shadow-sm focus:ring-red-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Sign In
                    </button>
                </div>
            </form>

             <p class="text-sm text-center text-gray-600">
                Or Sign Up Using
                <a href="{{ route('register') }}" class="font-medium text-red-600 hover:text-red-500">
                    SIGN UP
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>