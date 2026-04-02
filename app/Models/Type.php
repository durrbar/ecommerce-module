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
use Illuminate\Database\Eloquent\Relations\hasMany;
use Modules\Ecommerce\Traits\TranslationTrait;

#[Table('types')]
#[Unguarded]
#[Appends(['translated_languages'])]
class Type extends Model
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

    public function products(): hasMany
    {
        return $this->hasMany(Product::class, 'type_id');
    }

    public function categories(): hasMany
    {
        return $this->hasMany(Category::class, 'type_id');
    }

    public function banners(): hasMany
    {
        return $this->hasMany(Banner::class, 'type_id');
    }

    protected function casts(): array
    {
        return [
            'promotional_sliders' => 'json',
            'settings' => 'json',
        ];
    }
}
