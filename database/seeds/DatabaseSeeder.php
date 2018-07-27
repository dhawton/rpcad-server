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
        // Seed with testing user data
        if (env("APP_ENV") == "devel") {
            $this->call(UserTableSeed::class);
        }
    }
}
