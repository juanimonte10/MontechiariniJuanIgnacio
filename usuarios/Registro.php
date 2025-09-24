<?php
require_once("db.php");

$mensaje = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST["nombre"];
    $email    = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $rol      = "cliente"; // rol por defecto

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $password, $rol);

    if ($stmt->execute()) {
        $mensaje = "Usuario registrado con éxito";
    } else {
        $mensaje = "Error, volver a intentar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
  <link rel="stylesheet" href="registro.css">  
</head>
<body>
    <?php if ($mensaje): ?>
        <div class="mensaje">
            <p><?= $mensaje ?></p>
            <form action="login.php">
                <button type="submit">Ir al login</button>
            </form>
        </div>
    <?php else: ?>
        <H1> Registro de usuario</H1>
        <form method="POST" action="registro.php">
            <input type="text" name="nombre" placeholder="Ingrese nombre" required>
            <input type="email" name="email" placeholder="Ingrese un correo" required>
            <input type="password" name="password" placeholder="Ingrese una contraseña" required>
            <button type="submit">Continuar</button>
        </form>
    <?php endif; ?>
</body>
</html>
