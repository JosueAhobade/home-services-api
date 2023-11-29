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
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;


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
        * Reset password
        */
        public function resetPassword(ResetPasswordRequest $request)
        {

                    $user = User::where('email', auth()->user()->email)->first();
                        if ($user) {
                            if (Hash::check($request->current_password, $user->password)) {
                                if ($request->password != $request->current_password) {
                                    if ($request->password == $request->password_confirmation) {
                                        $hashedPassword = bcrypt($request->password);
                                        $user->password = $hashedPassword;
                                        $user->save();

                                        return response()->json([
                                            'status' => 'success',
                                            'message' => 'Password updated successfully'
                                        ], 200);
                                    } else {
                                        return response()->json([
                                            'status' => 'failed',
                                            'message' => 'Password and confirmation password do not match'
                                        ], 403);

                                    }
                                } else {
                                    return response()->json([
                                        'status' => 'failed',
                                        'message' => 'Your password must be different from the current password'
                                    ], 403);

                                }
                            } else {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => 'Invalid current password'
                                ], 401);

                            }
                        } else {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'User not found'
                            ], 404);

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
