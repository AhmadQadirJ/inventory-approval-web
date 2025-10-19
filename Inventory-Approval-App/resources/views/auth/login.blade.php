<x-guest-layout>

    <div class="text-center mb-6"> <h2 class="text-2xl font-bold text-gray-900">
            Welcome Back<span class="text-red-600">.</span>
        </h2>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4"> @csrf

        <div>
            <x-input-label for="username" value="Username" class="text-sm font-medium text-gray-700 mb-1" /> <x-text-input id="email" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Type Your Username" /> <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2"> <x-input-label for="password" value="Password" class="text-sm font-medium text-gray-700 mb-1"/> <x-text-input id="password" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password" required autocomplete="current-password" placeholder="Type Your Password"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        @if (Route::has('password.request'))
            <div class="flex justify-end text-sm"> <a class="text-gray-600 rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            </div>
        @endif

        <div class="pt-6"> <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Sign In
            </button>
        </div>
    </form>

    <p class="text-sm text-center text-gray-600 mt-6"> Or Sign Up Using
        <a href="{{ route('register') }}" class="font-medium text-red-600 hover:text-red-500">
            SIGN UP
        </a>
    </p>

</x-guest-layout>