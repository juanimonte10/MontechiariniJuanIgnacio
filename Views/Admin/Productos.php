<?php
session_start();
require_once "../../config/db.php";
require_once "../../App/helpers/Funciones.php";

// Verificar rol admin
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: Loginadm.php?error=No tienes permisos");
    exit;
}

// Capturar mensajes de sesión
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

// Manejar edición (cargar datos del producto a editar)
$editarproducto = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $editarproducto = obtenerproduxID($conn, $id);
}

// Obtener todos los productos
$productos = obtenerproductos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        img { max-width: 50px; }
        .msg { color: green; }
        .error { color: red; }
        form { margin-bottom: 20px; }
        input { margin: 5px; padding: 5px; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>

<h1>Gestión de Productos</h1>

<?php if ($msg): ?>
    <p class="msg"><?= $msg ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<!-- Formulario Agregar o Editar Producto -->
<h2><?= $editarproducto ? "Editar Producto" : "Agregar Producto" ?></h2>
<form action="../../controllers/productocontroller.php" method="POST">
    <input type="hidden" name="action" value="<?= $editarproducto ? "editar" : "agregar" ?>">
    <?php if ($editarproducto): ?>
        <input type="hidden" name="id_producto" value="<?= $editarproducto['id_producto'] ?>">
    <?php endif; ?>
    <input type="text" name="nombre" placeholder="Nombre" value="<?= $editarproducto['nombre'] ?? '' ?>" required>
    <input type="text" name="descripcion" placeholder="Descripción" value="<?= $editarproducto['descripcion'] ?? '' ?>">
    <input type="number" step="0.01" name="precio" placeholder="Precio" value="<?= $editarproducto['precio'] ?? '' ?>" required>
    <input type="number" name="stock" placeholder="Stock" value="<?= $editarproducto['stock'] ?? '' ?>" required>
    <input type="text" name="imagen" placeholder="Imagen (URL)" value="<?= $editarproducto['imagen'] ?? '' ?>">
    <button type="submit"><?= $editarproducto ? "Actualizar Producto" : "Agregar Producto" ?></button>
    <?php if ($editarproducto): ?>
        <a href="Productos.php">Cancelar</a>
    <?php endif; ?>
</form>

<hr>

<h2>Lista de Productos</h2>
<table>
    <tr>
        <th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th><th>Imagen</th><th>Acciones</th>
    </tr>
    <?php foreach ($productos as $p): ?>
    <tr>
        <td><?= $p["id_producto"] ?></td>
        <td><?= htmlspecialchars($p["nombre"]) ?></td>
        <td><?= htmlspecialchars($p["descripcion"]) ?></td>
        <td>$<?= number_format($p["precio"], 2) ?></td>
        <td>
            <?= $p["stock"] ?>
            <?php if ($p["stock"] == 0): ?>
                <span style="color:red; font-weight:bold;"> (Sin stock disponible)</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!empty($p["imagen"])): ?>
                <img src="<?= htmlspecialchars($p["imagen"]) ?>" alt="<?= htmlspecialchars($p["nombre"]) ?>">
            <?php endif; ?>
        </td>
        <td>
            <a href="Productos.php?editar=<?= $p["id_producto"] ?>">Editar</a>
            <a href="../../controllers/productocontroller.php?action=eliminar&id_producto=<?= $p["id_producto"] ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<form action="Loginadm.php" method="get" style="display:inline-block; margin:10px;">
    <button type="submit">Volver al Login </button>
</form>
<form action="../../logout.php" method="get" style="display:inline-block; margin:10px;">
    <button type="submit">Cerrar sesión</button>
</form>

</body>
</html>


