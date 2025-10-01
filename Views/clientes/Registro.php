<?php
session_start();

// Redirigir si ya hay usuario logueado
if (isset($_SESSION['usuario'])) {
    header("Location: /../../Index.php");
    exit;
}

// Capturar mensaje de error o éxito enviado por controller
$error = isset($_GET['error']) ? $_GET['error'] : '';
$msg   = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<h2>Registro de Cliente</h2>

<form action="../../controllers/usuariocontroller.php" method="POST">
    <input type="hidden" name="action" value="registro_usuario">
    <input type="text" name="nombre" placeholder="Ingrese un nombre" required>
    <input type="email" name="email" placeholder="Ingrese un correo" required>
    <input type="password" name="password" placeholder="Ingrese una contraseña" required>
    <button type="submit">Registrarse</button>
</form>

<?php if($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if($msg): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

