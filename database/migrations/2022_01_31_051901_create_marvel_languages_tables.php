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

        if (TRANSLATION_ENABLED) {
            Schema::create('languages', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->json('flag');
                $table->string('language_code');
                $table->string('language_name');
                $table->timestamps();
            });

            Schema::create('translations', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('item_type');
                $table->unsignedBigInteger('item_id'); // this is the translated item id
                $table->unsignedBigInteger('translation_item_id')->nullable(); // this is the main element id
                $table->string('language_code');
                $table->string('source_language_code')->default(DEFAULT_LANGUAGE);
                $table->timestamps();
            });
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->string('language')->after('sale_price')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('tags', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('authors', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('manufacturers', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('resources', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('types', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('attributes', function (Blueprint $table): void {
            $table->string('language')->after('slug')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('attribute_values', function (Blueprint $table): void {
            $table->string('language')->after('value')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('availabilities', function (Blueprint $table): void {
            $table->string('language')->after('to')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('coupons', function (Blueprint $table): void {
            $table->string('language')->after('code')->default(DEFAULT_LANGUAGE);
        });

        // Schema::table('order_status', function (Blueprint $table) {
        //     $table->string('language')->after('serial')->default(DEFAULT_LANGUAGE);
        // });
        Schema::table('variation_options', function (Blueprint $table): void {
            $table->string('language')->after('sale_price')->default(DEFAULT_LANGUAGE);
        });
        Schema::table('settings', function (Blueprint $table): void {
            $table->string('language')->unique()->after('options')->default(DEFAULT_LANGUAGE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
        Schema::dropIfExists('translations');
    }
};
