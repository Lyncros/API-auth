<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 1/8/16
 * Time: 10:46 AM
 */

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{

    protected $apiUrl;

    protected $client;

    const PROP_URL = 'propiedades/';

    /**
     * Propiedad constructor.
     * @param Client $client
     * @throws \Exception
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        $env = env('API_BUSQUEDA_URL');

        if (empty($env)) {
            throw new \Exception("No se encuntra iniciada la variable de entorno API_BUSQUEDA_URL");
        }

        $this->apiUrl = $env;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $res = $this->client->request('GET', $this->apiUrl . self::PROP_URL . $id);

        $body = json_encode($res->getBody());

        return collect($body->data);
    }

    /**
     * Return in a single query
     */
    public function listsByFavorite($lists)
    {
        $client = new \GuzzleHttp\Client();

        $data = [
            'json' => [
                    'lists' => $lists->toArray()
            ]
        ];

        $response = $client->request('GET', $this->apiUrl . self::PROP_URL . 'lists', $data);

        return  collect(json_decode($response->getBody()));
    }
}