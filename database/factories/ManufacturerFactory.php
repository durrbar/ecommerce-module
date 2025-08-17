<?php

namespace Modules\Ecommerce\Database\Factories;

use Modules\Ecommerce\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company,
            'is_approved' => true,
            'image' => [
                'thumbnail' => $this->faker->imageUrl(100, 100),
                'original' => $this->faker->imageUrl(),
            ],
            'cover_image' => [
                'thumbnail' => $this->faker->imageUrl(200, 200),
                'original' => $this->faker->imageUrl(),
            ],
            'slug' => $this->faker->unique()->slug,
            'language' => 'en',
            'type_id' => \Modules\Ecommerce\Models\Type::factory(),
            'description' => $this->faker->paragraph,
            'website' => $this->faker->url,
            'socials' => [
                ['icon' => 'FacebookIcon', 'url' => $this->faker->url],
                ['icon' => 'TwitterIcon', 'url' => $this->faker->url]
            ],
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}