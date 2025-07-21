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
        Schema::table('products', function (Blueprint $table): void {
            $table->uuid('author_id')->nullable();
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            $table->uuid('manufacturer_id')->nullable();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
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
