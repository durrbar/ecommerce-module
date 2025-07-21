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
        Schema::table('reviews', function (Blueprint $table): void {
            $table->uuid('order_id')->after('id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->uuid('variation_option_id')->after('product_id')->nullable();
            $table->foreign('variation_option_id')->references('id')->on('variation_options')->onDelete('cascade');
        });

        Schema::table('wishlists', function (Blueprint $table): void {
            $table->uuid('variation_option_id')->after('product_id')->nullable();
            $table->foreign('variation_option_id')->references('id')->on('variation_options')->onDelete('cascade');
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('cancelled_amount')->after('total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropColumn('order_id');
            $table->dropColumn('variation_option_id');
        });

        Schema::table('wishlists', function (Blueprint $table): void {
            $table->dropColumn('variation_option_id');
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('cancelled_amount');
        });
    }
};
