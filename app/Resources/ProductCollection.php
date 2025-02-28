<?php

namespace Modules\Ecommerce\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Traits\HasPagination;

class ProductCollection extends ResourceCollection
{
    use HasPagination;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->pagination(),
        ];
    }
}
