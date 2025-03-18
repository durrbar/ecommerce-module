<?php

namespace Modules\Ecommerce\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Review\Http\Resources\ReviewResource;
use Modules\Tag\Resources\TagResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
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

            // Relationships
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => $this->whenLoaded('images', fn() => $this->images->pluck('url')),

            // Variant types (dynamic generation)
            ...$this->whenLoaded('variants', function () {
                return collect(['gender', 'color', 'size', 'memory'])
                    ->mapWithKeys(fn($type) => [
                        Str::plural($type) => $this->variants
                            ->where('type', $type)
                            ->pluck('name')
                            ->values()
                            ->all()
                    ])
                    ->filter(fn($value) => !empty($value))
                    ->all();
            }, []),

            // Media
            'coverUrl' => $this->whenLoaded('cover', fn() => $this->cover?->url)
                ?? $this->whenLoaded('images', fn() => $this->images->first()?->url)
                ?? null,

            // Reviews and ratings
            'ratings' => $this->whenLoaded('reviews', fn() => $this->calculateRatings()),
            'totalReviews' => $this->whenCounted('reviews'),
            'totalRatings' => $this->whenAggregated('reviews', 'rating', 'avg'),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),

            // Labels
            'newLabel' => $this->formatLabel('new'),
            'saleLabel' => $this->formatLabel('sale'),
        ];
    }

    protected function calculateRatings(): array
    {
        $reviews = $this->reviews->toArray();
        $ratings = collect(range(1, 5))->mapWithKeys(fn($i) => [
            $i => ['name' => "{$i}star", 'count' => 0]
        ])->all();

        foreach ($reviews as $review) {
            $rating = (int)floor($review['rating']);
            $ratings[$rating]['count']++;
        }

        return array_values($ratings);
    }

    protected function formatLabel(string $type): array
    {
        $enabled = $this->{"{$type}_label_enabled"} && $this->{"{$type}_label_content"};
        return [
            'enabled' => $enabled,
            'content' => $enabled ? $this->{"{$type}_label_content"} : null,
        ];
    }
}
