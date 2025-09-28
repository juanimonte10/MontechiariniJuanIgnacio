<?php
session_start();
require_once "/../../config/db.php";

$mensaje= "";

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
                    header("Location: catalogo.php");
                    exit();
                } else {
                    $mensaje = "Contraseña incorrecta, vuelva a intentar.";
                }
            } else {
                $mensaje= "Usuario no encontrado, vuelva a intentar.";
            }

            $stmt->close();
        } else {
            $mensaje = "Error en la consulta SQL: " . $conn->mensaje;
        }
    } else {
        $mensaje = "Completa el formulario";
    }
}
?>
<h2> Login </h2>
<form  method="post">
    <input type="email" name="email" placeholder="ingrese un correo" required>
    <input type="password" name="password" placeholder="ingrese una constraseña" required>
    <button type="submit"> Inicia sesion </butotn>
</form>
