<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('banners')]
#[Unguarded]
class Banner extends Model
{
    use HasUuids;

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    protected function casts(): array
    {
        return [
            'image' => 'json',
        ];
    }
}
