<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'service' => 'sometimes|required|string',
        ]);

        $service = $data['service'] ?? $request->user()->service_sigle;
        abort_unless($service === $request->user()->service_sigle, 403);

        return response()->json(
            Employe::where('service_sigle', $service)
                ->orderBy('nom')
                ->get()
        );
    }

    public function services()
    {
        $services = DB::table('employes')
            ->select('employes.service_sigle')
            ->distinct()
            ->orderBy('employes.service_sigle')
            ->get();

        return response()->json($services);
    }
}
