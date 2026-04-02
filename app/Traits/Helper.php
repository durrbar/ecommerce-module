<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Traits;

trait Helper
{
    /**
     * Format billing, shipping address
     */
    public function formatAddress(?array $address): ?string
    {
        if (! $address) {
            return null;
        }

        return $address['street_address'].', '.$address['zip'].'-'.$address['city'].', '.$address['state'].', '.$address['country'];
    }
}
