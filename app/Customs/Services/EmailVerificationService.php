<?php


namespace App\Customs\Services;
use App\Models\EmailVerificationTokens as EmailVerificationToken ;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Models\User;

class EmailVerificationService
{
    /**
     * Send verification link to a user
    */
    public function sendVerificationLink(object $user): void
    {
        Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user->email)));
    }

    /**
     * Verify user Email
     */
    public function verifyEmail(string $email , string $token)
    {
        $user = User::where('email',$email)->first();
        if(!$user){
            response()->json([
                'status' =>'failed',
                'message' => 'User not found'
            ])->send();
            exit;
        }
        $this->checkIsEmailIsVerified($user);
        $verifiedToken = $this->verifyToken($email, $token);
        if($user->markEmailAsVerified()){
            $verifiedToken->delete();
            return  response()->json([
                'status' => 'success',
                'message' => 'Email has been been verifed successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Email verification failed'
            ]);
        }
    }

    /**
     * Check if user has alreday been verified
     */

     public function checkIsEmailIsVerified($user)
     {
        if($user->email_verified_at){
            response()->json([
                'status' => 'failed',
                'message' => 'Email has already been verified'
            ])->send();
            exit;
        }
     }

    /**
     * Verify Token
     */
    public function verifyToken(string $email, string $token)
    {
        $token = EmailVerificationToken::where('email',$email)->where('token',$token)->first();
        if($token){
            if($token->expired_at >= now())
                return $token;
            else{
                response()->json([
                    'status' => 'failed',
                    'message' => 'Token expired'
                ])->send();
                exit;
            }
        }else{
            response()->json([
                'status' => 'failed',
                'message' => 'Invalid token'
            ])->send();
            exit;
        }
    }

    /**
     * Generate Verification link
    */
    public function generateVerificationLink(string $email): string
    {
        $checkIfTokenExists = EmailVerificationToken::where("email",$email)->first();
        if($checkIfTokenExists)
            $checkIfTokenExists->delete();
        $token = Str::uuid();
        $url = config('app.url'). "?token=" .$token. "&email=".$email;
        $saveToken = EmailVerificationToken::create([
            "email" => $email,
            "token" => $token,
            "expired_at" => now()->addMinutes(120)
        ]);
        if($saveToken)
            return $url;
    }

    /**
     * Resend link with token
     */

     public function resendLink(string $email)
     {
        $user = User::where('email',$email)->first();
        if($user){
            $this->sendVerificationLink($user);
            return response()->json([
                'status' =>'success',
                'message' => 'Verification link send successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ]);
        }
     }

}
