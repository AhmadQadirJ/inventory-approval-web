<x-guest-layout>

    <div class="text-center mb-6"> 
        <h2 class="text-3xl font-extrabold text-gray-900">
            Create Account<span class="text-red-600">.</span>
        </h2>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}" class="space-y-4"> 
        @csrf

        <div>
            <x-input-label for="email" value="Email" class="mb-1" /> 
            <x-text-input id="email" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="email" name="email" :value="old('email')" required placeholder="Type Your Email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div> 
            <x-input-label for="username" value="Username" class="mb-1" /> 
            <x-text-input id="username" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="text" name="username" :value="old('username')" required placeholder="Type Your Username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>
        
        <div> 
            <x-input-label for="nip" value="NIP Number" class="mb-1" /> 
            <x-text-input id="nip" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="text" name="nip" :value="old('nip')" required placeholder="Type Your NIP Number" />
            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
        </div>

        <div> 
            <x-input-label for="branch" value="Branch" class="mb-1" /> 
            <select id="branch" name="branch" class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm bg-gray-100 px-3 py-2" required>
                <option value="">Select Your Branch</option>
                <option value="Jakarta">Jakarta</option>
                <option value="Bandung">Bandung</option>
                <option value="Surabaya">Surabaya</option>
            </select>
            <x-input-error :messages="$errors->get('branch')" class="mt-2" />
        </div>

        <div> 
            <x-input-label for="department" value="Departemen" class="mb-1" /> 
            <select id="department" name="department" class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm bg-gray-100 px-3 py-2" required>
                <option value="">Select Your Departemen</option>
                <option value="Operational">Operational</option>
                <option value="Human Resources">Human Resources</option>
                <option value="General Affair">General Affair</option>
                <option value="Finance and Acc Tax">Finance and Acc Tax</option>
                <option value="Technology">Technology</option>
                <option value="Marketing & Creative">Marketing & Creative</option>
            </select>
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <input type="hidden" name="role" value="Karyawan">

        <div> 
            <x-input-label for="password" value="Password" class="mb-1" /> 
            <x-text-input id="password" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password" required placeholder="Type Your Password"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div> 
            <x-input-label for="password_confirmation" value="Confirm Password" class="mb-1" /> 
            <x-text-input id="password_confirmation" class="block w-full px-3 py-2 bg-gray-100 border-gray-200 rounded-md focus:ring-red-500 focus:border-red-500" type="password" name="password_confirmation" required placeholder="Confirm Your Password"/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-6"> 
            <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Sign Up
            </button>
        </div>
    </form>
    
    <p class="text-sm text-center text-gray-600 mt-6"> 
        Already Have An Account?
        <a href="{{ route('login') }}" class="font-medium text-red-600 hover:text-red-500">
            SIGN IN
        </a>
    </p>

</x-guest-layout>