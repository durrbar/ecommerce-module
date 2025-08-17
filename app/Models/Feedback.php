<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class Feedback extends Model
{
    use HasUuids;
    
    protected $table = 'feedbacks';

    public $guarded = [];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the models that own feedbacks.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
