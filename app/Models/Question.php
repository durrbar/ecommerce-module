<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

#[Table('questions')]
#[Unguarded]
#[Appends([
    'positive_feedbacks_count',
    'negative_feedbacks_count',
    'my_feedback',
    'abusive_reports_count',
])]
class Question extends Model
{
    use HasUuids;
    use SoftDeletes;

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the questions feedbacks.
     */
    public function feedbacks(): MorphMany
    {
        return $this->morphMany(Feedback::class, 'model');
    }

    /**
     * Get all of the questions abusive reports.
     */
    public function abusive_reports(): MorphOne
    {
        return $this->morphOne(AbusiveReport::class, 'model');
    }

    /**
     * Positive feedback count of question.
     */
    public function getPositiveFeedbacksCountAttribute(): int
    {
        return $this->feedbacks()->wherePositive(1)->count();
    }

    /**
     * Negative feedback count of question.
     */
    public function getNegativeFeedbacksCountAttribute(): int
    {
        return $this->feedbacks()->whereNegative(1)->count();
    }

    /**
     * Get authenticated user feedback
     *
     * @return object | null
     */
    public function getMyFeedbackAttribute(): ?Feedback
    {
        if (auth()->user()) {
            return $this->feedbacks()->where('user_id', auth()->user()->id)->first();
        }

        return null;
    }

    /**
     * Count no of abusive reports in the question.
     */
    public function getAbusiveReportsCountAttribute(): int
    {
        return $this->abusive_reports()->count();
    }
}
