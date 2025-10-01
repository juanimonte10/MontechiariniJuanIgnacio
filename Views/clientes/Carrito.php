<?php
session_start();
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../App/helpers/Funciones.php";

if (!isset($_SESSION["cliente"])) {
    header("Location: login.php");
    exit;
}


$carrito = $_SESSION["carrito"] ?? [];
echo "<h2>Carrito de Compras</h2>";
echo "<a href='../../Index.php'>Volver al catálogo</a><hr>";

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    unset($_SESSION['carrito'][$id_eliminar]);
    header("Location: Carrito.php");
    exit;
}

// Actualizar cantidades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    foreach ($_POST['cantidades'] as $id => $cantidad) {
        $id = intval($id);
        $cantidad = max(1, intval($cantidad));
        $_SESSION['carrito'][$id] = $cantidad;
    }
    header("Location: Carrito.php");
    exit;
}

// La finalización del pedido la maneja el controlador 'pedidocontroller.php'
// (habrá un formulario separado que envíe action=crear_pedido)

if (empty($carrito)) {
    echo "<p>Tu carrito está vacío.</p>";
    exit;
}

// Mostrar carrito con formulario para actualizar/eliminar
$total = 0;
echo '<form method="post">';
echo '<table border="1" cellpadding="5"><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th><th>Acción</th></tr>';
foreach ($carrito as $id_producto => $cantidad) {
    $producto = obtenerproduxID($conn, $id_producto);
    $subtotal = $producto["precio"] * $cantidad;
    $total += $subtotal;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
    echo "<td><input type='number' name='cantidades[$id_producto]' value='$cantidad' min='1' style='width:50px;'></td>";
    echo "<td>$" . number_format($producto['precio'], 2) . "</td>";
    echo "<td>$" . number_format($subtotal, 2) . "</td>";
    echo "<td><a href='Carrito.php?eliminar=$id_producto' onclick=\"return confirm('¿Eliminar este producto?')\">Eliminar</a></td>";
    echo "</tr>";
}
echo "<tr><td colspan='3'><b>Total</b></td><td colspan='2'><b>$" . number_format($total, 2) . "</b></td></tr>";
echo "</table>";
echo '<button type="submit" name="actualizar">Actualizar cantidades</button> ';
echo '</form>';

// Formulario separado para finalizar compra (envía al controlador)
echo '<form method="post" action="../../controllers/pedidocontroller.php">';
echo '<input type="hidden" name="action" value="crear_pedido">';
echo '<button type="submit">Finalizar Compra</button>';
echo '</form>';
