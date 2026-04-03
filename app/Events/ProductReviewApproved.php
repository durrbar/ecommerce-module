<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Models\Product;

class ProductReviewApproved implements ShouldQueue
{
    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Product $product) {}
}
