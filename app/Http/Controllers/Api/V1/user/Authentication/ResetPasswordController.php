<?php

namespace App\Http\Controllers\Api\V1\User\Authentication;

use App\Mail\ResetPasswordMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request){
        if(!$this->validateEmail($request->email)){
            return $this->failedResponse();
        }
        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email){
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email){
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if($oldToken)return $oldToken->token;
        $token = str_random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email'=> $email,
            'token'=> $token,
            'created_at'=> Carbon::now(),
        ]);
    }

    public function validateEmail($email){
        return !!User::whereNotNull('email_verified_at')->where('email', $email)->first();
    }


    public function failedResponse(){
        return response()->json([
            'error' => 'Email doesn\'t found on our database'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse(){
        return response()->json([
            'data' => 'Reset Email is send successfully, please check your email inbox.'
        ], Response::HTTP_OK);
    }
}
