<?php
session_start();
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../App/helpers/Funciones.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: ../../Index.php');
    exit;
}
$producto = obtenerproduxID($conn, $id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= htmlspecialchars($producto['nombre']) ?> - Dos Hermanas</title>
    <link rel="stylesheet" href="../../Public/css/index.css">
    <link rel="stylesheet" href="../../Public/css/header.css">
    <link rel="stylesheet" href="../../Public/css/producto.css">
</head>
<body>
    <?php
        // se muestra el header del index
    ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="msg-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['msg'])): ?>
        <div class="msg-success">
            <?= htmlspecialchars($_SESSION['msg']) ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>
    <div class="container">
        <main class="main">
            <div class="product-detail">
                <div class="product-media">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" onerror="this.src='../../Public/img/no-image.png'">
                    <?php else: ?>
                        <img src="../../Public/img/no-image.png" alt="Sin imagen">
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
                    <div class="product-price">$<?= number_format($producto['precio'],2) ?></div>
                    <div class="product-stock">Stock: <?= $producto['stock'] ?> <?php if ($producto['stock'] == 0): ?><strong style="color:var(--danger)"> Sin stock</strong><?php endif; ?></div>
                    <div class="product-desc"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></div>

                    <div class="actions">
                        <form id="addToCartForm" action="../../controllers/carrito_controller.php" method="POST">
                            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                            <input id="qtyInput" type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" style="width:80px;padding:8px;border:1px solid #ddd;border-radius:6px">
                            <button id="addBtn" class="btn btn-primary" type="submit" <?= ($producto['stock']==0)?'disabled':'' ?>>Agregar al carrito</button>
                        </form>

                        <a href="../../Index.php" class="btn btn-outline">Volver al catálogo</a>
                    </div>
                </div>
            </div>
        </main>
    </div>


</body>
</html>
