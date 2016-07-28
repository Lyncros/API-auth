<?php

namespace Api\Controllers;

use App\User;
use Illuminate\Http\Request;
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
    public function getSocialAuth(Request $request, $name)
    {
        if (!config("services.$name")) {
            return request()->json(['message' => 'provider not found', 'code' => '404'], 404);
        }
        
        if ($request->has('redirectUri')) {
          config()->set("services.{$name}.redirect", $request->input('redirectUri'));
        }

        $provider = Socialite::driver($name);
        $provider->stateless();

        // Step 1 + 2
        $profile = $provider->user();

        return $this->getSocialAuthCallback($profile);
    }

    /**
     * @param null $provider
     * @return string
     */
    public function getSocialAuthCallback($user)
    {
        if ($user) {
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
