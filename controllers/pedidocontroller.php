<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

if (isset($_POST['action']) && $_POST['action'] == "crear_pedido") {
    session_start();
    if (!isset($_SESSION['cliente'])) {
        header("Location: ../Views/clientes/Login.php?error= Debes iniciar sesión");
        exit;
    }

    // Obtener id del cliente
    $id_cliente = $_SESSION['cliente']['id'] ?? $_SESSION['cliente']['id_usuario'] ?? null;
    if (empty($id_cliente)) {
        header("Location: ../Views/clientes/Login.php?error= Debes iniciar sesión");
        exit;
    }

    $carrito = $_SESSION['carrito'] ?? [];

    //  VALIDACIÓN DE STOCK 
    $stock_insuficiente = [];
    foreach ($carrito as $id_producto => $cantidad) {
        $producto = obtenerproduxID($conn, $id_producto);
        if ($producto['stock'] < $cantidad) {
            $stock_insuficiente[] = "{$producto['nombre']} (Stock disponible: {$producto['stock']})";
        }
    }

    if (!empty($stock_insuficiente)) {
        $mensaje = "No hay suficiente stock de: " . implode(", ", $stock_insuficiente);
        header("Location: ../Views/clientes/Carrito.php?error=" . urlencode($mensaje));
        exit;
    }

    // Crear pedido
    $resultado = crearpedido($conn, $id_cliente, $carrito);

    if ($resultado === true) {
        unset($_SESSION['carrito']); // vaciar carrito
        header("Location: ../Index.php?msg=Pedido realizado con éxito");
    } else {
        header("Location: ../Views/clientes/Carrito.php?error=" . urlencode($resultado));
    }
    exit;
}

// Acciones de admin: listar pedidos y ver detalle
if (isset($_GET['action']) && $_GET['action'] == "listar_pedidos") {
    $pedidos = obtenerpedidos($conn);
    include __DIR__ . "/../Views/Admin/pedidos.php";
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == "detalle") {
    $id_pedido = $_GET['id'];
    $detalle = obtenerdetallepedido($conn, $id_pedido);
    include __DIR__ . "/../Views/Admin/detalle_pedido.php";
    exit;
}
?>

