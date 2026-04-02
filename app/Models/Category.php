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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Ecommerce\Traits\TranslationTrait;

#[Table('categories')]
#[Unguarded]
#[Appends(['parent_id', 'translated_languages'])]
class Category extends Model
{
    use HasUuids;
    use Sluggable;
    use TranslationTrait;

    public function getParentIdAttribute(): int|string|null
    {
        return $this->getAttributes()['parent'] ?? null;
    }

    #[Scope]
    public function withUniqueSlugConstraints(Builder $query, Model $model): Builder
    {
        return $query->where('language', $model->language);
    }

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

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function children(): HasMany
    {
        return $this->hasMany('Modules\Ecommerce\Models\Category', 'parent', 'id')->with('children')->withCount('products');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany('Modules\Ecommerce\Models\Category', 'parent', 'id')->with('subCategories', 'parent')->withCount('products');
    }

    public function parent(): HasOne
    {
        return $this->hasOne('Modules\Ecommerce\Models\Category', 'id', 'parent')->with('parent');
    }

    public function parentCategory(): HasOne
    {
        return $this->hasOne('Modules\Ecommerce\Models\Category', 'id', 'parent')->with('parentCategory');
    }

    protected function casts(): array
    {
        return [
            'image' => 'json',
        ];
    }
}
