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

        $token = JWTAuth::attempt($credentials);

        $status = [];

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token) {
                return response()->json(['error' => 'Credenciales invalidas, vuelva a intentar.'], 401);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user->confirmed) {
                $status = 'TEMPORAL_PASSWORD';
            } else {
              $status = 'OWNER_PASSWORD';
            }

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token', 'status'));
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
        $password = str_random(6);

        $userExists = User::where('email', $request->email)->where('clave', '!=', '')->get();

        if ($userExists->count()) {
            return response()->json(['error' => 'El usuario ya existe'], 422);
        }

        $newUser = [
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'clave' => bcrypt($password)
        ];

        $data = [
            'password' => $password,
            'fullname' => $request->nombre . ' ' . $request->apellido
        ];

        $user = User::updateOrCreate($newUser);

        if ($user) {
            $this->email->send($data, 'email.verify', 'Activación de su cuenta', $request);

            return response()->json(['message' => 'Cliente registrado, por favor verifique su mail.']);
        }

        return response()->json(['message' => 'Internal error'], 500);
    }

    /**
     * Confirm user if code is valid
     * @param $code
     */
    public function changePassword($id, Request $request)
    {
        $user = User::find($id);

        if ($user->count()) {
            $user->update([
                'clave' => bcrypt($request->password),
                'confirmed' => 1
            ]);

            return response()->json(['message' => 'Clave actualizada', 'code' => 200]);
        }

        return response()->json(['message' => 'User not found', 'code' => 404], 404);
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
