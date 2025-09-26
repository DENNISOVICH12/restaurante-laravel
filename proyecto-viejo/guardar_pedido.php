<?php
// Incluir archivo de conexión
include 'database.php';

// Asegurarse de recibir datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar la decodificación de JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Datos JSON mal formados."]);
    exit;
}

// Verificar que todos los datos necesarios están presentes
if (isset($data['nombre'], $data['telefono'], $data['pedido']) && !empty($data['pedido'])) {
    try {
        // Iniciar transacción
        $conexion->begin_transaction();
        
        // Datos del cliente
        $nombre = $data['nombre'];
        $telefono = $data['telefono'];
        $direccion = isset($data['direccion']) ? $data['direccion'] : '';
        $productos = $data['pedido'];
        
        // Verificar si el cliente ya existe
        $stmt = $conexion->prepare("SELECT id FROM clientes WHERE telefono = ? LIMIT 1");
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Cliente existente
            $row = $result->fetch_assoc();
            $id_cliente = $row['id'];
            
            // Actualizar información si es necesario
            $stmt = $conexion->prepare("UPDATE clientes SET nombre_cliente = ?, direccion = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nombre, $direccion, $id_cliente);
            $stmt->execute();
        } else {
            // Nuevo cliente
            $stmt = $conexion->prepare("INSERT INTO clientes (nombre_cliente, telefono, direccion, fecha_registro) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $nombre, $telefono, $direccion);
            $stmt->execute();
            $id_cliente = $stmt->insert_id;
        }
        
        // Crear nuevo pedido
        $stmt = $conexion->prepare("INSERT INTO pedidos (id_cliente, fecha, estado) VALUES (?, NOW(), 'pendiente')");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $id_pedido = $stmt->insert_id;
        
        // Procesar productos
        $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedido (id_pedido, nombre_producto, precio, cantidad, categoria, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Agrupar productos iguales
        $productos_agrupados = [];
        foreach ($productos as $producto) {
            $id_producto = isset($producto['id']) ? $producto['id'] : 0;
            $key = $producto['nombre'] . '_' . $producto['categoria'];
            
            // Buscar precio actualizado desde la base de datos si hay ID de producto
            if ($id_producto > 0) {
                $stmt_precio = $conexion->prepare("SELECT precio FROM menu_items WHERE id = ?");
                $stmt_precio->bind_param("i", $id_producto);
                $stmt_precio->execute();
                $result_precio = $stmt_precio->get_result();
                
                if ($result_precio->num_rows > 0) {
                    $row_precio = $result_precio->fetch_assoc();
                    $producto['precio'] = $row_precio['precio'];
                }
            }
            
            if (!isset($productos_agrupados[$key])) {
                $productos_agrupados[$key] = [
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'categoria' => $producto['categoria'],
                    'cantidad' => 1,
                    'descripcion' => isset($producto['descripcion']) ? $producto['descripcion'] : ''
                ];
            } else {
                $productos_agrupados[$key]['cantidad']++;
                // Combinar descripciones si son diferentes
                if (isset($producto['descripcion']) && !empty($producto['descripcion']) && 
                    $producto['descripcion'] != $productos_agrupados[$key]['descripcion']) {
                    $productos_agrupados[$key]['descripcion'] .= '; ' . $producto['descripcion'];
                }
            }
        }
        
        // Insertar productos agrupados
        foreach ($productos_agrupados as $producto) {
            $nombre_producto = $producto['nombre'];
            $precio = $producto['precio'];
            $cantidad = $producto['cantidad'];
            $categoria = $producto['categoria'];
            $descripcion = $producto['descripcion'];
            
            $stmt_detalle->bind_param("isdiss", $id_pedido, $nombre_producto, $precio, $cantidad, $categoria, $descripcion);
            $stmt_detalle->execute();
        }
        
        // Completar transacción
        $conexion->commit();
        
        echo json_encode([
            "status" => "success", 
            "message" => "Pedido recibido correctamente",
            "id_pedido" => $id_pedido
        ]);
        
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $conexion->rollback();
        echo json_encode([
            "status" => "error", 
            "message" => "Error al procesar el pedido: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Faltan datos obligatorios."
    ]);
}
?>