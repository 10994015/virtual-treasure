<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class SimpleMessage extends Component
{
    public $message = '';
    public $sent = '';

    public function send()
    {
        $this->sent = $this->message;
        $this->message = '';
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return <<<'HTML'
        <div>
            <input type="text" wire:model="message" placeholder="Message">
            <button type="button" wire:click="send">Send</button>
            @if($sent)
                <p>Sent: {{ $sent }}</p>
            @endif
        </div>
        HTML;
    }
}
