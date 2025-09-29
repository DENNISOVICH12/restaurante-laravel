# Ejemplo de datos válidos para crear un pedido

Para registrar un pedido mediante `POST /api/pedidos` envía un JSON con la siguiente estructura mínima:

```json
{
  "id_cliente": 1,
  "restaurant_id": 1,
  "estado": "pendiente",
  "items": [
    {
      "id_menu_item": 2,
      "cantidad": 2
    },
    {
      "nombre_producto": "Ensalada César",
      "precio": 18000,
      "categoria": "entrada",
      "cantidad": 1,
      "descripcion": "sin aderezo"
    }
  ]
}
```

## Reglas clave

- `id_cliente` debe existir en la tabla `clientes`.
- `restaurant_id` debe existir en la tabla `restaurants`.
- Cada elemento de `items` acepta dos modalidades:
  - Enviar `id_menu_item` (el sistema completará nombre, precio, categoría y descripción desde el menú).
  - O especificar `nombre_producto`, `precio` y `categoria` manualmente.
- `cantidad` siempre es obligatoria y debe ser un entero mayor o igual a 1.
- `descripcion` es opcional.

Si cualquiera de estas condiciones no se cumple, el servicio responderá con un error de validación (`HTTP 422`).
