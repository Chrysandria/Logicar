<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Anomalie;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index()
    {
        $car = Car::all();
        return response()->json($car);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string',
        ]);

        if ($validated) {
            $car = Car::create([
                'matricule' => $request->matricule,
            ]);

            return response()->json(["message" => "Enregistrement réussi !!"], 201);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validated) {
            DB::table('cars')
                ->where('matricule', $request->matricule)
                ->update(['disponibilite' => false]);

            Anomalie::create([
                'mtrl_car' => $request->matricule,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Anomalie enregistrée !!'], 200);
        }
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string',
        ]);

        if ($validated) {
            DB::table('cars')
                ->where('matricule', $request->matricule)
                ->delete();
        }
    }
}
