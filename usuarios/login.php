<?php
session_start();
require_once("db.php");
; // conexión a la BD

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Verificar que los campos no estén vacíos
    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Verificamos contraseña encriptada
                if (password_verify($password, $row["password"])) {
                    $_SESSION["usuario"] = $row["nombre"];
                    $_SESSION["rol"]     = $row["rol"]; // opcional: guardar rol
                    header("Location: inicio.php");
                    exit();
                } else {
                    $error = "Contraseña incorrecta, vuelva a intentar.";
                }
            } else {
                $error = "Usuario no encontrado, vuelva a intentar.";
            }

            $stmt->close();
        } else {
            $error = "Error en la consulta SQL: " . $conn->error;
        }
    } else {
        $error = "Completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <h2>Iniciar sesión</h2>
  
  <?php if (!empty($error)) : ?>
    <p style="color:red; text-align:center;"><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="POST" action="login.php">
      <input type="email" name="email" placeholder="Ingrese un correo" required>
      <br>
      <input type="password" name="password" placeholder="Ingrese una contraseña" required>
      <br>
      <button type="submit">Ingresar</button>
  </form>
</body>
</html>
