<?php

namespace App\Repositories;

use App\FavoritoPropiedad;
use App\Propiedad;

class FavoritoPropiedadRepository
{
    public function __construct(FavoritoPropiedad $model, Propiedad $propiedad)
    {
        $this->model = $model;
        $this->propiedad = $propiedad;
    }

    /**
     * Return all properties favorited by client ID
     *
     * @param $clientID
     * @return mixed
     */
    public function getAll($clientID)
    {

        $lists = $this->model->where('id_cli', $clientID)->lists('id_prop');

        $propiedades = $this->propiedad->listsByFavorite($lists);

        return $propiedades;
    }

    /**
     * Return count of favorites by client id
     *
     * @param Propiedad $propiedad
     * @param $clientID
     * @return mixed
     */
    public function count($clientID)
    {
        return count($this->getAll($clientID));
    }
}