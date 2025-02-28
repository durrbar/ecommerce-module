<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ecommerce\Models\Variant;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed sizes
        $sizes = ['6', '7', '8', '9', '10', '11', '12'];
        foreach ($sizes as $size) {
            Variant::firstOrCreate(['name' => $size, 'type' => 'size']);
        }

        // Seed colors
        $colors = ['#FF0000', '#0048BA', '#000000', '#FFFFFF'];
        foreach ($colors as $color) {
            Variant::firstOrCreate(['name' => $color, 'type' => 'color']);
        }

        // Seed genders
        $genders = ['Men', 'Women', 'Kids'];
        foreach ($genders as $gender) {
            Variant::firstOrCreate(['name' => $gender, 'type' => 'gender']);
        }

        // Seed memories
        $memories = ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'];
        foreach ($memories as $memory) {
            Variant::firstOrCreate(['name' => $memory, 'type' => 'memory']);
        }
    }
}