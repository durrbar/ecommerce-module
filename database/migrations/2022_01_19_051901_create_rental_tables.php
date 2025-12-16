<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Enums\ResourceType;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products_meta', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('availabilities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('from');
            $table->string('to');
            $table->string('booking_duration');
            $table->integer('order_quantity');
            $table->string('bookable_type');
            $table->uuid('bookable_id');
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->text('details')->nullable();
            $table->json('image')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->double('price')->nullable();
            $table->enum('type', ResourceType::getValues());
            $table->timestamps();
        });

        Schema::create('dropoff_location_product', function (Blueprint $table): void {
            $table->uuid('resource_id')->nullable();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('pickup_location_product', function (Blueprint $table): void {
            $table->uuid('resource_id')->nullable();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('feature_product', function (Blueprint $table): void {
            $table->uuid('resource_id')->nullable();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::create('deposit_product', function (Blueprint $table): void {
            $table->uuid('resource_id')->nullable();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('person_product', function (Blueprint $table): void {
            $table->uuid('resource_id')->nullable();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Schema::table('availabilities', function (Blueprint $table) {
        //     $table->uuid('order_id')->nullable()->after('bookable_id');
        //     $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availabilities');
        Schema::dropIfExists('products_meta');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('location_product');
        Schema::dropIfExists('dropoff_location_product');
        Schema::dropIfExists('pickup_location_product');
        Schema::dropIfExists('feature_product');
        Schema::dropIfExists('deposit_product');
        Schema::dropIfExists('person_product');
        Schema::dropIfExists('resources');
    }
};
