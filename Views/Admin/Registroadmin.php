<?php
session_start();

// Solo admin puede registrar otro admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== "admin") {
    header("Location: ../Views/Login.php?error=No tienes permisos");
    exit;
}

// Capturar mensajes
$error = isset($_GET['error']) ? $_GET['error'] : '';
$msg   = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<h2>Registrar nuevo Admin</h2>

<form action="../../controllers/usuariocontroller.php" method="POST">
    <input type="hidden" name="action" value="registro_admin">
    <input type="text" name="nombre" placeholder="Nombre del admin" required>
    <input type="email" name="email" placeholder="Correo del admin" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Registrar Adminstrador </button>
</form>

<?php if($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if($msg): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>
