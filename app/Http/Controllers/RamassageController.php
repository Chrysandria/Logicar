<?php

namespace App\Http\Controllers;

use App\Models\DetailRamassage;
use App\Models\Ramassage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RamassageController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'hps' => 'required|date_format:H:i',
        ]);

        $details = DB::table('detail_ramassages')
            ->join('plannings', 'detail_ramassages.id_planning', '=', 'plannings.id')
            ->join('ramassages', 'detail_ramassages.id_ramassage', '=', 'ramassages.id')
            ->join('trajets', 'trajets.id', '=', 'ramassages.id_trajet')
            ->join('employes', 'employes.matricule', '=', 'plannings.mtrl_emp')
            ->where('plannings.date', $data['date'])
            ->where('plannings.HPS', $data['hps'])
            ->select(
                'detail_ramassages.id_ramassage',
                'ramassages.HDR',
                'trajets.designation as trajet',
                'employes.*'
            )
            ->get();

        $results = $details
            ->groupBy('id_ramassage')
            ->map(fn ($employees) => [
                'ramassage' => [[
                    'HDR' => $employees->first()->HDR,
                    'trajet' => $employees->first()->trajet,
                ]],
                'emps' => $employees->map(function ($employee) {
                    unset($employee->id_ramassage, $employee->HDR, $employee->trajet);

                    return $employee;
                })->values(),
            ])
            ->values();

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hdr' => 'required|date_format:H:i',
            'trip_id' => 'required|integer|exists:trajets,id',
            'planning_ids' => 'required|array|min:1',
            'planning_ids.*' => 'required|integer|distinct|exists:plannings,id',
        ]);

        $ramassage = DB::transaction(function () use ($data) {
            $ramassage = Ramassage::create([
                'HDR' => $data['hdr'],
                'id_trajet' => $data['trip_id'],
            ]);

            foreach ($data['planning_ids'] as $planningId) {
                DetailRamassage::create([
                    'id_ramassage' => $ramassage->id,
                    'id_planning' => $planningId,
                ]);
            }

            return $ramassage;
        });

        return response()->json($ramassage, 201);
    }
}
