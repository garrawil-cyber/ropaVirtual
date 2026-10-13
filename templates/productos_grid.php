<?php
/**
 * Grid de productos con modal de detalles
 * Ruta: templates/productos_grid.php
 */
?>

<div class="products-grid">
    <?php if (empty($productos)): ?>
        <div class="no-products">
            <div class="no-products-icon">📦</div>
            <h3>No se encontraron productos</h3>
            <p>Intenta con otros filtros o categorías</p>
            <a href="<?= APP_URL ?>index.php" class="btn-primary">Ver todos los productos</a>
        </div>
    <?php else: ?>
        <?php foreach ($productos as $producto): 
            // Calcular precio y descuento
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
        ?>
        
        <div class="product-card">
            <!-- Imagen del producto -->
            <div class="product-image">
                <a href="<?= APP_URL ?>producto.php?id=<?= $producto['id'] ?>">
                    <img src="<?= $imagen_url ?>" 
                         alt="<?= sanitizar($producto['nombre']) ?>"
                         loading="lazy"
                         onerror="this.src='<?= IMG_DEFAULT ?>'">
                </a>
                
                <!-- Badges -->
                <div class="product-badges">
                    <?php if ($descuento > 0): ?>
                        <span class="badge badge-discount">-<?= $descuento ?>%</span>
                    <?php endif; ?>
                    
                    <?php if ($producto['destacado'] == 1): ?>
                        <span class="badge badge-featured">⭐ Destacado</span>
                    <?php endif; ?>
                    
                    <?php if ($producto['stock'] <= 0): ?>
                        <span class="badge badge-out-stock">Agotado</span>
                    <?php elseif ($producto['stock'] <= 5): ?>
                        <span class="badge badge-low-stock">¡Últimas unidades!</span>
                    <?php endif; ?>
                </div>
                
                <!-- Acciones rápidas -->
                <div class="product-actions">
                    <button type="button" class="btn-quick-view" 
                            data-producto='<?= json_encode([
                                'id' => $producto['id'],
                                'nombre' => $producto['nombre'],
                                'descripcion' => $producto['descripcion'] ?? '',
                                'precio' => (float)$precio,
                                'precio_oferta' => $precio_oferta ? (float)$precio_oferta : null,
                                'descuento' => (int)$descuento,
                                'categoria' => $producto['categoria_nombre'],
                                'imagen' => $imagen_url,
                                'tallas' => $producto['tallas'] ?? '',
                                'stock' => (int)$producto['stock'],
                                'destacado' => (int)$producto['destacado']
                            ]) ?>'
                            title="Ver detalles">
                        👁️ Ver
                    </button>
                </div>
            </div>
            
            <!-- Información del producto -->
            <div class="product-info">
                <!-- Categoría -->
                <span class="product-category">
                    <?= sanitizar($producto['categoria_nombre']) ?>
                </span>
                
                <!-- Nombre -->
                <h3 class="product-title">
                    <a href="<?= APP_URL ?>producto.php?id=<?= $producto['id'] ?>">
                        <?= sanitizar($producto['nombre']) ?>
                    </a>
                </h3>
                
                <!-- Descripción corta -->
                <?php if (!empty($producto['descripcion'])): ?>
                    <p class="product-description">
                        <?= acortarTexto(sanitizar($producto['descripcion']), 60) ?>
                    </p>
                <?php endif; ?>
                
                <!-- Precio -->
                <div class="product-price">
                    <?php if ($precio_oferta): ?>
                        <span class="price-original"><?= formatearPrecio($precio) ?></span>
                        <span class="price-current"><?= formatearPrecio($precio_oferta) ?></span>
                    <?php else: ?>
                        <span class="price-current"><?= formatearPrecio($precio) ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Tallas disponibles (si existen) -->
                <?php if (!empty($producto['tallas'])): ?>
                    <div class="product-sizes">
                        <small>Tallas: <?= sanitizar($producto['tallas']) ?></small>
                    </div>
                <?php endif; ?>
                
                <!-- Botón agregar al carrito -->
                <?php if ($producto['stock'] > 0): ?>
                    <form method="POST" action="" class="add-to-cart-form">
                        <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" name="agregar_carrito" class="btn-add-cart">
                            🛒 Agregar
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn-out-stock" disabled>
                        Agotado
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal de Producto -->
<div id="productModal" class="modal-overlay">
    <div class="modal-container">
        <button class="modal-close" onclick="closeProductModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        
        <div class="modal-content">
            <!-- Imagen -->
            <div class="modal-image-container" id="imageContainer" onclick="toggleZoom(event)">
                <img id="modalImage" src="" alt="" class="modal-image">
                <div id="modalBadges" class="modal-badges"></div>
                <div class="zoom-indicator">🔍 Click para zoom</div>
            </div>
            
            <!-- Información -->
            <div class="modal-info">
                <span id="modalCategory" class="modal-category"></span>
                <h2 id="modalTitle" class="modal-title"></h2>
                <p id="modalDescription" class="modal-description"></p>
                
                <div id="modalPrice" class="modal-price"></div>
                
                <div id="modalSizes" class="modal-sizes" style="display: none;">
                    <label>Tallas disponibles:</label>
                    <div id="modalSizesContent"></div>
                </div>
                
                <div id="modalStock" class="modal-stock"></div>
                
                <div class="modal-actions">
                    <form method="POST" action="" id="modalCartForm">
                        <input type="hidden" name="producto_id" id="modalProductId">
                        <div class="quantity-selector">
                            <label>Cantidad:</label>
                            <div class="quantity-controls">
                                <button type="button" onclick="decreaseQuantity()" class="qty-btn">-</button>
                                <input type="number" name="cantidad" id="modalQuantity" value="1" min="1" max="10" readonly>
                                <button type="button" onclick="increaseQuantity()" class="qty-btn">+</button>
                            </div>
                        </div>
                        <button type="submit" name="agregar_carrito" id="modalAddCart" class="modal-btn-add">
                            🛒 Agregar al Carrito
                        </button>
                    </form>
                    <a id="modalViewFull" href="#" class="modal-btn-view">
                        Ver Página Completa →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= APP_URL ?>css/productos_grid.css">

<script>
// Variables globales
let currentProductData = null;
let isZoomed = false;

// Obtener APP_URL de forma segura con múltiples métodos de respaldo
let APP_URL = '<?= APP_URL ?>';

// Si APP_URL no está definida correctamente en PHP, usar respaldo
if (!APP_URL || APP_URL === '' || APP_URL === '<?= APP_URL ?>') {
    // Detectar la URL base desde la ubicación actual
    const pathArray = window.location.pathname.split('/');
    pathArray.pop(); // Remover el archivo actual
    APP_URL = window.location.origin + pathArray.join('/') + '/';
}

// Asegurar que termine en /
if (!APP_URL.endsWith('/')) {
    APP_URL += '/';
}

console.log('APP_URL configurada:', APP_URL); // Debug

// Inicializar event listeners cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners para botones de "Ver"
    document.querySelectorAll('.btn-quick-view').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productoData = JSON.parse(this.getAttribute('data-producto'));
            openProductModal(productoData);
        });
    });
    
    // Event listener para seguimiento del mouse en zoom
    const imageContainer = document.getElementById('imageContainer');
    if (imageContainer) {
        imageContainer.addEventListener('mousemove', function(event) {
            if (isZoomed) {
                updateZoomPosition(event);
            }
        });
    }
    
    // Cerrar modal con click fuera
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
    }
});

// Abrir modal
function openProductModal(producto) {
    currentProductData = producto;
    const modal = document.getElementById('productModal');
    
    // Establecer contenido
    document.getElementById('modalImage').src = producto.imagen;
    document.getElementById('modalImage').alt = producto.nombre;
    document.getElementById('modalCategory').textContent = producto.categoria;
    document.getElementById('modalTitle').textContent = producto.nombre;
    document.getElementById('modalDescription').textContent = producto.descripcion || 'Sin descripción disponible';
    document.getElementById('modalProductId').value = producto.id;
    
    // Configurar el enlace de "Ver Página Completa"
    const viewFullBtn = document.getElementById('modalViewFull');
    const productUrl = APP_URL + 'producto.php?id=' + producto.id;
    
    // Establecer href
    viewFullBtn.href = productUrl;
    
    console.log('=== DEBUG MODAL ===');
    console.log('APP_URL:', APP_URL);
    console.log('Producto ID:', producto.id);
    console.log('URL completa:', productUrl);
    console.log('Href del botón:', viewFullBtn.href);
    
    // Remover cualquier handler anterior
    viewFullBtn.onclick = null;
    
    // Agregar nuevo handler que fuerza la navegación
    viewFullBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir comportamiento por defecto
        const url = this.href;
        console.log('Redirigiendo a:', url);
        
        // Forzar navegación
        window.location.href = url;
    });
    
    // Precio
    let priceHtml = '';
    if (producto.precio_oferta && producto.precio_oferta > 0) {
        priceHtml = `<span class="price-original" style="font-size: 1rem;">S/ ${parseFloat(producto.precio).toFixed(2)}</span>
                     <span class="price-current" style="font-size: 1.75rem;">S/ ${parseFloat(producto.precio_oferta).toFixed(2)}</span>`;
    } else {
        priceHtml = `<span class="price-current" style="font-size: 1.75rem;">S/ ${parseFloat(producto.precio).toFixed(2)}</span>`;
    }
    document.getElementById('modalPrice').innerHTML = priceHtml;
    
    // Badges
    let badgesHtml = '';
    if (producto.descuento > 0) {
        badgesHtml += `<span class="badge badge-discount">-${producto.descuento}%</span>`;
    }
    if (producto.destacado == 1) {
        badgesHtml += `<span class="badge badge-featured">⭐ Destacado</span>`;
    }
    document.getElementById('modalBadges').innerHTML = badgesHtml;
    
    // Tallas
    const sizesContainer = document.getElementById('modalSizes');
    if (producto.tallas && producto.tallas.trim() !== '') {
        sizesContainer.style.display = 'flex';
        document.getElementById('modalSizesContent').textContent = producto.tallas;
    } else {
        sizesContainer.style.display = 'none';
    }
    
    // Stock
    const stockEl = document.getElementById('modalStock');
    const addCartBtn = document.getElementById('modalAddCart');
    const stock = parseInt(producto.stock);
    
    if (stock <= 0) {
        stockEl.className = 'modal-stock out';
        stockEl.textContent = '❌ Producto agotado';
        addCartBtn.disabled = true;
        addCartBtn.textContent = 'Producto Agotado';
    } else if (stock <= 5) {
        stockEl.className = 'modal-stock low';
        stockEl.textContent = `⚠️ ¡Solo quedan ${stock} unidades!`;
        addCartBtn.disabled = false;
        addCartBtn.textContent = '🛒 Agregar al Carrito';
    } else {
        stockEl.className = 'modal-stock';
        stockEl.textContent = `✅ En stock (${stock} disponibles)`;
        addCartBtn.disabled = false;
        addCartBtn.textContent = '🛒 Agregar al Carrito';
    }
    
    // Resetear cantidad
    const quantityInput = document.getElementById('modalQuantity');
    quantityInput.value = 1;
    quantityInput.max = stock > 0 ? stock : 1;
    
    // Resetear zoom
    resetZoom();
    
    // Mostrar modal con animación
    setTimeout(() => {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }, 10);
}

// Cerrar modal
function closeProductModal() {
    const modal = document.getElementById('productModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    resetZoom();
}

// Toggle Zoom
function toggleZoom(event) {
    event.stopPropagation();
    const imageContainer = document.getElementById('imageContainer');
    const modalImage = document.getElementById('modalImage');
    
    if (!isZoomed) {
        // Activar zoom
        imageContainer.classList.add('zoomed');
        isZoomed = true;
        
        // Posicionar según donde hizo click
        updateZoomPosition(event);
    } else {
        // Desactivar zoom
        resetZoom();
    }
}

// Actualizar posición del zoom según mouse
function updateZoomPosition(event) {
    if (!isZoomed) return;
    
    const container = document.getElementById('imageContainer');
    const image = document.getElementById('modalImage');
    const rect = container.getBoundingClientRect();
    
    // Calcular posición relativa del mouse
    const x = ((event.clientX - rect.left) / rect.width) * 100;
    const y = ((event.clientY - rect.top) / rect.height) * 100;
    
    // Aplicar transformación
    image.style.transformOrigin = `${x}% ${y}%`;
}

// Resetear zoom
function resetZoom() {
    const container = document.getElementById('imageContainer');
    const image = document.getElementById('modalImage');
    
    if (container && image) {
        container.classList.remove('zoomed');
        image.style.transformOrigin = 'center center';
        isZoomed = false;
    }
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductModal();
    }
});

// Control de cantidad
function increaseQuantity() {
    const input = document.getElementById('modalQuantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('modalQuantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}
</script>