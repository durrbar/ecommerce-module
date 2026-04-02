<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Ecommerce\Models\AttributeValue;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

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
