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
if (!$producto) {
    // Producto no encontrado
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Producto no encontrado</title>
        <link rel="stylesheet" href="../../Public/css/index.css">
    </head>
    <body>
        <div style="max-width:900px;margin:40px auto;padding:20px;text-align:center;">
            <h2>Producto no encontrado</h2>
            <p>El producto que intentas ver no existe o fue eliminado.</p>
            <p><a href="../../Index.php">Volver al catálogo</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

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
        // mostrar header simple (index.php ya tiene header, aquí se reutiliza parcialmente)
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
                    <div class="product-stock">Stock: <?= $producto['stock'] ?> <?php if ($producto['stock'] == 0): ?><strong style="color:var(--danger)"> (Sin stock)</strong><?php endif; ?></div>
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
    <script>
    (function(){
        const form = document.getElementById('addToCartForm');
        const qty = document.getElementById('qtyInput');
        const max = parseInt(qty.getAttribute('max') || '0', 10);
        form.addEventListener('submit', function(e){
            const v = parseInt(qty.value || '0', 10);
            if (isNaN(v) || v < 1) {
                e.preventDefault();
                alert('Ingresa una cantidad válida');
                return;
            }
            if (max <= 0) {
                e.preventDefault();
                alert('Ya no hay stock disponible de este producto');
                return;
            }
            if (v > max) {
                e.preventDefault();
                alert('No puedes agregar más de ' + max + ' unidad(es).');
                return;
            }
            // otherwise allow submit; server will also validate
        });
    })();
    </script>
</body>
</html>
