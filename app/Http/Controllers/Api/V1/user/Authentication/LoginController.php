<?php


namespace App\Http\Controllers\Api\V1\User\Authentication;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function authenticateUser(Request $request){
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials) ) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
            $user = Auth::user();
            if ($user->isAdmin())
                return response()->json(['error' => 'invalid_credentials'], 400);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $cs = csrf_token();
        if($user->email_verified_at !== NULL){
            return response()->json(compact('token', 'user', 'cs'));
        }else {
            return response()->json(['error' => 'Please Verify Email'], 400);
        }
    }

    public function authenticateAdmin(Request $request){
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials) ) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
            $user = Auth::user();
            if (!$user->isAdmin())
                return response()->json(['error' => 'invalid_credentials'], 400);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token', 'user'));
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
            echo $user;

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

}
