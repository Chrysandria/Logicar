<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trajet;

class TrajetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trajets = [
            // [
            //     "designation" => "EST",
            //     "trajet" => json_encode([
            //         ["pnt" => "ANNEXE ELABOLA"],
            //         ["pnt" => "IVATO TANANA"],
            //         ["pnt" => "HORIZON"],
            //         ["pnt" => "MAMAMBA"],
            //     ])
            // ],
            [
                "designation" => "OUEST",
                "trajet" => json_encode([
                    ["pnt" => "ANNEXE ELABOLA"],
                    ["pnt" => "IVATO TANANA"],
                    ["pnt" => "HORIZON"],
                    ["pnt" => "MAMAMBA"],
                ])
            ],
            [
                "designation" => "NORD",
                "trajet" => json_encode([
                    ["pnt" => "ANNEXE ELABOLA"],
                    ["pnt" => "IVATO TANANA"],
                    ["pnt" => "HORIZON"],
                    ["pnt" => "MAMAMBA"],
                ])
            ],
            [
                "designation" => "SUD",
                "trajet" => json_encode([
                    ["pnt" => "ANNEXE ELABOLA"],
                    ["pnt" => "IVATO TANANA"],
                    ["pnt" => "HORIZON"],
                    ["pnt" => "MAMAMBA"],
                ])
            ],
        ];

        foreach ($trajets as $trajet) {
            Trajet::create($trajet);
        }
    }
}
