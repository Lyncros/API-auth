<?php

namespace Api\Controllers;


use App\User;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialController extends BaseController
{

    /**
     * SocialController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param null $provider
     * @return mixed
     */
    public function getSocialAuth($provider = null)
    {
        if (!config("services.$provider")) {
            return request()->json(['message' => 'provider not found', 'code' => '404'], 404);
        }
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param null $provider
     * @return string
     */
    public function getSocialAuthCallback($provider = null)
    {
        if ($user = Socialite::driver($provider)->user()) {

            $loggedUser = User::where('email', $user->email)->where('social', 1)->first();
            
            if (count($loggedUser)) {

                $token = JWTAuth::fromUser($loggedUser);

                return response()->json(compact('token'));

            } else {

                $newUSer = User::create([
                    'email' => $user->email,
                    'nombre' => $user->name,
                    'social' => 1,
                    'confirmed' => 1
                ]);

                $token = JWTAuth::fromUser($newUSer);

                return response()->json(compact('token'));
            }

        } else {
            return response()->json(['message' => 'internal error', 'code' => 500], 500);
        }
    }
}