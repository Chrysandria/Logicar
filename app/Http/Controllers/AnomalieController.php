<?php

namespace App\Http\Controllers;

use App\Models\Anomalie;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnomalieController extends Controller
{
    public function index(Request $request)
    {
        $query = Anomalie::query()->orderByDesc('id');

        if ($request->filled('matricule')) {
            $query->where('mtrl_car', $request->query('matricule'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request, string $matricule)
    {
        $data = $request->validate([
            'description' => 'required|in:technique,mecanique',
        ]);

        $car = Car::where('matricule', $matricule)->firstOrFail();

        if (Anomalie::where('mtrl_car', $car->matricule)->whereNull('fin')->exists()) {
            return response()->json(['message' => 'Une anomalie est déjà en cours pour ce car.'], 409);
        }

        $anomalie = DB::transaction(function () use ($car, $data) {
            $car->update(['disponibilite' => false]);

            return Anomalie::create([
                'mtrl_car' => $car->matricule,
                'description' => $data['description'],
                'debut' => today()->toDateString(),
            ]);
        });

        return response()->json($anomalie, 201);
    }

    public function update(Anomalie $anomalie)
    {
        abort_if($anomalie->fin !== null, 409, 'Cette anomalie est déjà clôturée.');

        DB::transaction(function () use ($anomalie) {
            $anomalie->update(['fin' => today()->toDateString()]);

            $hasOpenAnomaly = Anomalie::where('mtrl_car', $anomalie->mtrl_car)
                ->whereNull('fin')
                ->exists();

            if (! $hasOpenAnomaly) {
                Car::where('matricule', $anomalie->mtrl_car)->update(['disponibilite' => true]);
            }
        });

        return response()->json($anomalie->fresh());
    }
}
