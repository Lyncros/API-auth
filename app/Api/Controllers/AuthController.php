<?php

namespace Api\Controllers;

use App\Services\EmailService;
use App\User;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    protected $email;

    /**
     * AuthController constructor.
     * @param EmailService $email
     */
    function __construct(EmailService $email)
    {
        $this->email  = $email;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function me(Request $request)
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }

            $user = User::where('email', $request->email)->where('confirmed', 1);

            if (!$user->count()) {
                return response()->json(['error' => 'user_not_confirmed'], 401);
            }

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    /**
     * @return mixed
     */
    public function validateToken()
    {
        // Our routes file should have already authenticated this token, so we just return success here
        return API::response()->array(['status' => 'success'])->statusCode(200);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function register(UserRequest $request)
    {
        $confirmationCode = str_random(30);

        $newUser = [
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'clave' => bcrypt($request->clave),
            'confirmation_code' => $confirmationCode
        ];

        $data = [
            'confirmation_code' => $confirmationCode,
            'fullname' => $request->nombre . ' ' . $request->apellido
        ];

        $user = User::create($newUser);

        if ($user) {
            $this->email->send($data, 'email.verify', 'Activación de su cuenta', $request);

            return response()->json(['message' => 'User registred, please verify your email']);
        }

        return response()->json(['message' => 'Internal error'], 500);
    }

    /**
     * Confirm user if code is valid
     * @param $code
     */
    public function confirm($code)
    {
        $user = User::where('confirmation_code', $code);

        if ($user->count()) {

            $user->update([
                'confirmed' => 1,
                'confirmation_code' => null,
            ]);

            return response()->json(['message' => 'Activated', 'code' => 200]);
        }

        return response()->json(['message' => 'Activation code not valid', 'code' => 404], 404);
    }

    /**
     * Reset password
     * @param Request $request
     * @internal param $email
     */
    public function reset(Request $request)
    {
        $user = User::where('email', $request->email)->where('social', 0);

        if ($user->count()) {

            $newPassword = str_random(5);

            $user->update(['reseted_password' => 1, 'clave' => bcrypt($newPassword)]);

            $user = $user->first();

            $data = ['password' => $newPassword, 'fullname' => $user->nombre. " " . $user->apellido];

            $this->email->send($data, 'email.reset', 'Envio de clave temporal', $user);

            return response()->json(['message' => 'Clave enviada a su mail']);
        }

        return response()->json(['message' => 'El usuario no existe'], 404);
    }
}