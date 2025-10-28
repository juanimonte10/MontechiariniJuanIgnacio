<?php
session_start();
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../App/helpers/Funciones.php";

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: Loginadm.php");
    exit;
}

// Validar si viene el id del pedido (aceptar 'id_pedido' o 'id')
$id_pedido = null;
if (isset($_GET['id_pedido'])) {
    $id_pedido = intval($_GET['id_pedido']);
} elseif (isset($_GET['id'])) {
    $id_pedido = intval($_GET['id']);
} else {
    die("Falta el ID del pedido.");
}

// Obtener detalle (usar la función definida en helpers)
$detalles = obtenerdetallepedido($conn, $id_pedido);

// Si no hay resultados
if (!$detalles) {
    die("No se encontraron productos para este pedido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>
    <link rel="stylesheet" href="../../Public/css/Registro.css">
</head>
<body>
<div class="admin-container">


<h1>Detalle del Pedido #<?= htmlspecialchars($id_pedido) ?></h1>

<table border="1">
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
    </tr>
    <?php 
    $total = 0;
    foreach ($detalles as $d): 
        $total += $d["subtotal"];
    ?>
    <tr>
        <td><?= htmlspecialchars($d["nombre"]) ?></td>
        <td><?= htmlspecialchars($d["cantidad"]) ?></td>
        <td>$<?= number_format($d["subtotal"] / $d["cantidad"], 2) ?></td>
        <td>$<?= htmlspecialchars($d["subtotal"]) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td><strong>$<?= number_format($total, 2) ?></strong></td>
    </tr>
</table>

<a href="Pedidos.php" class="btn-volver">Volver a pedidos</a>
    </div>
</body>
</html>

