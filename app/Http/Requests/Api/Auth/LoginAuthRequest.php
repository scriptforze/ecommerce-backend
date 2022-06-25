<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginAuthRequest extends FormRequest
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
            "username" => "required|string",
            "password" => "required|string",
        ];
    }

    public function bodyParameters()
    {
        return [
            'username' => [
                'description' => 'Nombre de usuario ó correo eléctonico del usuario',
            ],
            'password' => [
                'description' => 'Contraseña del usuario',
            ],
        ];
    }
}
