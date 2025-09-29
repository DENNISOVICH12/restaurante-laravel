<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *   schema="Usuario",
 *   type="object",
 *   required={"usuario","password","nombre","correo"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="usuario", type="string", example="admin"),
 *   @OA\Property(property="password", type="string", example="secreto123"),
 *   @OA\Property(property="nombre", type="string", example="Admin"),
 *   @OA\Property(property="apellido", type="string", nullable=true, example="Demo"),
 *   @OA\Property(property="correo", type="string", format="email", example="admin@example.com"),
 *   @OA\Property(property="activo", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoItemInput",
 *   type="object",
 *   required={"cantidad"},
 *   @OA\Property(property="id_menu_item", type="integer", nullable=true, example=12, description="ID del ítem del menú"),
 *   @OA\Property(property="nombre_producto", type="string", nullable=true, example="Limonada"),
 *   @OA\Property(property="precio", type="number", format="float", nullable=true, example=5500),
 *   @OA\Property(property="categoria", type="string", nullable=true, example="bebida"),
 *   @OA\Property(property="cantidad", type="integer", example=2),
 *   @OA\Property(property="descripcion", type="string", nullable=true, example="Sin azúcar")
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoItem",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=101),
 *   @OA\Property(property="id_pedido", type="integer", example=25),
 *   @OA\Property(property="nombre_producto", type="string", example="Limonada"),
 *   @OA\Property(property="precio", type="number", format="float", example=5500),
 *   @OA\Property(property="categoria", type="string", example="bebida"),
 *   @OA\Property(property="cantidad", type="integer", example=2),
 *   @OA\Property(property="descripcion", type="string", nullable=true, example="Sin azúcar")
 * )
 */

/**
 * @OA\Schema(
 *   schema="Pedido",
 *   type="object",
 *   required={"cliente_id","restaurant_id","estado"},
 *   @OA\Property(property="id", type="integer", example=25),
 *   @OA\Property(property="cliente_id", type="integer", example=7),
 *   @OA\Property(property="restaurant_id", type="integer", example=1),
 *   @OA\Property(property="estado", type="string", example="pendiente"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T12:30:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T12:35:00Z")
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoWithRelations",
 *   type="object",
 *   allOf={
 *     @OA\Schema(ref="#/components/schemas/Pedido"),
 *     @OA\Schema(
 *       @OA\Property(
 *         property="cliente",
 *         type="object",
 *         nullable=true,
 *         description="Datos del cliente asociado",
 *         @OA\Property(property="id", type="integer", example=7),
 *         @OA\Property(property="nombre", type="string", example="Laura Gómez"),
 *         @OA\Property(property="telefono", type="string", example="3001112222"),
 *         @OA\Property(property="correo", type="string", nullable=true, example="cliente@example.com")
 *       ),
 *       @OA\Property(
 *         property="detalle",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/PedidoItem")
 *       )
 *     )
 *   }
 * )
 */

/**
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="per_page", type="integer", example=10),
 *   @OA\Property(property="total", type="integer", example=35),
 *   @OA\Property(property="last_page", type="integer", example=4)
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoPaginatedResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Listado de pedidos"),
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Pedido")
 *   ),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoDetailResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Pedido encontrado"),
 *   @OA\Property(property="data", ref="#/components/schemas/PedidoWithRelations")
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoItemsResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Detalle listado"),
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PedidoItem")
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoCreateRequest",
 *   type="object",
 *   required={"cliente_id","restaurant_id","items"},
 *   @OA\Property(property="cliente_id", type="integer", example=7),
 *   @OA\Property(property="restaurant_id", type="integer", example=1),
 *   @OA\Property(property="estado", type="string", example="pendiente"),
 *   @OA\Property(
 *     property="items",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PedidoItemInput")
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   schema="PedidoUpdateRequest",
 *   type="object",
 *   @OA\Property(
 *     property="estado",
 *     type="string",
 *     enum={"pendiente","en_entrega","listo","entregado","cancelado"},
 *     example="en_entrega"
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   schema="ApiMessageResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Pedido eliminado")
 * )
 */

/**
 * @OA\Schema(
 *   schema="ApiErrorResponse",
 *   type="object",
 *   @OA\Property(
 *     property="error",
 *     type="object",
 *     @OA\Property(property="code", type="integer", example=404),
 *     @OA\Property(property="message", type="string", example="No encontrado")
 *   )
 * )
 */

class Schemas {}
