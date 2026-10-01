<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       $users = [
            ["name" => "Chryslin","service_sigle" => "SCU", "password" => bcrypt("123456")],
            ["name" => "Andry","service_sigle" => "SNR", "password" => bcrypt("qwerty")],
        ];

        foreach($users as $user){
            User::create($user);
        }

        $this->call([
            TrajetSeeder::class,
            CarSeeder::class,
        ]);
    }
}
