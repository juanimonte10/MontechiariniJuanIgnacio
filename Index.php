

<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/App/helpers/Funciones.php";

// Si no hay usuario logueado (cliente o admin), redirigir al login cliente
if (!isset($_SESSION['cliente']) && !isset($_SESSION['usuario'])) {
    header("Location: Views/clientes/Login.php?error=Debes iniciar sesión");
    exit;
}

// Obtener todos los productos
$productos = obtenerproductos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="Public/css/index.css">
    <link rel="stylesheet" href="Public/css/header.css">
</head>
<body>
    <?php
        $displayName = '';
        if (isset($_SESSION['cliente'])) {
            $displayName = htmlspecialchars($_SESSION['cliente']['nombre']);
        } elseif (isset($_SESSION['usuario'])) {
            $displayName = htmlspecialchars($_SESSION['usuario']['nombre']) . ' (Admin)';
        }
    ?>
    <div class="header">
        <div class="title">Dos Hermanas</div>
        <div class="right">
            <div class="user-menu">
                <div class="user-card">
                    <div class="avatar-svg">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="8" r="3.2" stroke="#666" stroke-width="1.2" fill="#fff" />
                            <path d="M4 20c0-3.3 2.7-6 6-6h4c3.3 0 6 2.7 6 6" stroke="#666" stroke-width="1.2" fill="#fff"/>
                        </svg>
                    </div>
                    <div class="user-name"><?= $displayName ?></div>
                </div>
                <div class="dropdown-menu">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="Views/Admin/dashboard.php" class="dropdown-item">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 13h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zm0 8h6c.55 0 1-.45 1-1v-4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1zm10 0h6c.55 0 1-.45 1-1v-8c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zM13 4v4c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1z" fill="currentColor"/>
                            </svg>
                            Panel de Admin
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" fill="currentColor"/>
                        </svg>
                        Cerrar Sesión
                    </a>
                </div>
            </div>

            <a href="Views/clientes/Carrito.php" class="cart-indicator" title="Ver carrito">
                <div class="cart-svg">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 6h15l-1.5 9h-11L6 6z" stroke="#333" stroke-width="1" fill="#fff"/>
                        <circle cx="10" cy="20" r="1" fill="#333" />
                        <circle cx="18" cy="20" r="1" fill="#333" />
                    </svg>
                </div>
                <div class="cart-count"><?= array_sum($_SESSION['carrito'] ?? []) ?></div>
            </a>
        </div>
    </div>

    <button class="sidebar-toggle" id="sidebarToggle">☰ Categorias</button>
    <div class="sidebar" id="sidebar">
        <h3>Buscar productos</h3>
        <div class="field">
            <label>Nombre</label>
            <input type="text" id="sideSearch" placeholder="Buscar por nombre...">
        </div>
        <div class="field">
            <label>Categoría (opcional)</label>
            <select id="sideCategory">
                <option value="">Todas</option>
                <option value="categoria1">Categoría 1</option>
                <option value="categoria2">Categoría 2</option>
            </select>
        </div>
        <div class="field">
            <button id="sideApply">Aplicar</button>
        </div>
    </div>

    <div class="container">
        <main class="main">
            <div class="search-row">
                <input type="search" id="globalSearch" placeholder="Buscar productos por nombre...">
                <button id="searchBtn">Buscar</button>
            </div>

            <h2 style="text-align:center;margin-top:0">Catálogo de Productos</h2>
            <div class="products-grid" id="productsGrid">
                <?php foreach ($productos as $p): ?>
                <div class="producto" data-name="<?= htmlspecialchars(strtolower($p['nombre'])) ?>">
                    <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                    <div style="height:8px"></div>
                    <?php if (!empty($p['imagen'])): ?>
                        <img src="<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" onerror="this.src='Public/img/no-image.png'">
                    <?php else: ?>
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0nMTYwJyBoZWlnaHQ9JzEyMCcgdmlld0JveD0nMCAwIDE2MCAxMjAnIHhtbG5zPSdodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Zyc+PHJlY3Qgd2lkdGg9JzE2MCcgaGVpZ2h0PScxMjAnIGZpbGw9JyNkZGRkZCcgcng9JzgnLz48L3N2Zz4=' alt="Sin imagen">
                    <?php endif; ?>
                    <p class="precio">$<?= number_format($p['precio'], 2) ?></p>
                    <p class="stock">Stock: <?= $p['stock'] ?> <?php if ($p['stock'] == 0): ?><span style="color:var(--danger); font-weight:bold;"> (Sin stock disponible)</span><?php endif; ?></p>
                    <div class="product-actions">
                        <form action="controllers/carrito_controller.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                            <input type="hidden" name="redirect" value="back">
                            <button class="btn btn-primary" type="submit" <?= ($p['stock'] == 0) ? 'disabled' : '' ?>>Agregar</button>
                        </form>
                        <a href="Views/clientes/Producto.php?id=<?= $p['id_producto'] ?>" class="btn btn-outline">Ver</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
    // Sidebar toggle and hover-open behavior
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    let hoverTimeout;
    // click to toggle (for touch / click users)
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    // open when mouse enters the toggle, close when mouse leaves (unless hovering sidebar)
    sidebarToggle.addEventListener('mouseenter', () => {
        clearTimeout(hoverTimeout);
        sidebar.classList.add('open');
        document.body.classList.add('sidebar-open');
    });
    sidebarToggle.addEventListener('mouseleave', () => {
        // give a small delay so user can move into the sidebar
        hoverTimeout = setTimeout(() => {
            if (!sidebar.matches(':hover')) {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
            }
        }, 250);
    });
    sidebar.addEventListener('mouseenter', ()=>{
        clearTimeout(hoverTimeout);
        sidebar.classList.add('open');
        document.body.classList.add('sidebar-open');
    });
    sidebar.addEventListener('mouseleave', ()=>{
        hoverTimeout = setTimeout(()=>{
            sidebar.classList.remove('open');
            document.body.classList.remove('sidebar-open');
        }, 200);
    });

    // Simple client-side search
    const productsGrid = document.getElementById('productsGrid');
    const products = Array.from(document.querySelectorAll('.producto'));
    function filterProducts(term){
        term = term.trim().toLowerCase();
        products.forEach(p => {
            const name = p.getAttribute('data-name') || '';
            if (!term || name.indexOf(term) !== -1) p.style.display = '';
            else p.style.display = 'none';
        });
    }
    document.getElementById('searchBtn').addEventListener('click', ()=>{
        const term = document.getElementById('globalSearch').value; filterProducts(term);
    });
    document.getElementById('globalSearch').addEventListener('keyup', (e)=>{ if(e.key === 'Enter') document.getElementById('searchBtn').click(); });

    // sidebar search
    document.getElementById('sideApply').addEventListener('click', ()=>{
        const term = document.getElementById('sideSearch').value; filterProducts(term); sidebar.classList.remove('open');
    });
    </script>
</body>
</html>
