<?php


namespace App\Http\Controllers\Api\V1\User\Authentication;


use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    use VerifiesEmails;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create($request->all());
        $user->password = Hash::make($user->password);
        $user->role_id = Role::AUTHORIZED_CLIENT_ID;
        $user->fullname = $user->firstname . ' ' .  $user->lastname;
        $user->save();
        $user->sendApiEmailVerificationNotification();
        $success['message'] = 'Please confirm yourself by clicking on verify user button sent to you on your email';
        return response()->json(['success'=>$success], 200);
    }


}
