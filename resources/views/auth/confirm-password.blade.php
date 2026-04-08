<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="dark:text-gray-200" />

            <x-text-input 
                id="password" 
                class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2 dark:text-red-400" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="dark:bg-blue-600 dark:hover:bg-blue-500 dark:text-white">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
