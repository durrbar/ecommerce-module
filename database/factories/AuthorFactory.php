<?php

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthorFactory extends Factory
{
    protected $model = \Modules\Ecommerce\Models\Author::class;
    
    public function definition(): array
    {
        $imageId = fake()->numberBetween(1000, 2000);
        $image = [
            "id" => $imageId,
            "original" => "https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/{$imageId}/Author-img-800-(1).jpg",
            "thumbnail" => "https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/{$imageId}/conversions/Author-img-800-(1)-thumbnail.jpg"
        ];

        return [
            'name' => fake()->name(),
            'slug' => Str::slug(fake()->name()),
            'is_approved' => 1,
            'language' => 'en',
            'bio' => fake()->paragraph(),
            'quote' => fake()->sentence(),
            'born' => fake()->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d\TH:i:s.000\Z'),
            'death' => null,
            'languages' => 'English',
            'socials' => json_encode([]),
            'image' => json_encode($image),
            'cover_image' => json_encode($image),
        ];
    }
}