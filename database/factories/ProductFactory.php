<?php

namespace Modules\Ecommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\AttributeValue;
use Modules\Ecommerce\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;


    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        $price = $this->faker->randomFloat(2, 0.5, 300);
        $salePrice = $this->faker->boolean(20) ? round($price * $this->faker->randomFloat(2, 0.6, 0.95), 2) : null;
        $minPrice = $salePrice ?? $price;
        $maxPrice = max($price, $minPrice);

        // small helper to create media object similar to SQL examples
        $makeMedia = fn($id = null) => [
            'id' => $id ? (string)$id : (string)$this->faker->numberBetween(500, 900),
            'original' => $this->faker->imageUrl(800, 800, 'food'),
            'thumbnail' => $this->faker->imageUrl(200, 200, 'food'),
        ];

        // gallery array (will be cast to json by the model)
        $gallery = [];
        $galleryCount = $this->faker->numberBetween(0, 4);
        for ($i = 0; $i < $galleryCount; $i++) {
            $gallery[] = $makeMedia();
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->numberBetween(1, 9999)),
            'description' => $this->faker->optional()->paragraphs(2, true),
            'type_id' => $this->faker->numberBetween(1, 5),
            'price' => $price,
            'shop_id' => $this->faker->numberBetween(1, 6),
            'sale_price' => $salePrice,
            'language' => 'en',
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sku' => (string)$this->faker->unique()->numberBetween(1, 99999),
            'quantity' => $this->faker->numberBetween(0, 1000),
            'sold_quantity' => 0,
            'in_stock' => $this->faker->boolean(90) ? 1 : 0,
            'is_taxable' => $this->faker->boolean(30) ? 1 : 0,
            'in_flash_sale' => $this->faker->boolean(10) ? 1 : 0,
            'shipping_class_id' => $this->faker->optional()->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['publish', 'draft']),
            'product_type' => $this->faker->randomElement(['simple', 'variable']),
            'unit' => $this->faker->randomElement(['1lb', '2lb', 'kg', '1pc(s)', null]),
            'height' => $this->faker->optional()->randomFloat(2, 0.1, 50),
            'width' => $this->faker->optional()->randomFloat(2, 0.1, 50),
            'length' => $this->faker->optional()->randomFloat(2, 0.1, 50),
            'image' => $makeMedia(),
            'video' => null,
            'gallery' => $gallery,
            // do not set deleted_at here; let soft deletes be handled by model if needed
            'author_id' => null,
            'manufacturer_id' => null,
            'is_digital' => $this->faker->boolean(5) ? 1 : 0,
            'is_external' => $this->faker->boolean(5) ? 1 : 0,
            'external_product_url' => $this->faker->optional()->url(),
            'external_product_button_text' => $this->faker->optional()->word(),
            'blocked_dates' => null,
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // attach random attribute values if table exists
            if (class_exists(AttributeValue::class)) {
                $attributeValueIds = AttributeValue::inRandomOrder()->take(rand(0, 4))->pluck('id')->toArray();
                if (!empty($attributeValueIds)) {
                    $product->variations()->attach($attributeValueIds);
                }
            }
        });
    }
}
