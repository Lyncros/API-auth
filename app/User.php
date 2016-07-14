<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'clientes';

    public $timestamps = false;

    protected $primaryKey = 'id_cli';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre', 'apellido', 'email', 'clave',
        'confirmation_code', 'confirmed', 'social',
        'reseted_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'clave', 'remember_token',
    ];

    /**
     * Change default password column
     *
     * @return mixed
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }
}
