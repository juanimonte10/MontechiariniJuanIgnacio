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

<form action="../../Controllers/usuariocontroller.php" method="POST">
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

