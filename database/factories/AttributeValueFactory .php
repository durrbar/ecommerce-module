<?php

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeValueFactory extends Factory
{
    protected $model = \Modules\Ecommerce\Models\AttributeValue::class;

    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->word(),
            'attribute_id' => $this->faker->numberBetween(1, 10),
            'value' => $this->faker->word(),
            'language' => 'en',
            'meta' => $this->faker->hexColor(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}