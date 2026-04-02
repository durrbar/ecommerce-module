<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Traits\TranslationTrait;

#[Table('availabilities')]
#[Unguarded]
class Availability extends Model
{
    use HasUuids;
    use TranslationTrait;
}
