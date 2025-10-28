<?php
session_start();
require_once "../../config/db.php";
require_once "../../App/helpers/Funciones.php";

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: Loginadm.php");
    exit;
}

$pedidos = obtenerpedidos($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="../../Public/css/Registro.css">
</head>
<body>
    <div class="admin-container">

<h1>Pedidos Realizados</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Cliente</th>
        <th>Detalle</th>
    </tr>
    <?php foreach ($pedidos as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p["id_pedido"]) ?></td>
        <td><?= htmlspecialchars($p["fecha"]) ?></td>
        <td>$<?= htmlspecialchars($p["total"]) ?></td>
        <td><?= htmlspecialchars($p["cliente"]) ?></td>
        <td><a href="detalle_pedido.php?id=<?= $p["id_pedido"] ?>">Ver detalle</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<form action="Loginadm.php" method="get" style="display:inline-block; margin:10px;">
    <button type="submit">Volver al Dashboard </button>
</form>
