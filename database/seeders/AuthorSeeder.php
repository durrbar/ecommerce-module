<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('authors')->insert([
            [
                'id' => 2,
                'name' => 'James N. Almeida',
                'is_approved' => 1,
                'image' => '{"id":1611,"original":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1611/Author-img-800-(5).jpg","thumbnail":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1611/conversions/Author-img-800-(5)-thumbnail.jpg"}',
                'cover_image' => '{"id":1611,"original":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1611/Author-img-800-(5).jpg","thumbnail":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1611/conversions/Author-img-800-(5)-thumbnail.jpg"}',
                'slug' => 'james-n-almeida',
                'language' => 'en',
                'bio' => 'An author is the creator or originator of any written work similar as a book or play...',
                'quote' => 'All writers are vain, selfish and lazy...',
                'born' => '1973-04-18T18:00:00.000Z',
                'death' => null,
                'languages' => 'English',
                'socials' => '[]',
                'created_at' => '2021-12-07 16:32:20',
                'updated_at' => '2021-12-20 08:56:39',
            ],
            [
                'id' => 3,
                'name' => 'Earnestine N. Pace',
                'is_approved' => 1,
                'image' => '{"id":1609,"original":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1609/Author-img-800-(10).jpg","thumbnail":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1609/conversions/Author-img-800-(10)-thumbnail.jpg"}',
                'cover_image' => '{"id":1609,"original":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1609/Author-img-800-(10).jpg","thumbnail":"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1609/conversions/Author-img-800-(10)-thumbnail.jpg"}',
                'slug' => 'earnestine-n-pace',
                'language' => 'en',
                'bio' => 'An author is the creator or originator of any written work similar as a book or play...',
                'quote' => 'All writers are vain, selfish and lazy...',
                'born' => '1963-05-20T18:00:00.000Z',
                'death' => null,
                'languages' => 'English, Russian',
                'socials' => '[]',
                'created_at' => '2021-12-07 16:33:36',
                'updated_at' => '2021-12-20 08:56:20',
            ],
            // ...add the rest here in the same pattern
        ]);
    }
}