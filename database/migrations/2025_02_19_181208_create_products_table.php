<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products_two', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('sku');
            $table->string('name');
            $table->string('code');
            $table->text('description');
            $table->text('sub_description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('category');
            $table->string('publish');
            $table->integer('available');
            $table->decimal('price_sale', 8, 2)->nullable();
            $table->decimal('taxes', 5, 2)->nullable();
            $table->integer('quantity');
            $table->string('inventory_type');
            $table->boolean('new_label_enabled')->default(false);
            $table->string('new_label_content')->nullable();
            $table->boolean('sale_label_enabled')->default(false);
            $table->string('sale_label_content')->nullable();
            $table->integer('total_sold')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
