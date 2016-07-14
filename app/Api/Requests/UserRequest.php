<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required|max:255',
            'email' => 'required|email|max:255|unique:clientes',
            'clave' => 'required|confirmed|min:4',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'El email ya existe en nuestros registros, si no tiene su contraseña o fue olvidada,
            renovar en el siguiente link.'
        ];
    }
}