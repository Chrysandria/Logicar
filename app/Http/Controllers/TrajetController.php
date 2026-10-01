<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trajet = Trajet::all();

        return response()->json($trajet);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            "designation" => "required|string",
            "trajet" => "required|array"
        ]);

        Trajet::create($validate);
        return response()->json(["message" => `Trajet {$request->designation} créer avec succés !!`]);
    }

    /**
     * Display the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            "id_trajet" => "required|integer",
            "trajet" => "required|array"
        ]);

        if($validate){
            $trajet = Trajet::find("id",$request->id_trajet);
            $trajet->update([
                "trajet" => $request->trajet
            ]);

            return response()->json(["message" => "Mise à jour éffectué !!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
         $validate = $request->validate([
            "id_trajet" => "required|integer",
        ]);

        if($validate){
            DB::table("trajets")->delete($request->id_trajet);
        }
    }
}
