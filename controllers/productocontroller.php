<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

// Verificar que el usuario sea administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {    
    // Simplificamos la redirección asumiendo una estructura de proyecto consistente.
    // La ruta relativa es más mantenible si la estructura de carpetas no cambia.
    $_SESSION['error'] = "No tienes permisos para acceder a esta sección.";
    header("Location: ../Views/Admin/Loginadm.php");
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

// Función para procesar URLs de imágenes de Google
function processGoogleImageUrl($url) {
    $parsed = parse_url($url);
    if (!empty($parsed['host']) && strpos($parsed['host'], 'google') !== false && !empty($parsed['query'])) {
        parse_str($parsed['query'], $qs);
        if (!empty($qs['imgurl'])) return $qs['imgurl'];
    }
    return $url;
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
    $imagen_final = '';

    // Validación de datos básicos
    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }
    
    // Procesamiento de la imagen desde URL
    $uploadDir = __DIR__ . "/../Public/img/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $url = trim($_POST['imagen_url_input'] ?? '');
    if (!empty($url)) {
        $imagen_final = processGoogleImageUrl($url);
    }
    
    $resultado = agregarproducto($conn, $nombre, $descripcion, $precio, $stock, $imagen_final);
    
    if ($resultado === "duplicado") {
        $_SESSION['error'] = "Ya existe un producto con el nombre '$nombre'";
    } elseif ($resultado) {
        $_SESSION['msg'] = "Producto agregado exitosamente";
    } else {
        $_SESSION['error'] = "Error al agregar el producto.";
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
    $imagen_final = '';

    // Validación de datos básicos
    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }
    
    // Obtener imagen existente para conservar si no se cambia
    $existing = obtenerproduxID($conn, $id);
    $existingImagen = $existing['imagen'] ?? '';

    // Lógica para determinar la imagen final
    $url = trim($_POST['imagen_url_input'] ?? '');
    $imagen_enviada = trim($_POST['imagen_final'] ?? '');

    if (!empty($url) && $url !== $existingImagen) {
        // 1. Si se ingresó una nueva URL, se usa esa.
        $imagen_final = processGoogleImageUrl($url);
    } elseif ($imagen_enviada === 'DELETE') {
        // 2. Si se presionó "Eliminar imagen", se usa la de por defecto.
        $imagen_final = '../../Public/img/placeholder.png';
    } else {
        // 3. Si no pasó nada de lo anterior, se mantiene la imagen existente.
        $imagen_final = $existingImagen;
    }

    if (editarproducto($conn, $id, $nombre, $descripcion, $precio, $stock, $imagen_final)) {
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
        $_SESSION['error'] = "Error al eliminar el producto.";
    }
    header("Location: ../Views/Admin/Productos.php");
    exit;
}

?>
