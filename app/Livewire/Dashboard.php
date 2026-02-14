<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $estimates = Auth::user()->estimates()->latest()->get();

        return view('livewire.dashboard', [
            'estimates' => $estimates
        ]);
    }
}
