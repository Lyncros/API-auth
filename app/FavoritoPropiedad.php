<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 1/8/16
 * Time: 11:54 AM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoritoPropiedad extends Model
{
    protected $table = 'favorito_propiedad';

    public $timestamps = false;

    protected $fillable = ['id_prop', 'id_cli', 'favorite'];
}