<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ramassage;
use App\Models\Detail_ramassage;

class RamassageController extends Controller
{
    public function createRamassage(Request $request)
    {
        $data = $request->validate([
            'HDR' => 'required|date_format:H:i',
            'id_trajet' => 'required|integer',
            "plannings" => 'required|array',
        ]);

        $ramassage = ramassage::create($data);

        foreach($request->plannings as $planning){
            Detail_ramassage::create([
                'id_ramassage' => $ramassage->id,
                'id_planning' => $planning['p_id'],
            ]);
        }

        return response()->json(['message' => 'Ramassage created successfully', 'ramassage' => $ramassage], 201);
    }
}
