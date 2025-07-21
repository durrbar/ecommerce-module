<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
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
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->json('image')->nullable();
            $table->text('details')->nullable();
            $table->uuid('type_id');
            $table->foreign('type_id')->references('id')->on('types');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_tag', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('tag_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
        Schema::table('products', function (Blueprint $table): void {
            $table->float('min_price')->after('sale_price')->nullable();
            $table->float('max_price')->after('min_price')->nullable();
            $table->json('video')->after('image')->nullable();
        });

        Schema::create('banners', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('type_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->json('image')->nullable();
            $table->timestamps();
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('variation_options');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('cards');
    }
};
