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
    // decidir a dónde redirigir: si el formulario envía redirect=carrito ir al carrito,
    // si envía redirect=back (o por defecto) volver a la página referer para no interrumpir la navegación
    $redirect = $_POST['redirect'] ?? 'back';
    if ($redirect === 'carrito') {
        header("Location: ../Views/clientes/Carrito.php");
        exit;
    }

    // intentar volver a la página anterior
    if (!empty($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    header("Location: ../Index.php");
    exit;
}

header("Location: ../Index.php");
exit;
?>
