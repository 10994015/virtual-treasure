<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CheckoutSuccessComponent extends Component
{
    public $order;

    public function mount($order)
    {
        $orderNumber = $order;
        $this->order = Order::where('order_number', $orderNumber)->with('items')->first();

        if (!$this->order) {
            return redirect()->route('market');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.checkout-success-component');
    }
}
