<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Traits\TranslationTrait;

class Availability extends Model
{
    use HasUuids;
    use TranslationTrait;

    protected $table = 'availabilities';

    public $guarded = [];
}
