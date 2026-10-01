<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            ["matricule" => "5149 TBK"],
            ["matricule" => "1344 TBV"],
            ["matricule" => "5165 TBM"],
            ["matricule" => "9735 TBL"],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}
