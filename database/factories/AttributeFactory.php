<?php

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\Attribute;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'slug' => $this->faker->unique()->slug(1), // short slug like 'color', 'size'
            'language' => 'en', // hardcoded to 'en', change if needed
            'name' => $this->faker->word(), // or you can use slug() or faker for specific names
            'shop_id' => $this->faker->numberBetween(1, 15), // random shop_id, adjust range as needed
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}