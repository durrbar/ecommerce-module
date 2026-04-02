<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Enums\ProductStatus;
use Modules\Ecommerce\Enums\ProductType;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->json('promotional_sliders')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('is_approved')->default(false);
            $table->json('image')->nullable();
            $table->json('cover_image')->nullable();
            $table->string('slug');
            $table->text('bio')->nullable();
            $table->text('quote')->nullable();
            $table->string('born')->nullable();
            $table->string('death')->nullable();
            $table->string('languages')->nullable();
            $table->json('socials')->nullable();
            $table->timestamps();
        });

        Schema::create('manufacturers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('is_approved')->default(false);
            $table->json('image')->nullable();
            $table->json('cover_image')->nullable();
            $table->string('slug');
            $table->foreignUuid('type_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->json('socials')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->foreignUuid('type_id')->constrained()->cascadeOnDelete();
            $table->double('price')->nullable();
            $table->double('sale_price')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_taxable')->default(false);
            // $table->uuid('shipping_class_id')->nullable();
            $table->enum('status', ProductStatus::cases())->default(ProductStatus::Draft->value);
            $table->enum('product_type', ProductType::cases())->default(ProductType::Simple->value);
            $table->string('unit');
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->json('image')->nullable();
            $table->json('gallery')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->json('image')->nullable();
            $table->text('details')->nullable();
            $table->foreignUuid('parent')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('type_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_product', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('attributes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->foreignUuid('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('attribute_product', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('attribute_value_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('types');
    }
};
