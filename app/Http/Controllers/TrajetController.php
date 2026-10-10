<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Illuminate\Http\Request;

class TrajetController extends Controller
{
    public function index()
    {
        return response()->json(Trajet::query()->orderBy('designation')->get());
    }

    public function show(Trajet $trip)
    {
        return response()->json($trip);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'designation' => 'required|string|max:255|unique:trajets,designation',
            'trajet' => 'required|array',
        ]);

        Trajet::create($data);

        return response()->json(['message' => "Trajet {$data['designation']} créé avec succès !!"], 201);
    }

    public function update(Request $request, Trajet $trip)
    {
        $data = $request->validate([
            'designation' => 'sometimes|required|string|max:255|unique:trajets,designation,'.$trip->id,
            'trajet' => 'sometimes|required|array',
        ]);

        $trip->update($data);

        return response()->json($trip->fresh());
    }

    public function destroy(Trajet $trip)
    {
        $trip->delete();

        return response()->noContent();
    }
}
