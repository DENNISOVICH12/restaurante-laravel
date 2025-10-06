<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PedidoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        app()->forgetInstance('current_restaurant_id');
        parent::tearDown();
    }

    public function test_can_list_pedidos_with_totals(): void
    {
        $ids = $this->seedPedidoConDetalle();

        $response = $this->getJson('/api/pedidos');

        $response->assertOk()
            ->assertJsonPath('message', 'Listado de pedidos')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $ids['pedido_id'])
            ->assertJsonPath('data.0.cliente.id', $ids['cliente_id']);

        $this->assertSame(24000.0, (float) $response->json('data.0.total'));
        $this->assertSame('Juan Pérez', $response->json('data.0.cliente.nombre_cliente'));
    }

    public function test_can_show_single_pedido_with_detail(): void
    {
        $ids = $this->seedPedidoConDetalle();

        $response = $this->getJson("/api/pedidos/{$ids['pedido_id']}");

        $response->assertOk()
            ->assertJsonPath('message', 'Pedido encontrado')
            ->assertJsonPath('data.id', $ids['pedido_id'])
            ->assertJsonPath('data.detalle.0.cantidad', 2);

        $this->assertSame(24000.0, (float) $response->json('data.total'));
        $this->assertSame($ids['menu_item_id'], $response->json('data.detalle.0.menu_item_id'));
    }

    private function seedPedidoConDetalle(): array
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'nombre' => 'Restaurante Test',
            'slug' => 'restaurante-test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app()->instance('current_restaurant_id', $restaurantId);

        $clienteId = DB::table('clientes')->insertGetId([
            'nombre_cliente' => 'Juan Pérez',
            'telefono' => '3001234567',
            'direccion' => 'Calle 1 #2-34',
            'fecha_registro' => now(),
        ]);

        $menuItemId = DB::table('menu_items')->insertGetId([
            'nombre' => 'Hamburguesa Clásica',
            'descripcion' => 'Carne, queso y vegetales frescos',
            'categoria' => 'plato',
            'precio' => 12000,
            'imagen' => null,
            'disponible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pedidoId = DB::table('pedidos')->insertGetId([
            'restaurant_id' => $restaurantId,
            'cliente_id' => $clienteId,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pedido_detalles')->insert([
            'restaurant_id' => $restaurantId,
            'pedido_id' => $pedidoId,
            'menu_item_id' => $menuItemId,
            'cantidad' => 2,
            'precio_unitario' => 12000,
            'importe' => 24000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'restaurant_id' => $restaurantId,
            'cliente_id' => $clienteId,
            'menu_item_id' => $menuItemId,
            'pedido_id' => $pedidoId,
        ];
    }
}

