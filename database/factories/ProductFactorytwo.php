<?php

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\Variant;
use Modules\User\Models\User;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $boolean = $this->faker->boolean;

        return [
            'id' => Str::uuid(),
            'name' => $this->faker->words(5, true),
            'sku' => strtoupper(Str::random(10)),
            'code' => strtoupper(Str::random(12)),
            'description' => $this->faker->paragraph(),
            'sub_description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Shoes', 'Apparel', 'Accessories']),
            'publish' => $this->faker->randomElement(['published', 'draft']),
            'available' => $this->faker->numberBetween(0, 100),
            'price_sale' => $this->faker->randomFloat(2, 10, 1000),
            'taxes' => $this->faker->numberBetween(0, 20),
            'quantity' => $this->faker->numberBetween(0, 100),
            'inventory_type' => $this->faker->randomElement(['in stock', 'low stock', 'out of stock']),
            'new_label_enabled' => $boolean,
            'new_label_content' => $boolean ? 'NEW' : null,
            'sale_label_enabled' => ! $boolean,
            'sale_label_content' => ! $boolean ? 'SALE' : null,
            'total_sold' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Configure the factory with relationships.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Product $product): void {
            // Attach Variants
            $variantData = [
                'size' => Variant::where('type', 'size')
                    ->inRandomOrder()
                    ->limit(rand(2, 4)) // Select between 2 and 4 sizes
                    ->pluck('name')
                    ->merge(Variant::where('type', 'size')->limit(1)->pluck('name')) // Ensure at least one size
                    ->toArray(),

                'color' => Variant::where('type', 'color')
                    ->inRandomOrder()
                    ->limit(rand(1, 3)) // Select between 1 and 3 colors
                    ->pluck('name')
                    ->merge(Variant::where('type', 'color')->limit(1)->pluck('name')) // Ensure at least one color
                    ->toArray(),

                'gender' => Variant::where('type', 'gender')
                    ->inRandomOrder()
                    ->limit(1) // Always select 1 gender
                    ->pluck('name')
                    ->merge(Variant::where('type', 'gender')->limit(1)->pluck('name')) // Ensure at least one gender
                    ->toArray(),
            ];

            $product->syncVariants($product, $variantData);

            // Attach Images
            $imagePaths = collect(range(1, 24))->random(rand(8, 24))->map(function ($index) {
                return "uploads/product/image/product-{$index}.webp";
            });

            foreach ($imagePaths as $path) {
                $product->images()->create([
                    'path' => $path,
                ]);
            }

            // Attach Tags
            $tagNames = $this->faker->words(rand(2, 5));
            // $tags = collect($tagNames)->map(fn ($name) => Tag::firstOrCreate(['name' => $name]));

            $product->syncTagsWithType($tagNames, 'productTag');

            // Attach Reviews
            $this->attachReviews($product);
        });
    }

    /**
     * Attach reviews to the product.
     *
     * @return void
     */
    protected function attachReviews(Product $product)
    {
        $numberOfReviews = rand(1, 5); // Randomly decide how many reviews to create

        for ($i = 0; $i < $numberOfReviews; $i++) {
            $user = User::inRandomOrder()->first(); // Get a random user

            $review = $product->reviews()->create([
                'user_id' => $user->id,
                'rating' => $this->faker->numberBetween(1, 5), // Random rating between 1.0 and 5.0
                'comment' => $this->faker->paragraph(), // Random comment
                'is_purchased' => $this->faker->boolean, // Randomly set if purchased
                'helpful' => $this->faker->numberBetween(0, 2000), // Random helpful count
            ]);

            // Optionally add attachments to the review
            if ($this->faker->boolean(50)) { // 50% chance to add attachments
                $attachments = collect(range(1, 5))->map(function ($index) {
                    return "uploads/product/image/product-{$index}.webp";
                });

                $review->attachments()->createMany($attachments->map(function ($url) {
                    return ['path' => $url]; // Store each attachment
                })->toArray());
            }
        }
    }
}
