<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DeleteChildRequest;
use App\Http\Requests\CreateConsultationRequest;
use App\Models\Consultations as consult;

class ConsultationController extends Controller
{
    public function index()
    {
        $consult = consult::select('*')
                ->where("consultations.patientId",auth()->user()->id)
                ->get();
        if ($consult){
            return response()->json([
                'status_code' => 200,
                'data' =>$consult
            ]);
        }
        else{
            return response()->json([
                'status_code' => 404,
                'status_message' => 'Aucune consultation  trouvé',
            ]);
        }
    }
    public function store(CreateConsultationRequest $request) {

        try{
         $consult = new consult([
             'motif'=> $request->motif,
             'duree_mal' =>$request->duree_mal,
             'treatment' => $request->treatment,
             'consulter_medecin_sepe' => $request->consulter_medecin_sepe,
             'current_treatment' => $request->current_treatment,
             'commentaire' => $request->commentaire,
             'image_mal' => $request->image_mal,
             'date_consultation'=>now(),
             'patientId'=>auth()->user()->id,
             'status'=>0,
         ]);
         $consult->save();

          return response()->json([
             'status_code' => 200,
             'status_message' => 'Une consultation a été initiée',
             'data' =>$consult
         ]);
        } catch(Exception $e){
             return response()->json($e);
        }
     }
}
