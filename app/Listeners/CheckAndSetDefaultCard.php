<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\PaymentMethods;
use Modules\Payment\Models\PaymentMethod;

class CheckAndSetDefaultCard implements ShouldQueue
{
    /**
     * Handle the event.
     *
     */
    public function handle(PaymentMethods $event): void
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

    private function fetchAllPaymentMethods()
    {
        return PaymentMethod::all();
    }
}
