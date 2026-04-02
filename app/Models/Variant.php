<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table('variants')]
#[Fillable(['name', 'type'])]
class Variant extends Model
{
    use HasFactory;
    use HasUuids;

    public function variantable(): MorphTo
    {
        return $this->morphTo();
    }
}
