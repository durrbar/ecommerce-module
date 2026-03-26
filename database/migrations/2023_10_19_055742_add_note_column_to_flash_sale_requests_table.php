<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flash_sale_requests', function (Blueprint $table): void {
            $table->dropColumn('requested_product_ids');
            $table->string('note')->after('request_status')->nullable();
            $table->foreignUuid('flash_sale_id')->after('title')->constrained()->cascadeOnDelete();
            $table->string('language')->after('note')->default(DEFAULT_LANGUAGE);
        });

        Schema::create('flash_sale_requests_products', function (Blueprint $table): void {
            $table->foreignUuid('flash_sale_requests_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_requests_products');

        Schema::dropIfExists('flash_sale_requests');
    }
};
