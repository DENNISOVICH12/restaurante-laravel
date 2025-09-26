<?php
// Script para importar platos y bebidas al sistema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
require_once 'database.php';

// Función para limpiar y formatear precios
function limpiarPrecio($precio) {
    // Eliminar "COP" y cualquier espacio
    $precio = str_replace('COP', '', $precio);
    $precio = str_replace('.', '', $precio);
    $precio = trim($precio);
    
    // Convertir a número
    return (float) $precio;
}

// Verificar si la tabla existe
$check_table = $conexion->query("SHOW TABLES LIKE 'menu_items'");
if ($check_table->num_rows == 0) {
    // Crear la tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10,2) NOT NULL,
        imagen VARCHAR(255),
        categoria ENUM('plato', 'bebida', 'postre') NOT NULL,
        disponible BOOLEAN NOT NULL DEFAULT TRUE,
        fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conexion->query($sql) === FALSE) {
        die("Error al crear la tabla menu_items: " . $conexion->error);
    }
    
    echo "Tabla menu_items creada correctamente.<br>";
}

// Array para almacenar los platos
$platos = [
    [
        'nombre' => 'Empanadas con aji',
        'descripcion' => 'Tradicionales empanadas acompañadas de ají.',
        'precio' => 15000,
        'imagen' => 'entrada1.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Tostada española',
        'descripcion' => 'Tostada al estilo español con ingredientes frescos.',
        'precio' => 12000,
        'imagen' => 'entrada3.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Sushi',
        'descripcion' => 'Variedad de sushi fresco.',
        'precio' => 25000,
        'imagen' => 'entrada4.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Churrasco',
        'descripcion' => 'Este corte se obtiene de la parte trasera de la res, acompañado con papas a la francesa y vegetales.',
        'precio' => 45000,
        'imagen' => 'PLATO1.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Ensalada marina',
        'descripcion' => 'Contiene mariscos, lechuga, pequeños cortes de naranja y tomate, es acompañada de una vinagreta agridulce.',
        'precio' => 30000,
        'imagen' => 'PLATO2.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Filet Mignon',
        'descripcion' => 'Un corte de res que se caracteriza por ser un corte muy tierno, este corte proviene de la parte de la cola de la res. Tiene como base una salsa de champiñón y zanahoria.',
        'precio' => 40000,
        'imagen' => 'PLATO3.png',
        'categoria' => 'plato'
    ],
    [
        'nombre' => 'Pulpo',
        'descripcion' => 'Este plato tiene como corte principal el pulpo y es acompañado de caviar y una salsa marinera.',
        'precio' => 80000,
        'imagen' => 'PLATO4.png',
        'categoria' => 'plato'
    ]
];

// Array para almacenar las bebidas
$bebidas = [
    [
        'nombre' => 'Té helado',
        'descripcion' => 'Bebida suave y refrescante.',
        'precio' => 8000,
        'imagen' => 'te.jpg',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Limonada',
        'descripcion' => 'Bebida de limón natural.',
        'precio' => 5000,
        'imagen' => 'limonada1.jpg',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Agua mineral',
        'descripcion' => 'Bebida refrescante y natural.',
        'precio' => 2500,
        'imagen' => 'agua mineral.jpg',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Coca Cola',
        'descripcion' => 'Bebida gaseosa refrescante.',
        'precio' => 4500,
        'imagen' => 'coca-cola.png',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Sprite',
        'descripcion' => 'Gaseosa con sabor a limón y lima.',
        'precio' => 3500,
        'imagen' => 'Sprite.webp',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Pepsi',
        'descripcion' => 'Gaseosa refrescante de sabor cola.',
        'precio' => 3500,
        'imagen' => 'pepsi.png',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Piña colada',
        'descripcion' => 'Bebida tropical de piña y coco.',
        'precio' => 8000,
        'imagen' => '125707_large.jpg',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Cerezada',
        'descripcion' => 'Limón con cereza dulce.',
        'precio' => 8000,
        'imagen' => 'limonada_cerezada.png',
        'categoria' => 'bebida'
    ],
    [
        'nombre' => 'Coctel',
        'descripcion' => 'Mezcla refrescante con licor.',
        'precio' => 20000,
        'imagen' => 'coctel.png',
        'categoria' => 'bebida'
    ]
];

// Combinar los arrays
$menu_items = array_merge($platos, $bebidas);

// Insertar los ítems en la base de datos
$stmt = $conexion->prepare("INSERT INTO menu_items (nombre, descripcion, precio, imagen, categoria, disponible) VALUES (?, ?, ?, ?, ?, 1)");

$count = 0;
foreach ($menu_items as $item) {
    // Verificar si el ítem ya existe
    $check = $conexion->prepare("SELECT id FROM menu_items WHERE nombre = ? AND categoria = ?");
    $check->bind_param("ss", $item['nombre'], $item['categoria']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo "El ítem '{$item['nombre']}' ya existe en la base de datos.<br>";
        continue; // Saltar a la siguiente iteración
    }
    
    $stmt->bind_param("ssdss", $item['nombre'], $item['descripcion'], $item['precio'], $item['imagen'], $item['categoria']);
    
    if ($stmt->execute()) {
        $count++;
        echo "Ítem '{$item['nombre']}' añadido correctamente.<br>";
    } else {
        echo "Error al añadir '{$item['nombre']}': " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conexion->close();

echo "<h3>Proceso completado. Se añadieron $count nuevos ítems al menú.</h3>";
echo "<a href='dashboard.php'>Volver al panel de administración</a>";
?>