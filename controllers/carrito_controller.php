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
    // comprobar stock disponible antes de agregar
    $producto = obtenerproduxID($conn, $id_producto);
    if (!$producto) {
        $_SESSION['error'] = "Producto no encontrado";
    } else {
        $stock = intval($producto['stock']);
        // cantidad actualmente en el carrito
        $actualEnCarrito = isset($_SESSION['carrito'][$id_producto]) ? intval($_SESSION['carrito'][$id_producto]) : 0;
        if ($actualEnCarrito + $cantidad > $stock) {
            // no hay stock suficiente
            if ($stock <= 0) {
                $_SESSION['error'] = "Ya no hay stock disponible de '" . $producto['nombre'] . "'";
            } else {
                $_SESSION['error'] = "No puedes agregar $cantidad . Solo quedan $stock disponibles (Tienes $actualEnCarrito en el carrito).";
            }
        } else {
            //  agregar
            agregaralcarrito($id_producto, $cantidad);
            $_SESSION['msg'] = "Se agrego  una $cantidad unidad de '{$producto['nombre']}' al carrito.";
        }
    }
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
