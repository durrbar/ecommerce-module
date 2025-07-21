<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;

class DurrbarSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RefundReasonSeeder::class,
            RefundPolicySeeder::class,
            FaqSeeder::class,
            TermsAndConditionSeeder::class,
        ]);
    }
}
