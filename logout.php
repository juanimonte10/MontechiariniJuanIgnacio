<?php
session_start();

// Verificar si hay sesión de cliente
if (isset($_SESSION['cliente'])) {
    unset($_SESSION['cliente']);
}

// Verificar si hay sesión de admin
if (isset($_SESSION['usuario'])) {
    unset($_SESSION['usuario']);
}


session_destroy();

// Redirigir al login de clientes (o donde quieras que empiece la página)
header("Location: Views/clientes/Login.php?msg= Sesión cerrada exitosamente ");
exit;
?>
