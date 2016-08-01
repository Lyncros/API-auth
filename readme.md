# API AUTH CLIENTES

## Configuracion global

Replicar el archivo .env.example a .env con los accesos necesarios
muy importante la direccion de las apis.


### Cambios en base de datos
*Aplicar los siguientes cambios en la tabla clientes*

http://pastebin.com/qa4RU8Kd

*Ejecutar migraciones*

`php artisan migrate`

## Favoritos

`GET /api/propiedades/favoritos`
`POST api/propiedades/{id}/favoritos`

###Google Auth

`api/login/google`

###Facebook Auth

`api/login/facebook`


###Login & Register

`POST api/login`

`POST api/register`


