<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-text-input id="role_id" type="hidden" value="Patient" @readonly(true)>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>
        
        {{-- Date of Birth --}}
        <div class="mt-4">
            <x-input-label for="date" :value="__('Date of birth:')" />
            <x-text-input id="date" class="block mt-1 w-full"
                            type="date"
                            name="date_of_birth" 
                            value="old('date')" required autocomplete="bdate" />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>

        {{-- Gender Options --}}
        <div class="mt-4">
            <x-input-label for="gender" :value="__('Select Gender:')" autocomplete="sex" />
            <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">{{ __('-- Select Your Role --') }}</option>
                <option value="male" @selected(old('gender') == 'male')>Male</option>
                <option value="female" @selected(old('gender') == 'female')>Female</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        {{-- Blood Type --}}
        <div class="mt-4">
            <x-input-label for="blood" :value="__('Select Blood type:')" />
            <select id="blood" name="blood_type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">{{ __('-- Select Your Blood type --') }}</option>
                <option value="A+" @selected(old('blood_type') == 'A+')>A+</option>
                <option value="A-" @selected(old('blood_type') == 'A-')>A-</option>
                <option value="B+" @selected(old('blood_type') == 'B+')>B+</option>
                <option value="B-" @selected(old('blood_type') == 'B-')>B-</option>
                <option value="AB+" @selected(old('blood_type') == 'AB+')>AB+</option>
                <option value="AB-" @selected(old('blood_type') == 'AB-')>AB-</option>
                <option value="O+" @selected(old('blood_type') == 'O+')>O+</option>
                <option value="O-" @selected(old('blood_type') == 'O-')>O-</option>
            </select>
            <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
