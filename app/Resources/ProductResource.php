<?php

namespace Modules\Ecommerce\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Review\Http\Resources\ReviewResource;
use Modules\Tag\Resources\TagResource;

class ProductResource extends JsonResource
{
    public function ratings(array $reviews): array
    {
        $ratings = [];

        // Initialize the counts for each star rating (from 5 stars to 1 star)
        for ($i = 5; $i >= 1; $i--) {
            $ratings[$i] = [
                'name' => $i . 'star',
                'count' => 0,
            ];
        }

        // Count the number of reviews for each star rating
        foreach ($reviews as $review) {
            $rating = (int)floor($review['rating']); // Convert float rating to integer

            if (isset($ratings[$rating])) {
                $ratings[$rating]['count']++;
            }
        }

        // Reindex the array to match the desired output format (from 5 stars to 1 star)
        return array_values($ratings);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reviews = $this->whenLoaded('reviews', fn() => $this->reviews->toArray());

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'sku' => $this->sku,
            'price' => $this->price,
            'taxes' => $this->taxes,
            'publish' => $this->publish,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'available' => $this->available,
            'totalSold' => $this->total_sold,
            'priceSale' => $this->price_sale,
            'description' => $this->description,
            'inventoryType' => $this->inventory_type,
            'subDescription' => $this->sub_description,
            'createdAt' => $this->whenHas('created_at'),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            // 'images' => ImageResource::collection($this->whenLoaded('images')),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->pluck('url');
            }),

            'gender' => $this->whenLoaded('variants', function () {
                return collect($this->variants)->where('type', 'gender')->pluck('name');
            }),
            'colors' => $this->whenLoaded('variants', function () {
                return collect($this->variants)->where('type', 'color')->pluck('name');
            }),
            'sizes' => $this->whenLoaded('variants', function () {
                return collect($this->variants)->where('type', 'size')->pluck('name');
            }),
            'memories' => $this->whenLoaded('variants', function () {
                return collect($this->variants)->where('type', 'memory')->pluck('name');
            }),

            'coverUrl' => optional($this->whenLoaded('images', function () {
                return $this->images->first();
            }))->url ?? optional($this->whenLoaded('cover'))->url ?? null,
            
            'ratings' => $this->whenLoaded('reviews', function () use ($reviews) {
                return $this->ratings($reviews);
            }),
            'totalReviews' => $this->whenCounted('reviews'),
            'totalRatings' => $this->whenAggregated('reviews', 'rating', 'avg'),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'newLabel' => [
                'enabled' => (bool) $this->new_label_enabled && !empty($this->new_label_content),
                'content' => $this->new_label_enabled && !empty($this->new_label_content) ? $this->new_label_content : null,
            ],
            'saleLabel' => [
                'enabled' => (bool) $this->sale_label_enabled && !empty($this->sale_label_content),
                'content' => $this->sale_label_enabled && !empty($this->sale_label_content) ? $this->sale_label_content : null,
            ],
        ];
    }
}
