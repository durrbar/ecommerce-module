<?php

namespace Modules\Ecommerce\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ecommerce\Traits\TranslationTrait;

class Manufacturer extends Model
{
    use HasUuids;
    use Sluggable;
    use TranslationTrait;

    protected $table = 'manufacturers';

    public $guarded = [];

    public $appends = ['products_count', 'translated_languages'];

    protected $casts = [
        'image' => 'json',
        'cover_image' => 'json',
        'socials' => 'json',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model): Builder
    {
        return $query->where('language', $model->language);
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacturer_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
