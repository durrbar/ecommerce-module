<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ecommerce\Traits\TranslationTrait;

#[Table('manufacturers')]
#[Unguarded]
#[Appends(['products_count', 'translated_languages'])]
class Manufacturer extends Model
{
    use HasUuids;
    use Sluggable;
    use TranslationTrait;

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

    #[Scope]
    public function withUniqueSlugConstraints(Builder $query, Model $model): Builder
    {
        return $query->where('language', $model->language);
    }

    public function getProductsCountAttribute()
    {
        if (array_key_exists('products_count', $this->getAttributes())) {
            return (int) $this->getAttributes()['products_count'];
        }

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

    protected function casts(): array
    {
        return [
            'image' => 'json',
            'cover_image' => 'json',
            'socials' => 'json',
        ];
    }
}
