<?php

namespace Modules\Ecommerce\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Traits\TranslationTrait;
use Modules\Order\Models\DigitalFile;

class Variation extends Model
{
    use TranslationTrait;

    protected $table = 'variation_options';

    public $guarded = [];

    protected $appends = ['blocked_dates'];

    protected $casts = [
        'options' => 'json',
        'image' => 'json',
    ];

    protected function sku(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => globalSlugify((string) $value, Variation::class, 'sku', '_'),
        );
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getBlockedDatesAttribute()
    {
        return $this->getBlockedDates();
    }

    public function getBlockedDates()
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

    public function fetchBlockedDatesForAVariation()
    {
        return Availability::where('bookable_id', $this->id)->where('bookable_type', 'Modules\Ecommerce\Models\Variation')->whereDate('to', '>=', Carbon::now())->get();
    }

    public function digital_file()
    {
        return $this->morphOne(DigitalFile::class, 'fileable');
    }

    public function availabilities()
    {
        return $this->morphMany(Availability::class, 'bookable');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
