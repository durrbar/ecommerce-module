<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Enums\ResourceType;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products_meta', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
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
            // $table->foreignUuid('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
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
            $table->enum('type', ResourceType::cases());
            $table->timestamps();
        });

        Schema::create('dropoff_location_product', function (Blueprint $table): void {
            $table->foreignUuid('resource_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('pickup_location_product', function (Blueprint $table): void {
            $table->foreignUuid('resource_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('feature_product', function (Blueprint $table): void {
            $table->foreignUuid('resource_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });
        Schema::create('deposit_product', function (Blueprint $table): void {
            $table->foreignUuid('resource_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('person_product', function (Blueprint $table): void {
            $table->foreignUuid('resource_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Schema::table('availabilities', function (Blueprint $table) {
        //     $table->foreignUuid('order_id')->nullable()->after('bookable_id')->constrained()->cascadeOnDelete();
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
