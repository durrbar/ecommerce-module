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
        Schema::create('variants', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., 'Men', '#1890FF', '6'
            $table->string('type'); // 'gender', 'color', 'size'
            $table->timestamps();

            // Indexes for better query performance
            $table->index('name');
            $table->index('type');
        });

        Schema::create('variantables', function (Blueprint $table): void {
            $table->foreignUuid('variant_id')->constrained()->cascadeOnDelete();

            $table->uuidMorphs('variantable'); // Polymorphic fields

            $table->unique(['variant_id', 'variantable_id', 'variantable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
        Schema::dropIfExists('variantables');
    }
};
