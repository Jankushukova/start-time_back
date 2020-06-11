<?php

namespace App\Http\Controllers\Api\V1\User\Authentication;

use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use JWTAuth;

class SocialAuthController extends Controller
{

    public function facebookAuth(Request $providerUser){
        $user = User::whereProvider('facebook')
            ->whereProviderId($providerUser->id)
            ->first();
        if ($user) {
            return $this->successResponse($user);
        } else {
            $user = User::whereEmail($providerUser->email)->first();
            if (!$user) {
                error_log('here');
                $user = User::create([
                    'provider_id' => $providerUser->id,
                    'provider' => 'facebook',
                    'email' => $providerUser->email,
                    'firstname' => $providerUser->firstName,
                    'lastname' => $providerUser->lastName,
                    'password' => md5(rand(1,10000)),
                    'phone_number' => $providerUser->phoneNumber,
                    'role_id' => Role::AUTHORIZED_CLIENT_ID
                ]);
                $user->email_verified_at = Carbon::now();
            }
            $user->save();
        }

        return $this->successResponse($user);

    }


    public function successResponse($user){
        $token = JWTAuth::fromUser($user);
        $cs = csrf_token();
        return response()->json(compact('token', 'user', 'cs'));

    }
}
