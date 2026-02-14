<?php

namespace App\Livewire;

use App\Models\Estimate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ViewEstimate extends Component
{
    use AuthorizesRequests;

    public Estimate $estimate;

    public function mount(Estimate $estimate)
    {
        if ($estimate->user_id !== auth()->id()) {
            abort(403);
        }
        $this->estimate = $estimate;
    }

    public function delete()
    {
        if ($this->estimate->user_id !== auth()->id()) {
            abort(403);
        }
        $this->estimate->delete();
        return redirect()->route('dashboard');
    }

    public function getEmailHtml()
    {
        return view('emails.estimate-share', ['estimate' => $this->estimate])->render();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.view-estimate');
    }
}
