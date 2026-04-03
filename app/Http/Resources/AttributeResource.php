<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;

class AttributeResource extends Resource
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
            'shop_id' => $this->shop_id,
            'language' => $this->language,
            'translated_languages' => $this->translated_languages,
            'slug' => $this->slug,
            'type' => $this->whenLoaded('type', fn () => getResourceData($this->type, []), null), // if you need extra data then pass key in array by second parameter
            'values' => AttributeValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
