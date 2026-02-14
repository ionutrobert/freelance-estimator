<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    
    <div class="md:grid md:grid-cols-3 md:gap-6 mb-10">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Profile & Rates</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your default billing information and profile.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                Name 
                                <span class="text-xs text-gray-400 font-normal ml-1">(Displayed on proposals)</span>
                            </label>
                            <div class="relative mt-1">
                                <input type="text" wire:model.blur="name" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" 
                                     x-data="{ show: false }" 
                                     x-on:name-updated.window="show = true; setTimeout(() => show = false, 2000)"
                                     x-show="show" 
                                     x-transition.opacity.out.duration.1500ms 
                                     style="display: none;">
                                    <span class="text-sm text-green-600">Saved.</span>
                                </div>
                            </div>
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Hourly Rate & Currency Group -->
                        <div class="col-span-6 sm:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Hourly Rate -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Default Hourly Rate
                                </label>
                                <input type="number" step="0.01" wire:model.blur="hourly_rate" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('hourly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Currency -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Currency
                                    <span class="text-xs text-gray-400 font-normal ml-1 opacity-0">Spacer</span>
                                </label>
                                <select wire:model.blur="currency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="CAD">CAD ($)</option>
                                    <option value="AUD">AUD ($)</option>
                                    <option value="RON">RON (lei)</option>
                                </select>
                                @error('currency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden sm:block">
        <div class="py-5">
            <div class="border-t border-gray-200 dark:border-gray-700"></div>
        </div>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">API Keys</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage your AI provider keys. These are encrypted and stored securely.
                </p>
                <div class="mt-4 text-xs text-gray-500">
                    <p><strong>Groq:</strong> <a href="https://console.groq.com" target="_blank" class="underline">Get Key (free)</a></p>
                    <p><strong>Google:</strong> <a href="https://aistudio.google.com/" target="_blank" class="underline">Get Key (free)</a></p>
                    <p><strong>OpenRouter:</strong> <a href="https://openrouter.ai/" target="_blank" class="underline">Get Key</a></p>
                    <p><strong>xAI:</strong> <a href="https://console.x.ai/" target="_blank" class="underline">Get Key</a></p>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 space-y-6">
                    
                    @foreach($providers as $provider)
                        @php
                            $placeholder = match($provider) {
                                'groq' => 'gsk_...',
                                'xai' => 'xai-...',
                                'google' => 'AIzaSy...',
                                'openrouter' => 'sk-or-...'
                            };
                            $hasKey = !empty($apiKeys[$provider]);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 capitalize">{{ $provider }} API Key</label>
                                @if (session()->has("api-saved-$provider"))
                                    <span class="text-xs text-green-600 font-semibold animate-pulse">{{ session("api-saved-$provider") }}</span>
                                @endif
                            </div>
                            
                            <div class="flex rounded-md shadow-sm">
                                <input type="password" wire:model="apiKeys.{{ $provider }}" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="{{ $placeholder }}">
                                
                                <button type="button" wire:click="saveApiKey('{{ $provider }}')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-r-md text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    Save
                                </button>
                            </div>
                            
                            @if($hasKey)
                                <div class="mt-1 text-right">
                                    <button type="button" wire:click="deleteApiKey('{{ $provider }}')" class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 underline">
                                        Remove Key
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
