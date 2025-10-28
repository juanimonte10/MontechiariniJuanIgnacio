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
    $imagen_final = '';

    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }

    // Procesar imagen según tipo
    $imagen_tipo = $_POST['imagen_tipo'] ?? 'archivo';
    $uploadDir = __DIR__ . "/../Public/img/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if ($imagen_tipo === 'archivo' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $origName = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = "Extensión de imagen no permitida.";
            header("Location: ../Views/Admin/Productos.php");
            exit;
        }
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $dest = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
            // ruta relativa usada por las vistas (desde Views/Admin)
            $imagen_final = '../../Public/img/uploads/' . $filename;
        } else {
            $_SESSION['error'] = "Error al mover la imagen subida.";
            header("Location: ../Views/Admin/Productos.php");
            exit;
        }
    } elseif ($imagen_tipo === 'url') {
        $url = trim($_POST['imagen_url_input'] ?? $_POST['imagen_final'] ?? '');
        // extraer imgurl si viene de Google imgres
        if ($url) {
            $parsed = parse_url($url);
            if (!empty($parsed['host']) && strpos($parsed['host'], 'google') !== false && !empty($parsed['query'])) {
                parse_str($parsed['query'], $qs);
                if (!empty($qs['imgurl'])) $url = $qs['imgurl'];
            }
            // si es un data URI (pegado desde portapapeles), guardarlo como archivo
            if (strpos($url, 'data:image') === 0) {
                if (preg_match('/data:image\/(png|jpeg|jpg|gif|webp);base64,(.*)$/', $url, $m)) {
                    $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                    $data = base64_decode($m[2]);
                    $filename = time() . '_' . uniqid() . '.' . $ext;
                    $dest = $uploadDir . $filename;
                    if (file_put_contents($dest, $data) !== false) {
                        $imagen_final = '../../Public/img/uploads/' . $filename;
                    }
                }
            } else {
                // simple validación por extensión
                $lower = strtolower($url);
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)(\?|$)/', $lower)) {
                    $imagen_final = $url;
                } else {
                    // intentar descargar y guardar si se puede
                    if (@file_get_contents($url)) {
                        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $ext = $ext ? $ext : 'jpg';
                        $filename = time() . '_' . uniqid() . '.' . $ext;
                        $dest = $uploadDir . $filename;
                        $content = @file_get_contents($url);
                        if ($content !== false && file_put_contents($dest, $content) !== false) {
                            $imagen_final = '../../Public/img/uploads/' . $filename;
                        } else {
                            // si falló la descarga, guardar la URL tal cual (puede ser externa)
                            $imagen_final = $url;
                        }
                    } else {
                        $imagen_final = $url; // dejarla como URL externa
                    }
                }
            }
        }
    }

    $resultado = agregarproducto($conn, $nombre, $descripcion, $precio, $stock, $imagen_final);
    
    if ($resultado === "duplicado") {
        $_SESSION['error'] = "Ya existe un producto con el nombre '$nombre'";
    } elseif ($resultado) {
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
    $imagen_final = '';

    $errores = validarProducto($nombre, $precio, $stock);
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        header("Location: ../Views/Admin/Productos.php");
        exit;
    }

    // Obtener imagen existente para conservar si no se cambia
    $existing = obtenerproduxID($conn, $id);
    $existingImagen = $existing['imagen'] ?? '';

    $imagen_tipo = $_POST['imagen_tipo'] ?? 'archivo';
    $uploadDir = __DIR__ . "/../Public/img/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if ($imagen_tipo === 'archivo' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $origName = $_FILES['imagen']['name'];
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = "Extensión de imagen no permitida.";
            header("Location: ../Views/Admin/Productos.php");
            exit;
        }
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $dest = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
            $imagen_final = '../../Public/img/uploads/' . $filename;
        } else {
            $_SESSION['error'] = "Error al mover la imagen subida.";
            header("Location: ../Views/Admin/Productos.php");
            exit;
        }
    } elseif ($imagen_tipo === 'url') {
        $url = trim($_POST['imagen_url_input'] ?? $_POST['imagen_final'] ?? '');
        if ($url) {
            $parsed = parse_url($url);
            if (!empty($parsed['host']) && strpos($parsed['host'], 'google') !== false && !empty($parsed['query'])) {
                parse_str($parsed['query'], $qs);
                if (!empty($qs['imgurl'])) $url = $qs['imgurl'];
            }
            if (strpos($url, 'data:image') === 0) {
                if (preg_match('/data:image\/(png|jpeg|jpg|gif|webp);base64,(.*)$/', $url, $m)) {
                    $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                    $data = base64_decode($m[2]);
                    $filename = time() . '_' . uniqid() . '.' . $ext;
                    $dest = $uploadDir . $filename;
                    if (file_put_contents($dest, $data) !== false) {
                        $imagen_final = '../../Public/img/uploads/' . $filename;
                    }
                }
            } else {
                $lower = strtolower($url);
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)(\?|$)/', $lower)) {
                    $imagen_final = $url;
                } else {
                    if (@file_get_contents($url)) {
                        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $ext = $ext ? $ext : 'jpg';
                        $filename = time() . '_' . uniqid() . '.' . $ext;
                        $dest = $uploadDir . $filename;
                        $content = @file_get_contents($url);
                        if ($content !== false && file_put_contents($dest, $content) !== false) {
                            $imagen_final = '../../Public/img/uploads/' . $filename;
                        } else {
                            $imagen_final = $url;
                        }
                    } else {
                        $imagen_final = $url;
                    }
                }
            }
        }
    }

    // Si no se setea imagen_final, conservar la existente
    if (empty($imagen_final)) {
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
        $_SESSION['error'] = "Error al eliminar el producto: " . $conn->error;
    }
    header("Location: ../Views/Admin/Productos.php");
    exit;
}
?>

