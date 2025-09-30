<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (array_key_exists('id_cliente', $input) && !array_key_exists('cliente_id', $input)) {
            $input['cliente_id'] = $input['id_cliente'];
        }

        if (!array_key_exists('restaurant_id', $input) && app()->bound('current_restaurant_id')) {
            $restaurantId = app('current_restaurant_id');
            if ($restaurantId) {
                $input['restaurant_id'] = $restaurantId;
            }
        }

        $this->merge($input);
    }

    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|integer|exists:clientes,id',
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'estado'     => 'nullable|in:pendiente,en_entrega,listo,entregado,cancelado',
            'items'      => 'required|array|min:1',
            'items.*.nombre_producto'=> 'required|string|max:100',
            'items.*.precio'         => 'required|numeric|min:0',
            'items.*.categoria'      => 'required|string|max:50',
            'items.*.cantidad'       => 'required|integer|min:1',
            'items.*.descripcion'    => 'nullable|string',
        ];
    }
}