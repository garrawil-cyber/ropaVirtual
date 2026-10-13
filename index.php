<?php
/**
 * BOUTIQUE SIXTA COVEÑAS
 * Archivo principal de la aplicación
 * Controlador: index.php
 * Versión 2.0 - Corregido contador del carrito
 */

// Iniciar sesión
session_start();

// Incluir archivos necesarios
require_once 'config/app.php';
require_once 'config/database.php';

// Cargar modelos primero
require_once MODELS_DIR . 'CategoriaModel.php';
require_once MODELS_DIR . 'ProductoModel.php';

// Luego cargar funciones
require_once 'includes/funciones.php';

// Procesar acciones del carrito (agregar, actualizar, eliminar)
procesarCarrito();

// ============================================
// OBTENER PARÁMETROS DE LA URL
// ============================================
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// ============================================
// OBTENER DATOS DESDE LA BASE DE DATOS
// ============================================
$categorias = obtenerCategorias($db);
$productos = obtenerProductos($db, $categoria_id, $busqueda);

// ============================================
// CALCULAR TOTALES DEL CARRITO - VERSIÓN MEJORADA
// ============================================
$total_carrito = 0;
$items_carrito = 0;

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Calcular total
    $total_carrito = calcularTotalCarrito($db, $_SESSION['carrito']);
    
    // Calcular items de forma robusta
    foreach ($_SESSION['carrito'] as $item) {
        if (isset($item['cantidad']) && is_numeric($item['cantidad'])) {
            $items_carrito += (int)$item['cantidad'];
        }
    }
}


// ============================================
// INCLUIR LA VISTA (HEADER)
// ============================================
include INCLUDES_DIR . 'header.php';
include TEMPLATES_DIR . 'menu_superior.php'; 
$imagen_logo=IMG_URL . 'sixta_logo.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique Sixta Coveñas - Estilo y Elegancia</title>
    <link rel="stylesheet" href="<?= APP_URL ?>css/index.css">
    
</head>
<body>
    <!-- ============================================
         HERO SECTION - Rediseñado
         ============================================ -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Boutique Sixta Coveñas</h1>
                    <p class="hero-subtitle">Descubre la elegancia y el estilo en cada prenda. Moda sofisticada para mujeres con personalidad.</p>
                    <a href="#products" class="hero-btn">Explorar Colección</a>
                </div>
                <div class="hero-image">
                    <img src="<?= $imagen_logo ?>" alt="Boutique Sixta Coveñas" class="item-img-modern">
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         CONTENEDOR PRINCIPAL
         ============================================ -->
    <div class="container">
        <!-- Mostrar mensajes del carrito (éxito, error, info) -->
        <?php mostrarMensajesCarrito(); ?>
        
       
        <!-- ============================================
             SECCIÓN DE PRODUCTOS
             ============================================ -->
        <section class="products-section" id="products">
            
            <!-- Información de filtros activos -->
            <?php if ($categoria_id || $busqueda): ?>
                <div class="filter-info">
                    <div>
                        <?php if ($busqueda): ?>
                            <p>Resultados para: <strong>"<?= htmlspecialchars($busqueda) ?>"</strong></p>
                        <?php endif; ?>
                        
                        <?php if ($categoria_id): ?>
                            <?php 
                            // Obtener nombre de la categoría seleccionada
                            $cat_nombre = '';
                            foreach ($categorias as $cat) {
                                if ($cat['id'] == $categoria_id) {
                                    $cat_nombre = $cat['nombre'];
                                    break;
                                }
                            }
                            ?>
                            <p>Categoría: <strong><?= htmlspecialchars($cat_nombre) ?></strong></p>
                        <?php endif; ?>
                    </div>
                    <a href="index.php" class="clear-filters">✕ Limpiar filtros</a>
                </div>
            <?php else: ?>
                <h2 class="section-title">Nuestros Productos Destacados</h2>
            <?php endif; ?>
            
            <!-- Grid de productos -->
            <?php include TEMPLATES_DIR . 'productos_grid.php'; ?>
            
        </section>
        
        <!-- ============================================
             SECCIÓN DE BENEFICIOS
             ============================================ -->
        <section class="benefits-section" id="beneficios">
            <h2 class="section-title">¿Por Qué Elegirnos?</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <span class="benefit-icon">🎁</span>
                    <h3>Ofertas Especiales</h3>
                    <p>Descuentos increíbles en productos seleccionados</p>
                </div>
                <div class="benefit-card">
                    <span class="benefit-icon">🔄</span>
                    <h3>Devoluciones Fáciles</h3>
                    <p>30 días para cambios y devoluciones sin complicaciones</p>
                </div>
                <div class="benefit-card">
                    <span class="benefit-icon">👍</span>
                    <h3>Calidad Garantizada</h3>
                    <p>Productos de la más alta calidad y estilo</p>
                </div>
            </div>
        </section>
        
        <!-- ============================================
             ESTADÍSTICAS DE LA TIENDA
             ============================================ -->
        <?php if (!$categoria_id && !$busqueda): ?>
        <section class="stats-section" id="estadisticas">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?= count($productos) ?>+</div>
                    <p>Productos Disponibles</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <p>Clientes Satisfechos</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <p>Garantía de Calidad</p>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
    </div>

    <!-- ============================================
         MODAL DEL CARRITO
         ============================================ -->
    <?php include TEMPLATES_DIR . 'carrito_modal.php'; ?>

    <!-- ============================================
         INCLUIR FOOTER
         ============================================ -->
    <?php include INCLUDES_DIR . 'footer.php'; ?>
</body>
</html>