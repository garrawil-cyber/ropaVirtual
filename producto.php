<?php
/**
 * Página de detalle de producto - VERSIÓN PREMIUM
 * Ruta: producto.php
 */

// Incluir configuración y conexión
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funciones.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si se recibió un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ' . APP_URL . 'index.php');
    exit;
}

$producto_id = (int)$_GET['id'];

// Obtener información del producto usando PDO
$sql = "SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = :id AND p.activo = 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $producto_id, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header('Location: ' . APP_URL . 'index.php');
    exit;
}

// Calcular descuento
$precio = $producto['precio'];
$precio_oferta = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
               ? $producto['precio_oferta'] 
               : null;
$descuento = 0;

if ($precio_oferta) {
    $descuento = calcularDescuento($precio, $precio_oferta);
}

// Obtener URL de la imagen
$imagen_url = getImagenProducto($producto['imagen']);

// Obtener productos relacionados (misma categoría)
$sql_relacionados = "SELECT * FROM productos 
                     WHERE categoria_id = :cat_id AND id != :prod_id AND activo = 1 
                     LIMIT 4";
$stmt_rel = $db->prepare($sql_relacionados);
$stmt_rel->bindParam(':cat_id', $producto['categoria_id'], PDO::PARAM_INT);
$stmt_rel->bindParam(':prod_id', $producto_id, PDO::PARAM_INT);
$stmt_rel->execute();
$productos_relacionados = $stmt_rel->fetchAll(PDO::FETCH_ASSOC);

// Procesar agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_carrito'])) {
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    $talla = isset($_POST['talla']) ? sanitizar($_POST['talla']) : '';
    
    if ($cantidad > 0 && $cantidad <= $producto['stock']) {
        agregarAlCarrito($producto_id, $cantidad, $talla);
        $_SESSION['mensaje_exito'] = '¡Producto agregado al carrito!';
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $producto_id);
        exit;
    }
}

$page_title = $producto['nombre'];
include __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="<?= APP_URL ?>css/producto.css">

<div class="producto-detalle-premium">
    <!-- Breadcrumb Premium -->
    <nav class="breadcrumb-premium">
        <a href="<?= APP_URL ?>index.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            </svg>
            Inicio
        </a>
        <span class="separator">›</span>
        <a href="<?= APP_URL ?>index.php?categoria=<?= $producto['categoria_id'] ?>">
            <?= sanitizar($producto['categoria_nombre']) ?>
        </a>
        <span class="separator">›</span>
        <span class="current"><?= sanitizar($producto['nombre']) ?></span>
    </nav>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert-premium alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span><?= $_SESSION['mensaje_exito'] ?></span>
            <button onclick="this.parentElement.remove()" class="alert-close">×</button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>

    <!-- Contenido principal -->
    <div class="producto-content-premium">
        <!-- Galería Premium -->
        <div class="producto-gallery-premium">
            <div class="gallery-main-premium">
                <img id="mainImagePremium" src="<?= $imagen_url ?>" alt="<?= sanitizar($producto['nombre']) ?>" 
                     onerror="this.src='<?= IMG_DEFAULT ?>'">
                
                <!-- Badges Premium -->
                <div class="gallery-badges-premium">
                    <?php if ($descuento > 0): ?>
                        <span class="badge-premium badge-discount">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                            -<?= $descuento ?>%
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($producto['destacado'] == 1): ?>
                        <span class="badge-premium badge-featured">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            Destacado
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Indicador de zoom -->
                <div class="zoom-hint-premium">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    Click para ampliar
                </div>
            </div>
        </div>

        <!-- Información Premium -->
        <div class="producto-info-premium">
            <div class="info-header-premium">
                <span class="producto-categoria-premium"><?= sanitizar($producto['categoria_nombre']) ?></span>
                
                <div class="producto-actions-top">
                    <button class="btn-icon-premium" title="Agregar a favoritos">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                    <button class="btn-icon-premium" title="Compartir" onclick="compartirProducto()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"/>
                            <circle cx="6" cy="12" r="3"/>
                            <circle cx="18" cy="19" r="3"/>
                            <path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <h1 class="producto-titulo-premium"><?= sanitizar($producto['nombre']) ?></h1>
            
            <!-- Rating Premium -->
            <div class="producto-rating-premium">
                <div class="stars-premium">
                    <?php for($i = 0; $i < 5; $i++): ?>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <span class="rating-text-premium">4.8 (127 reseñas)</span>
            </div>

            <!-- Precio Premium -->
            <div class="producto-precio-premium">
                <?php if ($precio_oferta): ?>
                    <div class="precio-group">
                        <span class="precio-original-premium">S/ <?= number_format($precio, 2) ?></span>
                        <span class="precio-actual-premium">S/ <?= number_format($precio_oferta, 2) ?></span>
                    </div>
                    <span class="ahorro-premium">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                        </svg>
                        Ahorras S/ <?= number_format($precio - $precio_oferta, 2) ?>
                    </span>
                <?php else: ?>
                    <span class="precio-actual-premium">S/ <?= number_format($precio, 2) ?></span>
                <?php endif; ?>
            </div>

            <!-- Descripción Premium -->
            <div class="producto-descripcion-premium">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    Descripción
                </h3>
                <p><?= nl2br(sanitizar($producto['descripcion'])) ?></p>
            </div>

            <!-- Características Premium -->
            <div class="producto-caracteristicas-premium">
                <div class="caracteristica-premium">
                    <div class="caracteristica-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <div class="caracteristica-info">
                        <strong>Envío Gratis</strong>
                        <small>En compras mayores a S/ 100</small>
                    </div>
                </div>
                <div class="caracteristica-premium">
                    <div class="caracteristica-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="1 4 1 10 7 10"/>
                            <polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                    </div>
                    <div class="caracteristica-info">
                        <strong>30 días de devolución</strong>
                        <small>Sin preguntas</small>
                    </div>
                </div>
                <div class="caracteristica-premium">
                    <div class="caracteristica-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div class="caracteristica-info">
                        <strong>Pago Seguro</strong>
                        <small>Múltiples métodos</small>
                    </div>
                </div>
            </div>

            <!-- Formulario Premium -->
            <form method="POST" action="" class="producto-form-premium">
                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                
                <!-- Tallas Premium -->
                <?php if (!empty($producto['tallas'])): ?>
                    <div class="form-group-premium">
                        <label>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M9 3v18M15 3v18M3 9h18M3 15h18"/>
                            </svg>
                            Selecciona tu talla:
                        </label>
                        <div class="tallas-selector-premium">
                            <?php 
                            $tallas = explode(',', $producto['tallas']);
                            foreach ($tallas as $talla): 
                                $talla = trim($talla);
                            ?>
                                <label class="talla-option-premium">
                                    <input type="radio" name="talla" value="<?= $talla ?>" required>
                                    <span><?= $talla ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cantidad Premium -->
                <div class="form-group-premium">
                    <label>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="16"/>
                        </svg>
                        Cantidad:
                    </label>
                    <div class="cantidad-selector-premium">
                        <button type="button" onclick="decreaseQty()" class="qty-btn-premium">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <input type="number" name="cantidad" id="cantidad" value="1" 
                               min="1" max="<?= $producto['stock'] ?>" readonly>
                        <button type="button" onclick="increaseQty()" class="qty-btn-premium">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <span class="stock-info-premium">
                            <?php if ($producto['stock'] > 0): ?>
                                (<?= $producto['stock'] ?> disponibles)
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Stock Status Premium -->
                <div class="stock-status-premium <?= $producto['stock'] <= 0 ? 'out-stock' : ($producto['stock'] <= 5 ? 'low-stock' : 'in-stock') ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php if ($producto['stock'] > 5): ?>
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        <?php elseif ($producto['stock'] > 0): ?>
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        <?php else: ?>
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        <?php endif; ?>
                    </svg>
                    <span>
                        <?php if ($producto['stock'] <= 0): ?>
                            Producto agotado
                        <?php elseif ($producto['stock'] <= 5): ?>
                            ¡Solo quedan <?= $producto['stock'] ?> unidades!
                        <?php else: ?>
                            En stock - Listo para enviar
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Botones de Acción Premium -->
                <div class="action-buttons-premium">
                    <?php if ($producto['stock'] > 0): ?>
                        <button type="submit" name="agregar_carrito" class="btn-add-cart-premium">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Agregar al Carrito
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-out-stock-premium" disabled>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Producto Agotado
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Productos relacionados Premium -->
    <?php if (!empty($productos_relacionados)): ?>
        <section class="productos-relacionados-premium">
            <div class="relacionados-header">
                <h2>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    También te puede interesar
                </h2>
                <p>Descubre productos similares que complementan tu estilo</p>
            </div>
            <div class="relacionados-grid-premium">
                <?php foreach ($productos_relacionados as $rel): 
                    $rel_imagen = getImagenProducto($rel['imagen']);
                    $rel_precio_oferta = !empty($rel['precio_oferta']) && $rel['precio_oferta'] > 0 
                                       ? $rel['precio_oferta'] 
                                       : null;
                    $rel_descuento = $rel_precio_oferta ? calcularDescuento($rel['precio'], $rel_precio_oferta) : 0;
                ?>
                    <a href="<?= APP_URL ?>producto.php?id=<?= $rel['id'] ?>" class="producto-relacionado-premium">
                        <div class="relacionado-image">
                            <img src="<?= $rel_imagen ?>" alt="<?= sanitizar($rel['nombre']) ?>"
                                 onerror="this.src='<?= IMG_DEFAULT ?>'">
                            <?php if ($rel_descuento > 0): ?>
                                <span class="relacionado-badge">-<?= $rel_descuento ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="relacionado-info">
                            <h4><?= sanitizar($rel['nombre']) ?></h4>
                            <div class="relacionado-price">
                                <?php if ($rel_precio_oferta): ?>
                                    <span class="old-price">S/ <?= number_format($rel['precio'], 2) ?></span>
                                    <span class="current-price">S/ <?= number_format($rel_precio_oferta, 2) ?></span>
                                <?php else: ?>
                                    <span class="current-price">S/ <?= number_format($rel['precio'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
/* Se incluirán los estilos premium en el siguiente mensaje por límite de espacio */
</style>

<script>
// Control de cantidad
const maxStock = <?= $producto['stock'] ?>;

function increaseQty() {
    const input = document.getElementById('cantidad');
    const current = parseInt(input.value);
    if (current < maxStock) {
        input.value = current + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('cantidad');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

// Compartir producto
function compartirProducto() {
    if (navigator.share) {
        navigator.share({
            title: '<?= sanitizar($producto['nombre']) ?>',
            text: 'Mira este producto increíble',
            url: window.location.href
        });
    } else {
        copiarEnlace();
    }
}

function copiarEnlace() {
    navigator.clipboard.writeText(window.location.href)
        .then(() => alert('¡Enlace copiado al portapapeles!'))
        .catch(() => alert('No se pudo copiar el enlace'));
}

// Zoom en imagen
const galleryMain = document.querySelector('.gallery-main-premium');
const mainImage = document.getElementById('mainImagePremium');
let isZoomed = false;

if (galleryMain && mainImage) {
    galleryMain.addEventListener('click', function(e) {
        if (!isZoomed) {
            this.style.cursor = 'zoom-out';
            mainImage.style.transform = 'scale(2)';
            isZoomed = true;
            updateZoomPosition(e);
        } else {
            this.style.cursor = 'zoom-in';
            mainImage.style.transform = 'scale(1)';
            isZoomed = false;
        }
    });

    galleryMain.addEventListener('mousemove', function(e) {
        if (isZoomed) {
            updateZoomPosition(e);
        }
    });

    function updateZoomPosition(e) {
        const rect = galleryMain.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        mainImage.style.transformOrigin = `${x}% ${y}%`;
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>