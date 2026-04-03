<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

class Maintenance
{
    /**
     * The function is a constructor that sets the value of the "language" property.
     *
     * @param string language The "language" parameter is a string that represents the programming
     * language. It is passed to the constructor of a class.
     */
    public function __construct(public string $language) {}
}
