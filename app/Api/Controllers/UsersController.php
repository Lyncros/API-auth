<?php

namespace Api\Controllers;


use App\User;
use Illuminate\Http\Request;

class UsersController extends BaseController
{

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user->count())
        {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $result = $user->update($request->all());

        if ($result) {
            return response()->json(['message' => 'Actualizado con exito']);
        }

        return response()->json(['error' => 'Error interno por favor vuelva a intentar'], '');
    }
}