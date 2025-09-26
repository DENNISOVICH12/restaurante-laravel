<?php
// Incluir conexión a la base de datos
require_once 'database.php';

// Obtener bebidas de la base de datos
$query_bebidas = "SELECT id, nombre, descripcion, precio, imagen FROM menu_items WHERE categoria = 'bebida' AND disponible = 1";
$result_bebidas = $conexion->query($query_bebidas);

// Separar en bebidas destacadas y bebidas principales
$bebidas_destacadas = [];
$bebidas_principales = [];

if ($result_bebidas && $result_bebidas->num_rows > 0) {
    while ($bebida = $result_bebidas->fetch_assoc()) {
        if (count($bebidas_destacadas) < 3) {
            $bebidas_destacadas[] = $bebida;
        } else {
            $bebidas_principales[] = $bebida;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú del Restaurante</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="menu container">
            <a href="#" class="logo">RESTAURANTE</a>
            <input type="checkbox" id="menu" />
            <label for="menu">
                <img src="img/LOGO1.png" class="menu-icono" alt="menu">
            </label>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php">PLATOS</a></li>
                    <li><a href="bebidas.php">BEBIDAS</a></li>
                    <li><a href="#">COCTERIA</a></li>
                    <li><a href="#">POSTRES</a></li>
                </ul>
            </nav>
            <div>
                <ul>
                    <li class="submenu">
                        <img src="img/carrito2.png" id="img-carrito" alt="carrito">
                        <div id="carrito">
                            <table id="lista-carrito">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <a href="#" id="vaciar-carrito" class="btn-2">Vaciar carrito</a>
                            <a href="#" id="enviar-pedido" class="btn-2">Enviar pedido</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="header-content container">
            <div class="header-img">
                <img src="img/entradas-banner.png" alt="bebida">
            </div>
            <div class="header-txt">
                <h1>BEBIDAS</h1>
            </div>
        </div>
    </header>

    <section class="ofert container">
        <?php foreach ($bebidas_destacadas as $bebida): ?>
        <div class="ofert-1" data-categoria="bebida">
            <div class="ofert-img1">
                <img src="img-bebidas/<?php echo htmlspecialchars($bebida['imagen']); ?>" alt="<?php echo htmlspecialchars($bebida['nombre']); ?>">
            </div>
            <div class="ofert-txt">
                <h3><?php echo htmlspecialchars($bebida['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($bebida['descripcion']); ?></p>
                <p class="precio"><?php echo number_format($bebida['precio'], 0, ',', '.'); ?> COP</p>
                <a href="#" class="agregar-carrito btn-2" data-id="<?php echo $bebida['id']; ?>">Agregar al carrito</a>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <main class="products container" id="lista-1">
        <h2>Bebidas Principales</h2>
        <div class="product-content">
            <?php foreach ($bebidas_principales as $bebida): ?>
            <div class="product" data-categoria="Bebida">
                <img src="img-bebidas/<?php echo htmlspecialchars($bebida['imagen']); ?>" alt="<?php echo htmlspecialchars($bebida['nombre']); ?>">
                <div class="product-txt">
                    <h3><?php echo htmlspecialchars($bebida['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($bebida['descripcion']); ?></p>
                    <p class="precio"><?php echo number_format($bebida['precio'], 0, ',', '.'); ?> COP</p>
                    <a href="#" class="agregar-carrito btn-2" data-id="<?php echo $bebida['id']; ?>">Agregar al carrito</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="modal-cliente" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Información del Cliente</h2>
            <form id="form-cliente">
                <div class="form-group">
                    <label for="nombre">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección (opcional):</label>
                    <input type="text" id="direccion" name="direccion">
                </div>
                <button type="submit" class="btn-1">Confirmar Pedido</button>
            </form>
        </div>
    </div>

    <script src="js/enviar.js"></script>
</body>
</html>