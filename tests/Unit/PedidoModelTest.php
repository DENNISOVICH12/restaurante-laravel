<?php

namespace Tests\Unit;

use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class PedidoModelTest extends TestCase
{
    public function test_total_attribute_uses_importe_total_when_present(): void
    {
        $pedido = new Pedido();
        $pedido->setRawAttributes(['importe_total' => 35250.75], true);

        $this->assertSame(35250.75, $pedido->total);
    }

    public function test_total_attribute_sums_loaded_detalle_relation(): void
    {
        $pedido = new Pedido();
        $pedido->setRelation('detalle', new Collection([
            new DetallePedido(['importe' => 12000]),
            new DetallePedido(['importe' => 8500.5]),
        ]));

        $this->assertSame(20500.5, $pedido->total);
    }

    public function test_fecha_attribute_formats_created_at(): void
    {
        $pedido = new Pedido();
        $pedido->created_at = Carbon::parse('2024-05-15 18:23:45');

        $this->assertSame('2024-05-15 18:23', $pedido->fecha);
    }
}

