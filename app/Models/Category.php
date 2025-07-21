<?php

namespace Modules\Ecommerce\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Ecommerce\Traits\TranslationTrait;

class Category extends Model
{
    use Sluggable;
    use TranslationTrait;

    protected $table = 'categories';

    public $guarded = [];

    protected $casts = [
        'image' => 'json',
    ];

    protected $appends = ['parent_id', 'translated_languages'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getParentIdAttribute()
    {
        if (isset($this->attributes['parent'])) {
            return $this->parent;
        }
    }

    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model): Builder
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

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany('Modules\Ecommerce\Models\Category', 'parent', 'id')->with('children')->withCount('products');
    }

    /**
     * @return HasMany
     */
    public function subCategories()
    {
        return $this->hasMany('Modules\Ecommerce\Models\Category', 'parent', 'id')->with('subCategories', 'parent')->withCount('products');
    }

    /**
     * @return HasOne
     */
    public function parent()
    {
        return $this->hasOne('Modules\Ecommerce\Models\Category', 'id', 'parent')->with('parent');
    }

    /**
     * @return HasOne
     */
    public function parentCategory()
    {
        return $this->hasOne('Modules\Ecommerce\Models\Category', 'id', 'parent')->with('parentCategory');
    }
}
