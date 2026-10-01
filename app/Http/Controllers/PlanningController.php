<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Planning;
use App\Models\Ramassage;

class PlanningController extends Controller
{

    public function index(Request $request)
    {
        $validate = $request->validate([
            "date" => "required|date",
            "service" => "required|string"
        ]);

        if ($validate) {
            try {
                $HPS = DB::table("plannings")
                    ->join("employes", "employes.matricule", "=", "plannings.mtrl_emp")
                    ->select("plannings.HPS")
                    ->where("plannings.date", "=", $request->date)
                    ->where("employes.service_sigle", "=", $request->service)
                    ->distinct()
                    ->get();

                $plannings = [];

                foreach ($HPS as $H) {
                    $detail = DB::table("plannings")
                        ->join("employes", "employes.matricule", "=", "plannings.mtrl_emp")
                        ->select("employes.*", "plannings.id", "plannings.ok")
                        ->where("plannings.HPS", "=", $H->HPS)
                        ->where("plannings.date", "=", $request->date)
                        ->where("employes.service_sigle", "=", $request->service)
                        ->get();

                    $plannings[] = ["HPS" => $H, "emps" => $detail];
                }

                return response()->json($plannings);
            } catch (Exception $e) {
                return response()->json($e);
            }
        }
    }

    public function allPlanning(Request $request)
    {
        $validate = $request->validate([
            "date" => "required|date",
        ]);

        if ($validate) {
            try {
                $HPS = DB::table("plannings")
                    ->select("plannings.HPS")
                    ->where("plannings.date", "=", $request->date)
                    ->distinct()
                    ->get();

                $plannings = [];

                foreach ($HPS as $H) {
                    $detail = DB::table("plannings")
                        ->join("employes", "employes.matricule", "=", "plannings.mtrl_emp")
                        ->select("employes.*", "plannings.id as p_id", "plannings.ok")
                        ->where("plannings.HPS", "=", $H->HPS)
                        ->where("plannings.date", "=", $request->date)
                        ->get();

                    $plannings[] = ["HPS" => $H, "emps" => $detail];
                }

                return response()->json($plannings);
            } catch (Exception $e) {
                return response()->json($e);
            }
        }
    }
    public function Next_Week()
    {
        DB::statement("SET lc_time_names = 'fr_FR'");
        $dates = DB::select("
        SELECT d AS date, DAYNAME(d) AS jour FROM ( SELECT DATE_ADD( DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL n+7 DAY ) d FROM ( SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) t )x ORDER BY d
        ");

        return response()->json($dates);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            "date" => "required|date",
            "hps" => "required|date_format:H:i",
            "list_emps" => "required|array"
        ]);

        if ($validate) {
            try {
                foreach ($request->list_emps as $emp) {
                    Planning::create([
                        "mtrl_emp" => $emp["matricule"],
                        "HPS" => $request->hps,
                        "date" => $request->date
                    ]);
                }

                return response()->json(["message" => "Planning enregistrer avec succés"], 200);
            } catch (Exception $e) {
                return response()->json(["error" => $e->getMessage(), "message" => "Une erreur s' est produite."],401);
            }
        }
    }

    public function update(Request $request)
    {
        $validate = $request->validate([
            "id" => "required|integer",
        ]);

        if ($validate) {
            try {
                DB::table("plannings")->where("id", $request->id)->update([
                    "ok" => 0
                ]);
                return response()->json(["message" => "Mise à jour effectuée avec succès"], 200);
            } catch (Exception $e) {
                return response()->json(["error" => $e, "message" => "Une erreur s' est produite."]);
            }
        }
    }

    public function pwr(Request $request){
        $validate = $request->validate([
            "date" => "required|date",
            "hps" => "required|date_format:H:i"
        ]);

        if($validate){
            $results = DB::table("plannings")
            ->join("employes","employes.matricule","=","plannings.mtrl_emp")
            ->where("plannings.date",$request->date)
            ->where("plannings.HPS",$request->hps)
            ->whereNotExists(function($query){
                $query->select(DB::raw(1))
                      ->from('detail_ramassages')
                      ->whereColumn("detail_ramassages.id_planning","=","plannings.id");
            })
            ->select("employes.*","plannings.id as p_id")
            ->get();

            return response()->json($results);
        }
    }

    public function pr(Request $request){
        $validate = $request->validate([
            "date" => "required|date",
            "hps" => "required|date_format:H:i"
        ]);

        if($validate){
            $ids = DB::table("detail_ramassages")
            ->join("plannings","detail_ramassages.id_planning","=","plannings.id")
            ->where("plannings.date",$request->date)
            ->where("plannings.HPS",$request->hps)
            ->select("detail_ramassages.id_ramassage")
            ->distinct()
            ->get();

            $results = [];

            foreach($ids as $id){
                $aboutRam = DB::table("ramassages")
                ->join("trajets","trajets.id","=","ramassages.id_trajet")
                ->where("ramassages.id","=",$id->id_ramassage)
                ->select("ramassages.HDR","trajets.designation as trajet")
                ->get();

                $employes = DB::table("detail_ramassages")
                ->join("plannings","detail_ramassages.id_planning","=","plannings.id")
                ->join("employes","employes.matricule","=","plannings.mtrl_emp")
                ->where("detail_ramassages.id_ramassage","=",$id->id_ramassage)
                ->select("employes.*")
                ->get();

                $results[] = [
                    "ramassage" => $aboutRam,
                    "emps" => $employes
                ];
            }

            return response()->json($results);
        }
    }
}
