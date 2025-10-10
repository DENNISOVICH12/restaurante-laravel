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

    public function test_can_create_pedido_without_header_when_single_restaurant_exists(): void
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'nombre' => 'Restaurante Único',
            'slug' => 'restaurante-unico',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clienteId = DB::table('clientes')->insertGetId([
            'nombre_cliente' => 'Ana Torres',
            'telefono' => '3009991122',
            'direccion' => 'Calle 123',
            'fecha_registro' => now(),
        ]);

        $menuItemId = DB::table('menu_items')->insertGetId([
            'nombre' => 'Arepa Rellena',
            'descripcion' => 'Con queso y hogao',
            'categoria' => 'plato',
            'precio' => 8000,
            'imagen' => null,
            'disponible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_id' => $clienteId,
            'items' => [
                ['menu_item_id' => $menuItemId, 'cantidad' => 2, 'precio' => 8000],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Pedido creado')
            ->assertJsonPath('data.restaurant_id', $restaurantId)
            ->assertJsonPath('data.detalle.0.menu_item_id', $menuItemId)
            ->assertJsonPath('data.detalle.0.cantidad', 2);

        $pedidoId = $response->json('data.id');

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedidoId,
            'restaurant_id' => $restaurantId,
            'cliente_id' => $clienteId,
        ]);

        $this->assertDatabaseHas('pedido_detalles', [
            'pedido_id' => $pedidoId,
            'menu_item_id' => $menuItemId,
            'cantidad' => 2,
            'precio_unitario' => '8000.00',
            'importe' => '16000.00',
        ]);
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

