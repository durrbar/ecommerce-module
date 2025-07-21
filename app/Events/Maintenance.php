<?php

namespace Modules\Ecommerce\Events;

class Maintenance
{
    public $language;

    /**
     * The function is a constructor that sets the value of the "language" property.
     *
     * @param string language The "language" parameter is a string that represents the programming
     * language. It is passed to the constructor of a class.
     */
    public function __construct(string $language)
    {
        $this->language = $language;
    }
}
