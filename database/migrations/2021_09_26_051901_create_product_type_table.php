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
        Schema::table('products', function (Blueprint $table): void {
            $table->foreignUuid('author_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('manufacturer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_digital')->default(0);
            $table->boolean('is_external')->default(0);
            $table->string('external_product_url')->nullable();
            $table->string('external_product_button_text')->nullable();
            $table->string('blocked_dates')->nullable();
        });

        Schema::table('variation_options', function (Blueprint $table): void {
            $table->json('image')->after('title')->nullable();
            $table->boolean('is_digital')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
