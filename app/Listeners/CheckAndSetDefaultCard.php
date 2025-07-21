<?php

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\PaymentMethods;
use Modules\Payment\Models\PaymentMethod;

class CheckAndSetDefaultCard implements ShouldQueue
{
    protected function fetchAllPaymentMethods()
    {
        return PaymentMethod::all();
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PaymentMethods $event)
    {
        $currentPaymentMethods = $event->payment_methods;
        $allPaymentMethods = $this->fetchAllPaymentMethods();

        if ($currentPaymentMethods->default_card) {
            foreach ($allPaymentMethods as $paymentMethod) {
                if ($paymentMethod->method_key !== $currentPaymentMethods->method_key) {
                    $paymentMethod->default_card = false;
                    $paymentMethod->save();
                }
            }
        }
    }
}
