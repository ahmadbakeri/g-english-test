<div>
    @if ($isSubmitted)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center" role="alert">
            <h3 class="font-bold text-lg mb-2">{{ __('Registration Successful!') }}</h3>
            <p>{{ __('Thank you, we have received your registration data.') }}</p>
            <p class="mt-2">{{ __('Your Registration Number:') }} <strong class="text-2xl block mt-2">{{ $registrationNumber }}</strong></p>
            <p class="mt-4 text-sm">{{ __('Please save this registration number for re-registration purposes. Our team will contact you shortly.') }}</p>
            
            <div class="mt-6">
                <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition duration-300">
                    {{ __('Return to Home') }}
                </a>
            </div>
        </div>
    @else
        <form wire:submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">{{ __('New Student Registration Form') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4 md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        {{ __('Full Name *') }}
                    </label>
                    <input wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('name') border-red-500 @enderror" id="name" type="text" placeholder="{{ __('Enter full name') }}">
                    @error('name') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        {{ __('Email') }}
                    </label>
                    <input wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('email') border-red-500 @enderror" id="email" type="email" placeholder="{{ __('Active email') }}">
                    @error('email') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        {{ __('Phone / WhatsApp Number') }}
                    </label>
                    <input wire:model="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('phone') border-red-500 @enderror" id="phone" type="text" placeholder="08xxxxxxxxxx">
                    @error('phone') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="birth_date">
                        {{ __('Date of Birth') }}
                    </label>
                    <input wire:model="birth_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('birth_date') border-red-500 @enderror" id="birth_date" type="date">
                    @error('birth_date') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="previous_school">
                        {{ __('Previous School') }}
                    </label>
                    <input wire:model="previous_school" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('previous_school') border-red-500 @enderror" id="previous_school" type="text" placeholder="{{ __('Previous school name') }}">
                    @error('previous_school') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                        {{ __('Full Address') }}
                    </label>
                    <textarea wire:model="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('address') border-red-500 @enderror" id="address" rows="3" placeholder="{{ __('Full home address') }}"></textarea>
                    @error('address') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="parent_name">
                        {{ __('Parent/Guardian Name') }}
                    </label>
                    <input wire:model="parent_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('parent_name') border-red-500 @enderror" id="parent_name" type="text" placeholder="{{ __('Parent/Guardian name') }}">
                    @error('parent_name') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="parent_phone">
                        {{ __('Parent Phone Number') }}
                    </label>
                    <input wire:model="parent_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('parent_phone') border-red-500 @enderror" id="parent_phone" type="text" placeholder="08xxxxxxxxxx">
                    @error('parent_phone') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-full focus:outline-none focus:shadow-outline transition duration-300" type="submit">
                    {{ __('Register Now') }}
                </button>
            </div>
        </form>
    @endif
</div>
