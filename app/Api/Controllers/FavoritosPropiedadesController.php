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
use App\Repositories\FavoritoPropiedadRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoritosPropiedadesController extends BaseController
{
    protected $clientID;

    public function __construct(FavoritoPropiedadRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Propiedad $propiedad
     * @return mixed
     */
    public function index()
    {
        $this->clientID = JWTAuth::parseToken()->authenticate()->id_cli;

        $propiedades = $this->repo->getAll($this->clientID);

        return $propiedades;
    }

    public function count(Propiedad $propiedad)
    {
        $this->clientID = JWTAuth::parseToken()->authenticate()->id_cli;
        
        return $this->repo->count($this->clientID);
    }

    /**
     * @param $id propiedad
     * @return mixed
     */
    public function toggle($id)
    {
        $this->clientID = JWTAuth::parseToken()->authenticate()->id_cli;

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