<?php
session_start();

// Redirigir si ya hay admin logueado
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === "admin") {
    header("Location: Dashboard.php");
    exit;
}

// Capturar error o mensaje
$error = isset($_GET['error']) ? $_GET['error'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrativo</title>
</head>
<body>

<form action="../../Controllers/usuariocontroller.php" method="POST">
    <H2>LOGIN ADMIN </H2>
    <input type="hidden" name="action" value="login_usuario">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Iniciar sesión</button>
</form>

<?php if($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if($msg): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

