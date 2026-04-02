<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Ecommerce\Traits\TranslationTrait;
use Modules\Order\Models\DigitalFile;

#[Table('variation_options')]
#[Unguarded]
#[Appends(['blocked_dates'])]
class Variation extends Model
{
    use HasUuids;
    use TranslationTrait;

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getBlockedDatesAttribute(): array
    {
        return $this->getBlockedDates();
    }

    public function getBlockedDates(): array
    {
        $_blockedDates = $this->fetchBlockedDatesForAVariation();
        $_flatBlockedDates = [];
        foreach ($_blockedDates as $date) {
            $from = Carbon::parse($date->from);
            $to = Carbon::parse($date->to);
            $dateRange = CarbonPeriod::create($from, $to);
            $_blockedDates = $dateRange->toArray();
            unset($_blockedDates[count($_blockedDates) - 1]);
            $_flatBlockedDates = array_unique(array_merge($_flatBlockedDates, $_blockedDates));
        }

        return $_flatBlockedDates;
    }

    public function fetchBlockedDatesForAVariation(): Collection
    {
        return Availability::where('bookable_id', $this->id)->where('bookable_type', 'Modules\Ecommerce\Models\Variation')->whereDate('to', '>=', Carbon::now())->get();
    }

    public function digital_file(): MorphOne
    {
        return $this->morphOne(DigitalFile::class, 'fileable');
    }

    public function availabilities(): MorphMany
    {
        return $this->morphMany(Availability::class, 'bookable');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected function casts(): array
    {
        return [
            'options' => 'json',
            'image' => 'json',
        ];
    }

    protected function sku(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => globalSlugify((string) $value, Variation::class, 'sku', '_'),
        );
    }
}
