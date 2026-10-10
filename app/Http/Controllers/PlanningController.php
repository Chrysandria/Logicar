<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'service' => 'sometimes|required|string',
        ]);

        $service = $data['service'] ?? ($request->user()->service_sigle === 'SNR'
            ? null
            : $request->user()->service_sigle);
        abort_unless($service === null || $service === $request->user()->service_sigle, 403);

        return response()->json($this->groupedPlannings($data['date'], $service));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'hps' => 'required|date_format:H:i',
            'employees' => 'required|array|min:1',
            'employees.*.matricule' => [
                'required',
                'string',
                'distinct',
                Rule::exists('employes', 'matricule')
                    ->where('service_sigle', $request->user()->service_sigle),
                Rule::unique('plannings', 'mtrl_emp')->where('date', $request->input('date')),
            ],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['employees'] as $employee) {
                Planning::create([
                    'mtrl_emp' => $employee['matricule'],
                    'HPS' => $data['hps'],
                    'date' => $data['date'],
                ]);
            }
        });

        return response()->json(['message' => 'Planning enregistré avec succès'], 201);
    }

    public function update(Request $request, Planning $planning)
    {
        $data = $request->validate([
            'ok' => 'required|boolean',
        ]);

        $planning->update(['ok' => $data['ok']]);

        return response()->json($planning->fresh());
    }

    public function unassigned(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'hps' => 'required|date_format:H:i',
        ]);

        $results = DB::table('plannings')
            ->join('employes', 'employes.matricule', '=', 'plannings.mtrl_emp')
            ->where('plannings.date', $data['date'])
            ->where('plannings.HPS', $data['hps'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detail_ramassages')
                    ->whereColumn('detail_ramassages.id_planning', '=', 'plannings.id');
            })
            ->select('employes.*', 'plannings.id as p_id')
            ->get();

        return response()->json($results);
    }

    private function groupedPlannings(string $date, ?string $service)
    {
        $query = DB::table('plannings')
            ->join('employes', 'employes.matricule', '=', 'plannings.mtrl_emp')
            ->whereDate('plannings.date', $date)
            ->select(
                'plannings.HPS',
                'employes.*',
                'plannings.id',
                'plannings.id as p_id',
                'plannings.ok'
            )
            ->orderBy('plannings.HPS')
            ->orderBy('employes.nom');

        if ($service !== null) {
            $query->where('employes.service_sigle', $service);
        }

        return $query->get()
            ->groupBy('HPS')
            ->map(fn ($employees, $hour) => [
                'HPS' => (object) ['HPS' => $hour],
                'emps' => $employees->map(function ($employee) {
                    unset($employee->HPS);

                    return $employee;
                })->values(),
            ])
            ->values();
    }
}
