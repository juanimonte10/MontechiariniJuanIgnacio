<?php
require_once "../../config/db.php";
require_once "../../App/helpers/Funciones.php";
session_start();

// Solo admin puede registrar otro admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== "admin") {
    // Redirigir al login admin (ruta relativa dentro de Views/Admin)
    header("Location: Loginadm.php?error=No tienes permisos");
    exit;
}

// Capturar mensajes
$error = isset($_GET['error']) ? $_GET['error'] : '';
$msg   = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<style>
    <?php include "../../Public/css/registroAlt.css"; ?>
</style>
<h2>Registrar nuevo Admin</h2>

<form action="../../controllers/Usuariocontroller.php" method="POST">
    <input type="hidden" name="action" value="registro_admin">
    <input type="text" name="nombre" placeholder="Nombre del admin" required>
    <input type="email" name="email" placeholder="Correo del admin" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Registrar Administrador</button>
    
</form>
<!-- Enlace alternativo por si no funciona JS -->
<p><a href="Loginadm.php">Volver al login</a></p>

<?php if($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if($msg): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>
