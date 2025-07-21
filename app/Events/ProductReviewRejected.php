<?php

namespace Modules\Ecommerce\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Models\Product;

class ProductReviewRejected implements ShouldQueue
{
    /**
     * @var Product
     */
    public $product;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
