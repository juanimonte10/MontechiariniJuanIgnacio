<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../App/helpers/Funciones.php";

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
            header("Location: ../Index.php");
        }
        exit;
    } else {
        header("Location: ../Views/clientes/Login.php?error=Datos incorrectos");
        exit;
    }
}


// REGISTRO DE CLIENTE
if (isset($_POST['action']) && $_POST['action'] == "registro_usuario") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // 🔍 Verificar si ya existe el email antes de registrar
    $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Ya existe ese usuario → redirigimos con mensaje de error
        header("Location: ../Views/clientes/Registro.php?error=El usuario ya está registrado");
        exit;
    }

    // Si no existe → lo registramos normalmente
    registrarCliente($conn, $nombre, $email, $password);

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

    // 🔍 Verificar si el email ya existe también para admins
    $check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: ../Views/Admin/Dashboard.php?error=El usuario ya existe");
        exit;
    }

    registraradmin($conn, $nombre, $email, $password);

    header("Location: ../Views/Admin/Dashboard.php?msg=Admin registrado exitosamente");
    exit;
}
?>


