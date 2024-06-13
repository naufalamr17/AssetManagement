<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::factory()->create([
            'name' => 'Administrator',
            'email' => 'administrator@gmail.com',
            'status' => 'Administrator',
            'password' => ('12345678')
        ]);
    }
}
