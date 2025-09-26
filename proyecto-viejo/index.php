<?php
// Incluir conexión a la base de datos
require_once 'database.php';

// Obtener platos de la base de datos
$query_platos = "SELECT id, nombre, descripcion, precio, imagen FROM menu_items WHERE categoria = 'plato' AND disponible = 1";
$result_platos = $conexion->query($query_platos);

// Obtener entradas (platos marcados como entradas)
$platos_principales = [];
$entradas = [];

if ($result_platos && $result_platos->num_rows > 0) {
    while ($plato = $result_platos->fetch_assoc()) {
        // Aquí podrías tener una lógica para distinguir entre entradas y platos principales
        // Por ahora, asumiremos que las entradas son los primeros 3 platos
        if (count($entradas) < 3) {
            $entradas[] = $plato;
        } else {
            $platos_principales[] = $plato;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MENU</title>
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
                                        <th>imagen</th>
                                        <th>nombre</th>
                                        <th>tipo</th>
                                        <th>precio</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <a href="#" id="vaciar-carrito" class="btn-2">vaciar carrito</a>
                            <a href="#" id="enviar-pedido" class="btn-2">Enviar pedido</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="header-content container">
            <div class="header-img">
                <img src="img/entrada-banner.jpg" alt="Entradas">
            </div>
            <div class="header-txt">
                <h1>ENTRADAS</h1>
            </div>
        </div>
    </header>

    <section class="ofert container">
        <?php foreach ($entradas as $entrada): ?>
        <div class="ofert-1" data-categoria="Plato">
            <div class="ofert-img1">
                <img src="img/<?php echo htmlspecialchars($entrada['imagen']); ?>" alt="<?php echo htmlspecialchars($entrada['nombre']); ?>">
            </div>
            <div class="ofert-txt">
                <h3><?php echo htmlspecialchars($entrada['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($entrada['descripcion']); ?></p>
                <p class="precio"><?php echo number_format($entrada['precio'], 0, ',', '.'); ?> COP</p>
                <a href="#" class="agregar-carrito btn-2" data-id="<?php echo $entrada['id']; ?>">agregar al carrito</a>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
    
    <main class="products container" id="lista-1">
        <h2>platos principales</h2>
        <div class="product-content">
            <?php foreach ($platos_principales as $plato): ?>
            <div class="product" data-categoria="Plato">
                <img src="img/<?php echo htmlspecialchars($plato['imagen']); ?>" alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                <div class="product-txt">
                    <h3><?php echo htmlspecialchars($plato['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                    <p class="precio"><?php echo number_format($plato['precio'], 0, ',', '.'); ?> COP</p>
                    <a href="#" class="agregar-carrito btn-2" data-id="<?php echo $plato['id']; ?>">agregar al carrito</a>
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