<?php
session_start();
require_once __DIR__ . "/../../config/db.php";

// Si ya hay un cliente logueado, redirigir al catálogo
if (isset($_SESSION['cliente'])) {
    header("Location: /Index.php");
    exit;
}

// Capturar mensajes enviados por controller
$error = isset($_GET['error']) ? $_GET['error'] : '';
$msg   = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
   <link rel="stylesheet" href="../../Public/css/registroAlt.css">
</head>
<body>

<h2>Login </h2>

<form action="../../controllers/usuariocontroller.php" method="POST">
    <input type="hidden" name="action" value="login_usuario">
    <input type="email" name="email" placeholder="Ingrese su correo" required>
    <input type="password" name="password" placeholder="Ingrese su contraseña" required>
    <button type="submit">Iniciar sesión</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($msg): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>

</body>
</html>


