<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\ProductReviewRejected;
use Modules\Notification\Notifications\ProductRejectedNotification;

class ProductReviewRejectedListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  ProductReview  $event
     */
    public function handle(ProductReviewRejected $event): void
    {
        $vendor = $event->product->shop->owner;
        $vendor->notify(new ProductRejectedNotification($event->product));
    }
}
