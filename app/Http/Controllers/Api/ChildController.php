<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateChildRequest;
use App\Http\Requests\EditChildRequest;
use App\Http\Requests\DeleteChildRequest;
use App\Models\Enfants as child;

class ChildController extends Controller
{

    public function index()
    {
        $child = child::select('*')
                ->where("enfants.parentId",auth()->user()->id)
                ->get();
        if ($child){
            return response()->json([
                'status_code' => 200,
                'data' =>$child
            ]);
        }
        else{
            return response()->json([
                'status_code' => 404,
                'status_message' => 'Aucun enfant trouvé',
            ]);
        }

    }
    public function addChild(CreateChildRequest $request)
    {
        try{
            $child = new child([
                'nom'=> $request->nom,
                'prenom'=>$request->prenom,
                'sexe'=>$request->sexe,
                'age'=>$request->age,
                'parentId'=>auth()->user()->id,
            ]);
            $child->save();

             return response()->json([
                'status_code' => 200,
                'status_message' => 'Votre enfant  a été ajouté',
                'data' =>$child
            ]);
           } catch(Exception $e){
                return response()->json($e);
           }
    }

    public function editChild(EditChildRequest $request, child $child)
    {
        try{
            $child->nom = $request->nom;
            $child->prenom = $request->prenom;
            $child->sexe = $request->sexe;
            $child->age = $request->age;

            $child->save();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Les informations de votre enfant ont bien été modifiées',
                'data' =>$child
            ]);
        } catch(Exception $e){
            return response()->json($e);
        }
    }

    public function deleteChild(DeleteChildRequest $request, child $child)
    {
        try{
            $child->delete();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Les informations de votre enfant ont bien été supprimées',
            ]);
        } catch(Exception $e){
            return response()->json($e);
        }
    }
}
