<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: Loginadm.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrativo</title>
    <link rel="stylesheet" href="../../Public/css/registro.css">
</head>
<body>
    <div class="admin-container">


<h1>Bienvenido Administrador, <?php echo $_SESSION["usuario"]["nombre"]; ?> </h1>

<ul>
    <li><a href="Productos.php"> Gestionar Productos</a></li>
    <li><a href="Pedidos.php"> Ver Pedidos</a></li>
    <li><a href="../../logout.php"> Cerrar Sesión</a></li>
</ul>
