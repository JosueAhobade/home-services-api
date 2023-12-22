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

       $headers = 'MIME-Version: 1.0' . "\r\n";
       $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

       $headers .= "From: contact@home-service.com". "\r\n";



       $message='<html>
   <head>

       <style>
           @page {
               margin: 0cm 0cm;
           }


           body {
               margin-left: 0cm;
               margin-right: 0cm;
               margin-bottom: 0cm;
               width: 100%;
               height: 100%;
           }


       </style>



   </head>
<body style="font-family:sans-serif; text-align: center;">
   <div style="width:500px;
               position:absolute;
               background-color: #e9ebec;
               border-radius:5px;">
       <div style="width:480px;
                   margin:10px;
                   position:relative;
                   background-color:white ;">
           <div style="width:460px;align:center;">
               <p style="color:white;
                         font-size:22pt;
                         align:center;
                         margin:15px;">
               <img style="text-decoration: none; display: block; color:#f4f6f9; font-size:30px;" src="https://groupe-BCE.com/logo1.png" alt="Logo"/>
               </p>

           </div>
           <div style="width:460px;
                       height:auto;
                       float:right;
                       margin: 10px 10px 10px 10px;
                       position:absolute;">
                       <div style="max-width: 800px; padding: 12px; margin: 0px;">
   <p><b>Vérification de mail<b/></p>
   <table class="table datatable-responsive-row-control" style="width: 100%;">
           <thead>
           <tr class="text-uppercase">
               <th colspan="2"  style="background-color: #525659; color: #FFF; padding: 8px;text-align:left">DETAILS DU CLIENT</th>

           </tr>
           </thead>
           <tbody>
           <tbody>
             <tr>
             <td style="width: 50%; padding: 5px;text-align:left">Titulaire de compte :</td><td style="width: 50%; font-weight: bold; padding: 5px;text-align:justify">'.$user->name.'</td>
             </tr>


             <tr>
             <td style="padding: 5px;text-align:left">Code :</td>
             <td style="font-weight: bold; padding: 5px;text-align:justify">'.$this->generateVerificationLink($user->email).'</td>
             </tr>
           </tbody>
         </table>


   </div>



   </div>




           <div style="width:460px;
                       height:50px;
                       float:right;
                       bottom:0px;
                       margin:5px;
                       padding-top:5px;
                       background-color:#E75258;
                       position:absolute;">
               <p style="text-align:center;
                         color:#fff; font-weight: 800;">&copy; HOME SERVICES SANTE </p>
           </div>
       </div>
   </div>
</body>

   ';
$sender="contact@home-services.com";
$email=$user->email;
 if (mail($email, $sender, $message,$headers)) {

     echo "<center style=\"background-color:green;color:#fefdff\">Success!!!</center>";
 }
     // echo $message;

        //Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user->email)));
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
    public function generateVerificationCode(string $email): string
    {
        $checkIfTokenExists = EmailVerificationToken::where("email",$email)->first();
        if($checkIfTokenExists)
            $checkIfTokenExists->delete();
        $char = "0123456789";
        $code = str_shuffle($char);
        $code = substr($code, 0,5);
        $token = $code;
        //$url = config('app.url'). "?token=" .$token. "&email=".$email;
        $saveToken = EmailVerificationToken::create([
            "email" => $email,
            "token" => $token,
            "expired_at" => now()->addMinutes(120)
        ]);
        if($saveToken)
            return $token;
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
