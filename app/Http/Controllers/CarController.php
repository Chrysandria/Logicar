<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $car = Car::all();

        return response()->json($car);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'matricule' => 'required|string|max:255|unique:cars,matricule',
        ]);

        $car = Car::create(['matricule' => $data['matricule']]);

        return response()->json([
            'message' => 'Enregistrement réussi !!',
            'car' => $car,
        ], 201);
    }

    public function destroy(string $car)
    {
        Car::where('matricule', $car)->firstOrFail()->delete();

        return response()->noContent();
    }
}
