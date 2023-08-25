<?php

namespace App\Http\Livewire;

use Livewire\Component;

// Just a component for testing.
class Counter extends Component
{
    public $count = "hi";
 
    public function increment()
    {
        $this->count="Jeff";
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
