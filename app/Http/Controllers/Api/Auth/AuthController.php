<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patients as Patient;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ResendEmailVerificationRequest;
use App\Customs\Services\EmailVerificationService;

class AuthController extends Controller
{

    public function  __construct(private EmailVerificationService $service){}
    /**
     *Login method
     */
    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        $user = User::where('email',$request->email)->first();
        if($token){
            if($user->email_verified_at){
                return $this->responseWithToken($token, auth()->user());
            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Your email is not verified',
                ],401);
            }

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
            $this->service->sendVerificationLink($user);
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
      * Verify user email
      */

      public function verifyUserEmail(VerifyEmailRequest $request)
      {
        return $this->service->verifyEmail($request->email, $request->token);
      }
      /**
       * Resend email verification
       */

       public function resendEmailVerificationLink(ResendEmailVerificationRequest $request)
       {
            return $this->service->resendLink($request->email);
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
