<?php
session_start();
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../App/helpers/Funciones.php";

if (!isset($_SESSION["cliente"])) {
    header("Location: login.php");
    exit;
}

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
        $producto = obtenerproduxID($conn, $id);
        if ($cantidad > $producto['stock']) {
            $_SESSION['error'] = "La cantidad de '{$producto['nombre']}' supera el stock disponible ({$producto['stock']})";
            $cantidad = $producto['stock'];
        }
        $_SESSION['carrito'][$id] = $cantidad;
    }
    header("Location: Carrito.php");
    exit;
}

$carrito = $_SESSION["carrito"] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Dos Hermanas</title>
    <link rel="stylesheet" href="../../Public/css/carrito.css">
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h2>Carrito de Compras</h2>
            <a href="../../Index.php" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Volver al catálogo
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php endif; ?>

        <?php if (empty($carrito)): ?>
            <div class="cart-empty">
                <p>Tu carrito está vacío</p>
                <a href="../../Index.php" class="back-btn">Ir a comprar</a>
            </div>
        <?php else: ?>
            <form method="post" id="formCarrito">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($carrito as $id_producto => $cantidad):
                            $producto = obtenerproduxID($conn, $id_producto);
                            $subtotal = $producto["precio"] * $cantidad;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>
                                <input type="number" 
                                       name="cantidades[<?= $id_producto ?>]" 
                                       value="<?= $cantidad ?>" 
                                       min="1" 
                                       max="<?= $producto['stock'] ?>" 
                                       class="quantity-input"
                                       oninput="validarStock(this, <?= $producto['stock'] ?>)">
                                <span class="stock-warning">¡Stock máximo: <?= $producto['stock'] ?>!</span>
                            </td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <button type="button" class="remove-btn" onclick="if(confirm('¿Eliminar este producto?')) window.location.href='Carrito.php?eliminar=<?= $id_producto ?>'">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
                                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="cart-total">
                                Total: $<?= number_format($total, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="cart-actions">
                    <button type="submit" name="actualizar" class="btn btn-update">
                        Actualizar cantidades
                    </button>
                </div>
            </form>

            <form method="post" action="../../controllers/pedidocontroller.php" class="cart-actions">
                <input type="hidden" name="action" value="crear_pedido">
                <button type="submit" class="btn btn-checkout">
                    Finalizar Compra
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    function validarStock(input, stockMax) {
        const warning = input.nextElementSibling;
        if (parseInt(input.value) > stockMax) {
            input.value = stockMax;
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    }
    </script>
</body>
</html>
