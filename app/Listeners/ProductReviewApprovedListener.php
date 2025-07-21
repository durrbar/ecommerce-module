<?php

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\ProductReviewApproved;
use Modules\Notification\Notifications\ProductApprovedNotification;

class ProductReviewApprovedListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  ProductReview  $event
     * @return void
     */
    public function handle(ProductReviewApproved $event)
    {
        $vendor = $event->product->shop->owner;
        $vendor->notify(new ProductApprovedNotification($event->product));
    }
}
