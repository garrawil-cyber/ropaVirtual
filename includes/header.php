<?php
/**
 * HEADER - Boutique Sixta
 * IMPORTANTE: $items_carrito debe estar definido ANTES de incluir este archivo
 */

// Validar que $items_carrito esté definido
if (!isset($items_carrito)) {
    $items_carrito = 0;
    if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $items_carrito += isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Moda y Estilo</title>
    <link rel="stylesheet" href="<?= APP_URL ?>css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>css/header.css">
    
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?= APP_URL ?>index.php" class="logo">
                <span class="logo-icon">👗</span>
                <span class="logo-text">Boutique Sixta</span>
            </a>
            
            <!-- Botón Menú Móvil -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Buscador -->
            <div class="search-container">
                <form class="search-form" method="GET" action="<?= APP_URL ?>index.php">
                    <input type="search" 
                           name="buscar" 
                           placeholder="Buscar productos..." 
                           value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>"
                           class="search-input"
                           aria-label="Buscar productos">
                    <button type="submit" class="search-btn" aria-label="Buscar">🔍</button>
                </form>
            </div>
            
            <!-- Acciones -->
            <div class="header-actions">
                <a href="<?= APP_URL ?>cuenta.php" class="header-link" title="Mi cuenta">
                    <span class="header-icon">👤</span>
                    <span>Cuenta</span>
                </a>
                
                <!-- BOTÓN DEL CARRITO -->
                <button id="cartToggle" class="header-link" title="Mi carrito (<?= $items_carrito ?> items)" type="button">
                    <span class="cart-badge-container">
                        <span class="header-icon">🛒</span>
                        <?php if ($items_carrito > 0): ?>
                            <span class="cart-badge" id="cartBadge"><?= $items_carrito ?></span>
                        <?php endif; ?>
                    </span>
                    <span>Carrito</span>
                </button>
            </div>
        </div>
        
        <!-- Barra de Información -->
        <div class="info-bar">
            <div class="info-container">
                <span class="info-item">🚚 Envío gratis en compras mayores a S/ 100</span>
                <span class="info-item">💳 Paga seguro con cualquier método</span>
                <span class="info-item">↩️ 30 días para devoluciones</span>
            </div>
        </div>
    </header>

    <!-- Menú Móvil de Categorías -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="mobile-categories" id="mobileCategories">
        <div class="mobile-categories-header">
            <h3>Categorías</h3>
            <button class="close-mobile-menu" id="closeMobileMenu" aria-label="Cerrar menú">✕</button>
        </div>
        <div class="mobile-categories-list">
            <a href="<?= APP_URL ?>index.php" class="mobile-category-link">🛍️ Todos los productos</a>
            <?php if (isset($categorias) && !empty($categorias)): ?>
                <?php foreach ($categorias as $cat): ?>
                    <a href="<?= APP_URL ?>index.php?categoria=<?= $cat['id'] ?>" class="mobile-category-link">
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <main class="main-content">