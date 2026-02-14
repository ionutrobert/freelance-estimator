<div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md text-gray-900 dark:text-gray-100">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-center">Freelance Proposal Time Estimator</h1>
    </div>

    <!-- Toolbar -->
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            
            <!-- Provider -->
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">AI Provider</label>
                <select wire:model.live="provider" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="groq">Groq (Llama 3)</option>
                    <option value="google">Google Gemini</option>
                    <option value="xai">xAI (Grok)</option>
                    <option value="openrouter">OpenRouter</option>
                </select>
            </div>

            <!-- Currency -->
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Currency</label>
                <select wire:model.live="currency" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="CAD">CAD ($)</option>
                    <option value="AUD">AUD ($)</option>
                    <option value="RON">RON (lei)</option>
                </select>
            </div>

            <!-- Hourly Rate Slider -->
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Hourly Rate: <span class="text-indigo-600 dark:text-indigo-400">{{ $hourly_rate }} {{ $currency }}</span></label>
                <div class="flex items-center gap-2">
                    <input type="range" wire:model.live="hourly_rate" min="10" max="500" step="5" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                </div>
            </div>

        </div>

        <!-- API Key Toggle -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
             <button type="button" wire:click="toggleSettings" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                {{ $showSettings ? 'Hide API Key' : 'Using Custom API Key?' }}
            </button>
            
            @if ($showSettings)
                <div class="mt-2">
                    <input type="password" wire:model.live="apiKey" placeholder="Paste custom {{ ucfirst($provider) }} API Key (overrides settings)" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm">
                </div>
            @endif
        </div>
    </div>

    <form wire:submit="estimate">
        <!-- Hidden input for compliance if needed, but slider covers it -->
        
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 mb-2 font-medium">Paste Client Brief / Job Description</label>
            <textarea wire:model="brief" rows="8" class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 placeholder-gray-400" placeholder="e.g. Need a custom Laravel dashboard with user auth, reports, and payment integration..."></textarea>
            @error('brief') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition font-semibold text-lg disabled:opacity-50" wire:loading.attr="disabled" wire:target="estimate">
            <span wire:loading.remove wire:target="estimate">Get Estimate</span>
            <span wire:loading wire:target="estimate" class="hidden">Estimating...</span>
        </button>
    </form>

    @if ($error)
        <div class="mt-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded border border-red-200 dark:border-red-800">
            {{ $error }}
        </div>
    @endif

    @if ($results)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Estimated Breakdown</h2>
                @if($savedEstimateId)
                    <a href="{{ route('estimates.view', $savedEstimateId) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition ease-in-out duration-150">
                        View & Export Document
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                @endif
            </div>

            <div class="space-y-4">
                @foreach ($results['tasks'] ?? [] as $task)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $task['name'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $task['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 whitespace-nowrap">
                                    {{ $task['hours'] }} hrs
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center text-lg mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Total Hours:</span>
                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $results['total_hours'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center text-lg">
                    <span class="text-gray-600 dark:text-gray-400">Suggested Price:</span>
                    <span class="font-bold text-green-600 dark:text-green-400">
                        {{ $currency }} {{ $results['total_price_low'] ?? 'N/A' }} – {{ $results['total_price_high'] ?? 'N/A' }}
                    </span>
                </div>
            </div>
             
            @if (!empty($results['notes']))
                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded border border-yellow-200 dark:border-yellow-800">
                    <strong>Notes:</strong> {{ $results['notes'] }}
                </div>
            @endif
        </div>
    @endif
</div>