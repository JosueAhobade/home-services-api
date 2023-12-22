<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MotifConsultation as motif;


class MotifConsultation extends Controller
{
    public function index()
    {
        $motif = motif::select('*')
                ->get();
        if ($motif){
            return response()->json([
                'status_code' => 200,
                'data' =>$motif
            ]);
        }
        else{
            return response()->json([
                'status_code' => 404,
                'status_message' => 'Pas de motifs',
            ]);
        }
    }
}
