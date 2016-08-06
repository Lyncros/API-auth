<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 1/8/16
 * Time: 11:59 AM
 */

namespace Api\Controllers;

use App\FavoritoPropiedad;
use App\Propiedad;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoritosPropiedadesController extends BaseController
{
    protected $clientID;
    
    public function index(Propiedad $propiedad)
    {
        $this->clientID = JWTAuth::parseToken()->authenticate()->id_cli;

        $lists = FavoritoPropiedad::where('id_cli', $this->clientID)->lists('id');

        $propiedades = $propiedad->listsByFavorite($lists);

        return $propiedades;
    }

    /**
     * @param $id propiedad
     * @return mixed
     */
    public function toggle($id)
    {
        $fav = FavoritoPropiedad::where('id_cli', $this->clientID)->where('id_prop', $id)->where('favorite', 1);

        if ($fav->count()) {
            $fav->delete();

            return response()->json(['message' => 'OK']);
        }

         FavoritoPropiedad::create([
            'id_cli' => $this->clientID,
            'id_prop' => $id,
            'favorite' => 1
        ]);

        return response()->json(['message' => 'OK']);


    }
}