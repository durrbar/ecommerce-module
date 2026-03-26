<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_options', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('price');
            $table->string('sale_price')->nullable();
            $table->uuid('quantity');
            $table->boolean('is_disable')->default(false);
            $table->string('sku')->nullable();
            $table->json('options');
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->json('image')->nullable();
            $table->text('details')->nullable();
            $table->foreignUuid('type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_tag', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tag_id')->constrained()->cascadeOnDelete();
        });
        
        Schema::table('products', function (Blueprint $table): void {
            $table->float('min_price')->after('sale_price')->nullable();
            $table->float('max_price')->after('min_price')->nullable();
            $table->json('video')->after('image')->nullable();
        });

        Schema::create('banners', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('type_id')->constrained()->cascadeOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->json('image')->nullable();
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
        Schema::dropIfExists('banners');
        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('variation_options');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('cards');
    }
};
