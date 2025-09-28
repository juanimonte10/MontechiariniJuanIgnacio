<?php
session_start();
require_once __DIR__ ."/../../config/db.php";

$mensaje = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST["nombre"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $rol      = "cliente"; // rol por defecto

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        
        // 1. Verificar si el correo ya está registrado
        $checkSql = "SELECT id FROM usuarios WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $mensaje = "El correo ya está registrado. <br> <a href='login.php'>Iniciar sesión</a>";
        } else {
            // 2. Registrar  un usuario nuevo
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $email, $passwordHash, $rol);

            if ($stmt->execute()) {
                $mensaje = "Usuario registrado con éxito. <br> <a href='login.php'>Iniciar sesión</a>";
            } else {
                $mensaje = "Error al registrar: " . $conn->error;
            }
        }

    } else {
        $mensaje = "Por favor completar el formulario";
    }
}
?>

<form method="post">
    <input type="text" name="nombre" placeholder="Ingrese un nombre" required>
    <input type="email" name="email" placeholder="Ingrese un correo" required>
    <input type="password" name="password" placeholder="Ingrese una contraseña" required>
    <button type="submit">Registrarse</button>
</form>

<?php if (!empty($mensaje)) echo "<p>$mensaje</p>"; ?>
