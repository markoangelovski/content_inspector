<?php

namespace App\Livewire\Pages;

use App\Models\Website;
use Livewire\Component;

class Listing extends Component
{
    public Website $website;

    public function mount(Website $website)
    {
        $this->website = $website;
    }

    public function render()
    {
        return view('livewire.pages.listing');
    }
}
