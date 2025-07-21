<?php

namespace Modules\Ecommerce\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Modules\Ecommerce\Traits\TranslationTrait;

class Type extends Model
{
    use Sluggable;
    use TranslationTrait;

    protected $appends = ['translated_languages'];

    protected $table = 'types';

    public $guarded = [];

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

    protected $casts = [
        'promotional_sliders' => 'json',
        'settings' => 'json',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'type_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'type_id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'type_id');
    }
}
