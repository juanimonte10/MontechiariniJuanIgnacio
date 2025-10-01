<?php
require_once __DIR__."/../config/db.php";
require_once  __DIR__."/../App/helpers/Funciones.php";
// LOGIN USUARIO (cliente o admin)

if (isset($_POST['action']) && $_POST['action'] == "login_usuario") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $usuario = loginusuarios($conn, $email, $password);

    if ($usuario) {
        session_start();

        if ($usuario['rol'] === "admin") {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../Views/Admin/Dashboard.php");
        } else {
            // Crear sesión del cliente con id consistente
            session_regenerate_id(true);
            $_SESSION['cliente'] = [
                "id" => $usuario['id_usuario'],
                "nombre" => $usuario['nombre'],
                "rol" => $usuario['rol']
            ];
            // Redirigir a la página principal relativa al proyecto
            header("Location: ../Index.php");
        }
        exit;
    } else {
        header("Location: ../Views/clientes/Login.php?error= Datos incorrectos");
        exit;
    }
}


// REGISTRO DE CLIENTE

if (isset($_POST['action']) && $_POST['action'] == "registro_usuario") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    registrarcliente($conn, $nombre, $email, $password);

    header("Location: ../Views/clientes/Login.php?msg=Registro exitoso");
    exit;
}


// REGISTRO DE ADMIN (solo admin puede crear otro admin)

if (isset($_POST['action']) && $_POST['action'] == "registro_admin") {
    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== "admin") {
        header("Location: ../Views/Login.php?error=No tienes permisos");
        exit;
    }

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    registraradmin($conn, $nombre, $email, $password);

    header("Location: ../Views/Admin/Dashboard.php?msg=Admin registrado exitosamente");
    exit;
}
?>

