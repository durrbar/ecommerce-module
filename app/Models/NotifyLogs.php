<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Traits\TranslationTrait;

class NotifyLogs extends Model
{
    use SoftDeletes;
    use TranslationTrait;

    protected $table = 'notify_logs';

    public $guarded = [];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function receiver_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver')->with('profile');
    }

    public function sender_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender')->with('profile');
    }
}
