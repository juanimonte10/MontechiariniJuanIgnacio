

<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/App/helpers/Funciones.php";

// Si no hay usuario logueado (cliente o admin), redirigir al login cliente
if (!isset($_SESSION['cliente']) && !isset($_SESSION['usuario'])) {
    header("Location: Views/clientes/Login.php?error=Debes iniciar sesión");
    exit;
}

// Obtener todos los productos
$productos = obtenerproductos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <style>
        .producto {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        img {
            max-width: 100%;
            height: 150px;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['cliente'])): ?>
        <h1>Bienvenid@ <?= htmlspecialchars($_SESSION['cliente']['nombre']); ?> </h1>
    <?php elseif (isset($_SESSION['usuario'])): ?>
        <h1>Bienvenido <?= htmlspecialchars($_SESSION['usuario']['nombre']);  ?> Administrador  </h1> 
    <?php endif; ?>

    <h2>Catálogo de Productos</h2>
    <div>
        <?php foreach ($productos as $p): ?>
            <div class="producto">
                <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                <p><?= htmlspecialchars($p['descripcion']) ?></p>
                <p><strong>$<?= number_format($p['precio'], 2) ?></strong></p>
                <p>Stock: <?= $p['stock'] ?><?php if ($p['stock'] == 0): ?><span style="color:red; font-weight:bold;"> (Sin stock disponible)</span><?php endif; ?></p>
                <?php if (!empty($p['imagen'])): ?>
                    <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                <?php endif; ?>
                <br>
                <form action="controllers/carrito_controller.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                    <button type="submit" <?= ($p['stock'] == 0) ? 'disabled' : '' ?>>Agregar al carrito</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
