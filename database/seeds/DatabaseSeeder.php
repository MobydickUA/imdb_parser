<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            [
                'type' => 'domain',
                'value' => 'https://www.imdb.com',
            ],
            [
                'type' => 'profile_amount',
                'value' => '500',
            ],
        ]);
    }
}
