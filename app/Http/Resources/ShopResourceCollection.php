<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;

class ShopResourceCollection extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
