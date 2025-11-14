<?php
session_start();
require_once "../../config/db.php";
require_once "../../App/helpers/Funciones.php";

// Imagen por defecto en base64 (un ícono simple de imagen)
define('DEFAULT_IMAGE_PATH', '../../Public/img/placeholder.png'); // Ruta a la imagen por defecto
define('DEFAULT_IMAGE', 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2NjYyIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxyZWN0IHg9IjMiIHk9IjMiIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgcng9IjIiIHJ5PSIyIj48L3JlY3Q+PGNpcmNsZSBjeD0iOC41IiBjeT0iOC41IiByPSIxLjUiPjwvY2lyY2xlPjxwb2x5bGluZSBwb2ludHM9IjIxIDE1IDEzLjUgOC41IDUgMjEiPjwvcG9seWxpbmU+PC9zdmc+');

// Verificar rol admin
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: Loginadm.php?error=No tienes permisos");
    exit;
}

// Capturar mensajes de sesión
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

// Manejar edición (cargar datos del producto a editar)
$editarproducto = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $editarproducto = obtenerproduxID($conn, $id);
}

// Obtener todos los productos
$productos = obtenerproductos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../../Public/css/Registro.css">
</head>
<body>

<h1>Gestión de Productos</h1>

<?php if ($msg): ?>
    <p class="msg"><?= $msg ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<!-- Formulario Agregar o Editar Producto -->
<h2><?= $editarproducto ? "Editar Producto" : "Agregar Producto" ?></h2>
<form action="../../controllers/Productocontroller.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?= $editarproducto ? "editar" : "agregar" ?>">
    <?php if ($editarproducto): ?>
        <input type="hidden" name="id_producto" value="<?= $editarproducto['id_producto'] ?>">
    <?php endif; ?>
    <input type="text" name="nombre" placeholder="Nombre" value="<?= $editarproducto['nombre'] ?? '' ?>" required>
    <input type="text" name="descripcion" placeholder="Descripción" value="<?= $editarproducto['descripcion'] ?? '' ?>">
    <input type="number" step="0.01" name="precio" placeholder="Precio" value="<?= $editarproducto['precio'] ?? '' ?>" required>
    <input type="number" name="stock" placeholder="Stock" value="<?= $editarproducto['stock'] ?? '' ?>" required>
    <div class="image-upload-container">
        <!-- <label for="imagen">Imagen del Producto:</label> -->

        <div class="option-container">

                <label for="imagen_url">Usar URL de imagen</label>
                <input type="url" name="imagen_url_input" id="imagen_url_input" placeholder="Pega la URL de la imagen aquí" class="url-input" >
            </div>
        </div>

        <div class="preview-container" style="display:none;">
            <img id="preview" src="#" alt="Vista previa">
            <div class="preview-info">
                <span id="imagen-info"></span>
                <button type="button" id="removeImage">Eliminar imagen</button>
            </div>
        </div>
        <input type="hidden" name="imagen_final" id="imagen_final" value="<?= $editarproducto['imagen'] ?? '' ?>">
    </div>
    <button type="submit"><?= $editarproducto ? "Actualizar Producto" : "Agregar Producto" ?></button>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('imagen');
        const urlInput = document.getElementById('imagen_url_input');
        const preview = document.getElementById('preview');
        const previewContainer = document.querySelector('.preview-container');
        const removeButton = document.getElementById('removeImage');
        const imagenFinal = document.getElementById('imagen_final');
        const imagenInfo = document.getElementById('imagen-info');
        
        // Manejar URL
        urlInput.addEventListener('input', function() {
            if (this.value && isValidUrl(this.value)) {
                const processed = processGoogleImageUrl(this.value);
                preview.src = processed;
                imagenFinal.value = processed;
                previewContainer.style.display = 'block';
                mostrarInfoImagen(processed, 'url');
            }
        });

        // Función para mostrar información de la imagen
        function mostrarInfoImagen(src, tipo) {
            let info = '';
            if (tipo === 'archivo') {
                info = 'Imagen subida desde el dispositivo';
            } else if (tipo === 'url') {
                info = 'Imagen desde URL: ' + src;
            }
            imagenInfo.textContent = info;
        }

        // Validar URL
        function isValidUrl(string) {
            try {
                const url = new URL(string);
                // Verificar si la URL termina en una extensión de imagen común
                const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                const hasImageExtension = imageExtensions.some(ext => 
                    url.pathname.toLowerCase().endsWith(ext)
                );
                return hasImageExtension;
            } catch (_) {
                return false;
            }
        }

        // Validar URL de Google Images
        function processGoogleImageUrl(url) {
            try {
                const urlObj = new URL(url);
                if (urlObj.hostname === 'www.google.com' && urlObj.pathname === '/imgres') {
                    const imgUrl = urlObj.searchParams.get('imgurl');
                    if (imgUrl) return imgUrl;
                }
                return url;
            } catch (_) {
                return url;
            }
        }

        // Mostrar imagen existente si hay una
        if (imagenFinal.value) {
            const processed = processGoogleImageUrl(imagenFinal.value);
            preview.src = processed;
            previewContainer.style.display = 'block';
            // Rellenar el input de la URL si la imagen existente es una URL válida
            if (!imagenFinal.value.startsWith('data:')) {
                urlInput.value = imagenFinal.value;
            }
            mostrarInfoImagen(processed, imagenFinal.value.startsWith('data:') ? 'archivo' : 'url');
        }

        // Eliminar imagen
        removeButton.addEventListener('click', function() {

            urlInput.value = ''; // Limpiar el input de la URL
            imagenFinal.value = 'DELETE'; // Enviar señal al backend para usar imagen por defecto
            preview.src = '<?= DEFAULT_IMAGE_PATH ?>'; // Mostrar imagen por defecto en la vista previa
            previewContainer.style.display = 'block';
            mostrarInfoImagen('<?= DEFAULT_IMAGE_PATH ?>', 'url');
        });
    });
    </script>
    <?php if ($editarproducto): ?>
        <a href="Productos.php">Cancelar</a>

    <?php endif; ?>
</form>

<hr>

<h2>Lista de Productos</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($productos as $p): ?>
    <tr>
        <td><?= $p["id_producto"] ?></td>
        <td><?= htmlspecialchars($p["nombre"]) ?></td>
        <td><?= htmlspecialchars($p["descripcion"]) ?></td>
        <td>$<?= number_format($p["precio"], 2) ?></td>
        <td>
            <?= $p["stock"] ?>
            <?php if ($p["stock"] == 0): ?>
                <span style="color:red; font-weight:bold;"> <br> No disponible</span>
            <?php endif; ?>
        </td>
        <td class="imagen-producto">
            <div class="imagen-container">
                <img src="<?= !empty($p["imagen"]) ? htmlspecialchars($p["imagen"]) : DEFAULT_IMAGE ?>" 
                     alt="<?= htmlspecialchars($p["nombre"]) ?>"
                     onerror="this.src=DEFAULT_IMAGE; this.onerror=null;">
            </div>
        </td>
        <td class="acciones">
            <a href="Productos.php?editar=<?= $p["id_producto"] ?>" class="btn-accion btn-editar">Editar</a>
            <a href="../../controllers/productocontroller.php?action=eliminar&id_producto=<?= $p["id_producto"] ?>" 
               onclick="return confirm('¿Eliminar este producto?')" 
               class="btn-accion btn-eliminar">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<form action="Loginadm.php" method="get" style="display:inline-block; margin:10px;">
    <button type="submit">Volver al Login </button>
</form>
<form action="../../logout.php" method="get" style="display:inline-block; margin:10px;">
    <button type="submit">Cerrar sesión</button>
</form>

</body>
</html>
