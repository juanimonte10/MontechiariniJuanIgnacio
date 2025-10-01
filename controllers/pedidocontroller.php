<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

// CREAR pedido
if (isset($_POST['action']) && $_POST['action'] == "crear_pedido") {
    session_start();
    if (!isset($_SESSION['cliente'])) {
        header("Location: ../Views/clientes/Login.php?error= Debes iniciar sesión");
        exit;
    }
    // Obtener id del cliente desde la sesión (puede estar en diferentes claves según el login)
    $id_cliente = null;
    if (isset($_SESSION['cliente']['id'])) {
        $id_cliente = $_SESSION['cliente']['id'];
    } elseif (isset($_SESSION['cliente']['id_usuario'])) {
        $id_cliente = $_SESSION['cliente']['id_usuario'];
    } elseif (isset($_SESSION['usuario']['id_usuario'])) {
        $id_cliente = $_SESSION['usuario']['id_usuario'];
    } elseif (isset($_SESSION['usuario']['id'])) {
        $id_cliente = $_SESSION['usuario']['id'];
    }

    if (empty($id_cliente)) {
        header("Location: ../Views/clientes/Login.php?error= Debes iniciar sesión");
        exit;
    }

    $carrito = $_SESSION['carrito'] ?? [];
    $resultado = crearpedido($conn, $id_cliente, $carrito);

    if ($resultado === true) {
        unset($_SESSION['carrito']); // vaciar carrito
        header("Location: ../Index.php?msg=Pedido realizado con éxito");
    } else {
        // $resultado contiene el mensaje de error devuelto por la función
        header("Location: ../Views/clientes/Carrito.php?error=" . urlencode($resultado));
    }
    exit;
}

// VER pedidos (para admin)
if (isset($_GET['action']) && $_GET['action'] == "listar_pedidos") {
    $pedidos = obtenerpedidos($conn);
    include __DIR__ . "/../Views/Admin/pedidos.php";
    exit;
}

// VER detalle de un pedido (para admin)
if (isset($_GET['action']) && $_GET['action'] == "detalle") {
    $id_pedido = $_GET['id'];
    $detalle = obtenerdetallepedido($conn, $id_pedido);
    include __DIR__ . "/../Views/Admin/detalle_pedido.php";
    exit;
}
?>
