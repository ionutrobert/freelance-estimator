<?php

namespace App\Livewire;

use App\Models\UserApiKey;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Settings extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|numeric|min:0')]
    public $hourly_rate = 0;

    #[Validate('required|string|size:3')]
    public $currency = 'USD';

    public $apiKeys = []; // ['groq' => '...', 'xai' => '...']

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->hourly_rate = $user->hourly_rate;
        $this->currency = $user->currency;

        // Load existing keys
        foreach ($user->apiKeys as $key) {
            $this->apiKeys[$key->provider] = $key->api_key;
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['name', 'hourly_rate', 'currency'])) {
            $this->validate();
            
            Auth::user()->update([
                'name' => $this->name,
                'hourly_rate' => $this->hourly_rate,
                'currency' => $this->currency,
            ]);

            if ($property === 'name') {
                $this->dispatch('profile-updated', name: $this->name);
                $this->dispatch('name-updated');
            } else {
                $this->dispatch('profile-updated', name: $this->name); // Keep navbar in sync even for other updates
                $this->dispatch('settings-updated');
            }
        }
    }

    public function saveApiKey($provider)
    {
        if (empty($this->apiKeys[$provider])) return;

        UserApiKey::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => $provider],
            ['api_key' => $this->apiKeys[$provider]]
        );

        session()->flash("api-saved-$provider", 'Saved');
    }

    public function deleteApiKey($provider)
    {
        UserApiKey::where('user_id', Auth::id())
            ->where('provider', $provider)
            ->delete();

        unset($this->apiKeys[$provider]);
        // session()->flash("api-saved-$provider", 'Removed'); 
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.settings', [
            'providers' => ['groq', 'xai', 'google', 'openrouter']
        ]);
    }
}
