<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anomalie;
use Illuminate\Support\Facades\DB;

class AnomalieController extends Controller
{

    public function index()
    {
        $anomalies = DB::table("anomalies")->orderBy("id","desc")->get();
        return response()->json($anomalies);
    }

    public function show(Request $request){
        $validated = $request->validate([
            'matricule' => 'required|string',
        ]);

        if($validated){
            $results = DB::table("anomalies")->where("mtrl_car","=",$request->matricule)->get();
            return response()->json($results);
        }
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string',
            'id_anom' => 'required|integer',
        ]);

        if ($validated) {
            DB::table('cars')
                ->where('matricule', $request->matricule)
                ->update(['disponibilite' => true]);

            DB::table('anomalies')
                ->where('id', $request->id_anom)
                ->update(['fin' => DB::raw('CURRENT_DATE')]);

            return response()->json(["message" => "fin de l'anomalie !!"], 200);
        }
    }
}
