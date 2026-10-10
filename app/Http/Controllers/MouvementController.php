<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Mouvement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MouvementController extends Controller
{
    public function monthly()
    {
        $monthly = DB::table('mouvements')
            ->join('cars', 'cars.matricule', '=', 'mouvements.mtrl_car')
            ->select('cars.matricule', DB::raw('SUM(mouvements.distance_parcourue) as total'))
            ->whereMonth('mouvements.date', '=', date('m'))
            ->whereYear('mouvements.date', '=', date('Y'))
            ->groupBy('cars.matricule')
            ->get();

        return response()->json(['data' => $monthly, 'month' => Carbon::now()->locale('fr')->translatedFormat('F'), 'year' => Carbon::now()->year]);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'date' => 'sometimes|required|date',
        ]);

        $date = $data['date'] ?? Carbon::today()->toDateString();
        $distances = DB::table('mouvements')
            ->whereDate('date', $date)
            ->orderBy('id')
            ->get(['mtrl_car', 'distance_parcourue'])
            ->groupBy('mtrl_car')
            ->map(fn ($movements) => $movements->first()->distance_parcourue);

        $daily = Car::query()
            ->orderBy('matricule')
            ->get()
            ->map(function (Car $car) use ($distances) {
                $distance = $distances->get($car->matricule, 0);

                return [
                    'matricule' => $car->matricule,
                    'distance_parcourue' => $distance,
                    'km' => $distance,
                ];
            });

        return response()->json($daily);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mtrl_car' => 'required|string|exists:cars,matricule',
            'distance_parcourue' => 'required|numeric|gt:0',
        ]);

        Mouvement::updateOrCreate(
            [
                'mtrl_car' => $data['mtrl_car'],
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'distance_parcourue' => $data['distance_parcourue'],
            ]
        );

        return response()->json(['message' => 'Mouvement enregistré !!'], 201);
    }
}
