<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employe;
use Exception;
use Illuminate\Support\Facades\DB;

class EmployeController extends Controller
{
    public function index(Request $request){
        try {
            $validate = $request->validate([
            "service" => "required|string"
        ]);

        if($validate){
            $emps = Employe::where("service_sigle","=",$request->service,null)->get();
            return response()->json($emps, 200);
        }
        } catch(Exception $e){
            return response()->json([
                "error" => $e->getMessage()
            ], 501);
        }
    }

    public function getServices(){
        $services = DB::table("employes")
        ->select("employes.service_sigle")
        ->distinct()->get();

        return response()->json($services);
    }

}
