<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;

class ProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->whenLoaded('type', fn () => getResourceData($this->type, ['settings'])), // if you need extra data then pass key in array by second parameter
            'language' => $this->language,
            'translated_languages' => $this->translated_languages,
            'product_type' => $this->product_type,
            'shop' => $this->whenLoaded('shop', fn () => getResourceData($this->shop, [])), // if you need extra data then pass key in array by second parameter
            'sale_price' => $this->sale_price,
            'max_price' => $this->max_price,
            'min_price' => $this->min_price,
            'image' => $this->image,
            'status' => $this->status,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'sku' => $this->sku,
            'sold_quantity' => $this->sold_quantity,
            'in_flash_sale' => $this->in_flash_sale,
            'visibility' => $this->visibility,
        ];
    }
}
