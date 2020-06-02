<?php

namespace App\Http\Controllers\api\v1\user\authentication;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ChangePasswordController extends Controller
{
    public function process(Request $request){

        return count($this->getPasswordResetTableRow($request)->get()) == 0 ? $this->tokenNotFoundResponse() : $this->changePassword($request);
    }


    private function getPasswordResetTableRow($request){
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->resetToken
        ]);
    }

    private function tokenNotFoundResponse(){
        return response()->json(['error' => 'Token or email is incorrect'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword(Request $request){
        $user = User::whereEmail($request->email)->first();
        $user->update(['password'=> bcrypt($request->password)]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json(['data' => 'Password successfully chaged'], Response::HTTP_CREATED);
    }
}
