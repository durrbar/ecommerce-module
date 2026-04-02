<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Traits\TranslationTrait;
use Modules\User\Models\User;

#[Table('notify_logs')]
#[Unguarded]
#[Hidden([
    'updated_at',
    'deleted_at',
])]
class NotifyLogs extends Model
{
    use HasUuids;
    use SoftDeletes;
    use TranslationTrait;

    public function receiver_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver')->with('profile');
    }

    public function sender_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender')->with('profile');
    }
}
