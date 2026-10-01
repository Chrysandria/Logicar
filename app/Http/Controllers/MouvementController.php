<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mouvement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Car;

class MouvementController extends Controller
{
    public function index()
    {
        $monthly = DB::table('mouvements')
            ->join('cars', 'cars.matricule', '=', 'mouvements.mtrl_car')
            ->select("cars.matricule", DB::raw('SUM(mouvements.distance_parcourue) as total'))
            ->whereMonth('mouvements.date', '=', date('m'))
            ->whereYear('mouvements.date', '=', date('Y'))
            ->groupBy('cars.matricule')
            ->get();

        return response()->json(['data' => $monthly,"month"=>Carbon::now()->locale("fr")->translatedFormat("F"),"year"=>Carbon::now()->year]);
    }

    public function getDailyMoov()
    {
        $cars = Car::all();
        $daily = [];

        foreach($cars as $car) {
            $distance = DB::table('mouvements')
                ->where('mtrl_car', $car->matricule)
                ->whereDate('date', Carbon::now()->toDateString())
                ->select('distance_parcourue')->get();

            $daily[] = [
                'matricule' => $car->matricule,
                'distance_parcourue' => $distance[0]->distance_parcourue ?? 0,
                'km' => $distance[0]->distance_parcourue ?? 0
            ];
        }

        return response()->json($daily);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mtrl_car' => 'required|string',
            'distance_parcourue' => 'required|numeric',
        ]);

        if ($validated) {
            Mouvement::updateOrCreate(
                [
                    'mtrl_car' => $request->mtrl_car,
                    'date' => Carbon::now()->toDateString()
                ],
                [
                    'distance_parcourue' => $request->distance_parcourue
                ]
            );

            return response()->json(['message' => 'Mouvement enregistré !!'], 201);
        }
    }
}
