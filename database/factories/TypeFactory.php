<?php

namespace Modules\Ecommerce\Database\Factories;

use Modules\Ecommerce\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeFactory extends Factory
{
    protected $model = Type::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'settings' => [
                'isHome' => $this->faker->boolean,
                'productCard' => $this->faker->randomElement(['helium', 'argon', 'xenon']),
                'layoutType' => $this->faker->randomElement(['classic', 'standard', 'modern'])
            ],
            'slug' => $this->faker->unique()->slug,
            'language' => 'en',
            'icon' => $this->faker->word,
            'promotional_sliders' => [],
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}