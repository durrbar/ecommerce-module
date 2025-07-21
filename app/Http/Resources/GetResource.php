<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;

class GetResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
