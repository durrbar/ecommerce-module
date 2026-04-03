<?php

declare(strict_types=1);

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
     */
    public function handle(ProductReviewApproved $event): void
    {
        $vendor = $event->product->shop->owner;
        $vendor->notify(new ProductApprovedNotification($event->product));
    }
}
