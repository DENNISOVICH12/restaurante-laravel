<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_cliente' => 'required|string|max:255',
            'telefono'       => 'nullable|string|max:50',
            'direccion'      => 'nullable|string|max:255',
        ];
    }
}
