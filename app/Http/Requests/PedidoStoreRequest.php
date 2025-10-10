<?php

namespace App\Http\Requests;

use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (array_key_exists('id_cliente', $input) && !array_key_exists('cliente_id', $input)) {
            $input['cliente_id'] = $input['id_cliente'];
        }

        if (!array_key_exists('restaurant_id', $input)) {
            $restaurantId = $this->attributes->get('restaurant_id');

            if (!$restaurantId && app()->bound('current_restaurant_id')) {
                $restaurantId = app('current_restaurant_id');
            }

            if (!$restaurantId) {
                $candidateIds = Restaurant::query()->limit(2)->pluck('id');
                if ($candidateIds->count() === 1) {
                    $restaurantId = $candidateIds->first();
                }
            }

            if ($restaurantId) {
                $input['restaurant_id'] = (int) $restaurantId;
            }
        }

        $this->merge($input);
    }

    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'cliente_id'          => 'required|integer|exists:clientes,id',
            'restaurant_id'       => 'required|integer|exists:restaurants,id',
            'mesa'                => 'nullable|string|max:50',
            'estado'              => 'nullable|in:pendiente,en_entrega,listo,entregado,cancelado',
            'items'               => 'required|array|min:1',

            // opción A: usar menu_item_id
            'items.*.menu_item_id'=> 'required|integer|exists:menu_items,id',
            'items.*.cantidad'    => 'required|integer|min:1',
            'items.*.precio'      => 'required|numeric|min:0', // precio unitario recibido
        ];
    }
}