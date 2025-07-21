<?php

namespace Modules\Ecommerce\Listeners;

use Exception;
use Illuminate\Support\Facades\Mail;
use Modules\Ecommerce\Events\CommissionRateUpdateEvent;
use Modules\Ecommerce\Mail\AdminCommissionRateUpdate;
use Modules\Ecommerce\Mail\VendorCommissionRateUpdate;
use Modules\Notification\Traits\SmsTrait;
use Modules\User\Traits\UsersTrait;

class CommissionRateUpdateListener
{
    use SmsTrait;
    use UsersTrait;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CommissionRateUpdateEvent $event)
    {
        $shop = $event->shop;
        $balance = $event->balance;

        try {
            $admins = $this->getAdminUsers();
            if ($admins) {
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminCommissionRateUpdate($shop, $balance));
                }
            }
            Mail::to($shop->owner->email)->send(new VendorCommissionRateUpdate($shop, $balance));
        } catch (Exception $e) {
            logger('Error in CommissionRateUpdateListener! '.$e->getMessage());
        }

    }
}
