<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'estado'     => 'nullable|in:pendiente,en_entrega,listo,entregado,cancelado',
            'items'      => 'required|array|min:1',
            // Aceptamos 2 formas:
            // a) con id_menu_item -> lo resolvemos a nombre/precio/categoria
            // b) sin id_menu_item -> se envían nombre_producto, precio, categoria
            'items.*.id_menu_item'   => 'nullable|integer|exists:menu_items,id',
            'items.*.nombre_producto'=> 'required_without:items.*.id_menu_item|string|max:100',
            'items.*.precio'         => 'required_without:items.*.id_menu_item|numeric|min:0',
            'items.*.categoria'      => 'required_without:items.*.id_menu_item|string|max:50',
            'items.*.cantidad'       => 'required|integer|min:1',
            'items.*.descripcion'    => 'nullable|string',
        ];
    }
}