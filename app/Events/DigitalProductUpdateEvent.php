<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Models\Product;
use Modules\User\Models\User;

class DigitalProductUpdateEvent implements ShouldQueue
{
    /**
     * Create a new event instance.
     *
     * @param  $flash_sale
     */
    public function __construct(
        public Product $product,
        public User $user,
        public mixed $optional_data = null
    ) {}
}
