<?php
session_start();
require_once "../../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = ? AND rol = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {
        $_SESSION["admin"] = $admin;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Datos incorrectos";
    }
}
?>

<h2> Login </h2>
<form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit"> Iniciar sesion </button>
</form>
