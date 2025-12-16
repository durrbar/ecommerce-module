<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Enums\ProductStatus;
use Modules\Ecommerce\Enums\ProductType;

return new class () extends Migration {
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
            // $table->uuid('type_id');
            $table->foreignUuid('type_id')->references('id')->on('types')->onDelete('cascade');
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
            // $table->uuid('type_id');
            $table->foreignUuid('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->double('price')->nullable();
            $table->double('sale_price')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_taxable')->default(false);
            // $table->uuid('shipping_class_id')->nullable();
            $table->enum('status', ProductStatus::getValues())->default(ProductStatus::DRAFT);
            $table->enum('product_type', ProductType::getValues())->default(ProductType::SIMPLE);
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
            // $table->uuid('parent')->nullable();
            $table->foreignUuid('parent')->references('id')->on('categories')->onDelete('cascade')->nullable();
            // $table->uuid('type_id');
            $table->foreignUuid('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_product', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            // $table->uuid('product_id');
            // $table->uuid('category_id');
            $table->foreignUuid('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignUuid('category_id')->references('id')->on('categories')->onDelete('cascade');
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
            // $table->uuid('attribute_id');
            $table->foreignUuid('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('attribute_product', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            // $table->uuid('attribute_value_id');
            $table->foreignUuid('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
            // $table->uuid('product_id');
            $table->foreignUuid('product_id')->references('id')->on('products')->onDelete('cascade');
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
