<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patients as Patient;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     *Login method
     */ 
    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if($token){
            return $this->responseWithToken($token, auth()->user());
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials',
            ],401);
        }
    }

    /**
     *Registration method
    */

     public function register(RegistrationRequest $request)
     {
        $user = User::create($request->validated());
        if($user){
            $token = auth()->login($user);
            $patient = new Patient([
                    "nom" => $request->nom,
                    "prenom" => $request->prenom,
                    "sexe" => $request->sexe,
                    "age" => $request->age,
                    "userId" => $user->id,
                ]);
                $patient->save();

                $result = User::join('patients','patients.userId','=','users.id')
                        ->where('users.id',$user->id)
                        ->select('users.*','patients.*')
                        ->get();
            return $this->responseWithToken($token, $result);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'An arror occure while trying to create user',
            ],500);
        }
     }

    /**
     * Return JWT access  token
    */ 

    public function responseWithToken($token , $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer',
        ]);
    }
}
