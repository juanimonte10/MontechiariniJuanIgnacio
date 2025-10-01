<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

// Verificar que el usuario sea administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    // Redirigir al login de admin con URL absoluta para evitar rutas relativas que puedan duplicar 'Views'
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $project = rawurlencode(basename(dirname(__DIR__)));
    header("Location: {$proto}://{$host}/{$project}/Views/Admin/loginadm.php?error=No tienes permisos");
    exit;
}

// FUNCIONES AUXILIARES DE VALIDACIÓN
function validarProducto($nombre, $precio, $stock) {
    $errors = [];
    if (empty($nombre)) $errors[] = "El nombre es obligatorio";
    if (!is_numeric($precio) || $precio < 0) $errors[] = "El precio debe ser un número positivo";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "El stock debe ser un número positivo";
    return $errors;
}

// LISTAR PRODUCTOS
if (isset($_GET['action']) && $_GET['action'] === "listar") {
    $productos = obtenerproductos($conn);
    include __DIR__ . "/../Views/Admin/Productos.php"; // vista de gestión de productos
    exit;
}

// AGREGAR PRODUCTO
if (isset($_POST['action']) && $_POST['action'] === "agregar") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = trim($_POST['imagen']);

    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }

    if (agregarproducto($conn, $nombre, $descripcion, $precio, $stock, $imagen)) {
        $_SESSION['msg'] = "Producto agregado exitosamente";
    } else {
        $_SESSION['error'] = "Error al agregar el producto: " . $conn->error;
    }
    header("Location: ../Views/Admin/Productos.php");
    exit;
}

// EDITAR PRODUCTO
if (isset($_POST['action']) && $_POST['action'] === "editar") {
    $id = $_POST['id_producto'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = trim($_POST['imagen']);

    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }

    if (editarproducto($conn, $id, $nombre, $descripcion, $precio, $stock, $imagen)) {
        $_SESSION['msg'] = "Producto actualizado exitosamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el producto: " . $conn->error;
    }
    header("Location: ../Views/Admin/Productos.php");
    exit;
}

// ELIMINAR PRODUCTO
if (isset($_GET['action']) && $_GET['action'] === "eliminar") {
    $id = $_GET['id_producto'];
    if (eliminarproducto($conn, $id)) {
        $_SESSION['msg'] = "Producto eliminado exitosamente";
    } else {
        $_SESSION['error'] = "Error al eliminar el producto: " . $conn->error;
    }
    header("Location: ../Views/Admin/Productos.php");
    exit;
}
?>

