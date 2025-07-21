<?php

namespace Modules\Ecommerce\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Database\Factories\ProductFactory;
use Modules\Ecommerce\Traits\HasVariant;
use Modules\Review\Traits\ReviewableRateable;
use Spatie\Tags\HasTags;

class ProductTwo extends Model
{
    use HasFactory;
    use HasTags;
    use HasUuids;
    use HasVariant;
    use ReviewableRateable;
    use SoftDeletes;

    protected $table = 'products_two';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'sub_description',
        'price',
        'category',
        'publish',
        'available',
        'price_sale',
        'taxes',
        'quantity',
        'inventory_type',
        'new_label_enabled',
        'new_label_content',
        'sale_label_enabled',
        'sale_label_content',
        'total_sold',
    ];

    protected $casts = [
        'gender' => 'array',
        'new_label' => 'array',
        'sale_label' => 'array',
        'colors' => 'array',
        'price' => 'float',
        'price_sale' => 'float',
        'taxes' => 'float',
    ];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Get all images for the product.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the cover image for the product.
     *
     * @return \App\Models\Image|null
     */
    public function cover(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->orderBy('created_at');
    }

    public function variants(): MorphToMany
    {
        return $this->morphToMany(Variant::class, 'variantable');
    }

    /**
     * Return the tags relationship.
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany($this->getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
