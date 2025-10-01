<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DataTableSeeder::class,
            AccountsTableSeeder::class,
            MembersTableSeeder::class,
            ClientsTableSeeder::class,
        ]);
    }
}
