<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

if (!isset($_SESSION["cliente"])) {
    // Redirigir al login de clientes (ruta relativa desde controllers)
    header("Location: ../Views/clientes/Login.php?error=Debes iniciar sesión");
    exit;
}

if (isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = isset($_POST['cantidad']) ? max(1, intval($_POST['cantidad'])) : 1;
    agregaralcarrito($id_producto, $cantidad);
    header("Location: ../Views/clientes/Carrito.php");
    exit;
}

header("Location: ../Index.php");
exit;
?>
