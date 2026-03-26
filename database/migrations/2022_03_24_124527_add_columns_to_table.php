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
        Schema::table('reviews', function (Blueprint $table): void {
            $table->foreignUuid('order_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('variation_option_id')->after('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('wishlists', function (Blueprint $table): void {
            $table->foreignUuid('variation_option_id')->after('product_id')->nullable()->constrained()->cascadeOnDelete();
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
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table): void {
                if (Schema::hasColumn('reviews', 'order_id')) {
                    $table->dropForeign(['order_id']);
                    $table->dropColumn('order_id');
                }
                if (Schema::hasColumn('reviews', 'variation_option_id')) {
                    $table->dropForeign(['variation_option_id']);
                    $table->dropColumn('variation_option_id');
                }
            });
        }

        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table): void {
                if (Schema::hasColumn('wishlists', 'variation_option_id')) {
                    $table->dropForeign(['variation_option_id']);
                    $table->dropColumn('variation_option_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table): void {
                if (Schema::hasColumn('orders', 'cancelled_amount')) {
                    $table->dropColumn('cancelled_amount');
                }
            });
        }
    }
};
