<link rel="stylesheet" href="<?= APP_URL ?>css/carrito_modal.css">

<!-- Modal del Carrito - Diseño Moderno y Profesional -->
<div id="carritoModal" class="carrito-modal-modern">
    <div class="carrito-contenido-modern">
        <!-- Header del Carrito -->
        <div class="carrito-header-modern">
            <div class="header-content">
                <div class="header-title">
                    <div class="cart-icon-modern">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h2>Mi Carrito de Compras</h2>
                        <p class="items-count">
                            <?php 
                            $total_items = 0;
                            if (!empty($_SESSION['carrito'])) {
                                foreach ($_SESSION['carrito'] as $item) {
                                    $total_items += $item['cantidad'];
                                }
                            }
                            echo $total_items . ' ' . ($total_items === 1 ? 'producto' : 'productos');
                            ?>
                        </p>
                    </div>
                </div>
                <button class="carrito-close-modern" aria-label="Cerrar carrito">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Body del Carrito -->
        <div class="carrito-body-modern">
            <?php if (empty($_SESSION['carrito'])): ?>
                <!-- Estado: Carrito Vacío -->
                <div class="carrito-vacio-modern">
                    <div class="empty-cart-icon">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </div>
                    <h3>Tu carrito está vacío</h3>
                    <p>Agrega algunos productos increíbles a tu carrito</p>
                    <a href="<?= APP_URL ?>index.php" class="btn-continue-shopping">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 5l-7 7 7 7"/>
                        </svg>
                        Continuar Comprando
                    </a>
                </div>
            <?php else: ?>
                <!-- Lista de Productos -->
                <div class="carrito-items-modern">
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $key => $item): 
                        $producto = obtenerProductoPorId($db, $item['producto_id']);
                        if (!$producto) continue;
                        
                        $precio = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
                                ? $producto['precio_oferta'] 
                                : $producto['precio'];
                        $subtotal = $precio * $item['cantidad'];
                        $total += $subtotal;
                        
                        // Construir la ruta correcta de la imagen
                        $imagen_url = '';
                        if (!empty($producto['imagen'])) {
                            // Si la imagen ya contiene la ruta completa, usarla directamente
                            if (strpos($producto['imagen'], 'http') === 0) {
                                $imagen_url = $producto['imagen'];
                            } else {
                                // Si no, construir la ruta
                                $imagen_url = APP_URL . 'app/views/img/productos/' . $producto['imagen'];
                            }
                        }
                    ?>
                        <div class="carrito-item-modern" data-item-key="<?= $key ?>">
                            <!-- Imagen del Producto -->
                            <div class="item-image-modern">
                                <?php if (!empty($imagen_url)): ?>
                                    <img src="<?= htmlspecialchars($imagen_url) ?>" 
                                         alt="<?= htmlspecialchars($producto['nombre']) ?>" 
                                         class="item-img-modern"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="item-placeholder-modern" style="display: none;">
                                        <?php
                                        $cat_iconos = [
                                            'Vestidos' => '👗',
                                            'Blusas' => '👚',
                                            'Pantalones' => '👖',
                                            'Faldas' => '🩱',
                                            'Accesorios' => '👜',
                                            'Conjuntos' => '👔'
                                        ];
                                        echo $cat_iconos[$producto['categoria_nombre']] ?? '👕';
                                        ?>
                                    </div>
                                <?php else: ?>
                                    <div class="item-placeholder-modern">
                                        <?php
                                        $cat_iconos = [
                                            'Vestidos' => '👗',
                                            'Blusas' => '👚',
                                            'Pantalones' => '👖',
                                            'Faldas' => '🩱',
                                            'Accesorios' => '👜',
                                            'Conjuntos' => '👔'
                                        ];
                                        echo $cat_iconos[$producto['categoria_nombre']] ?? '👕';
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Detalles del Producto -->
                            <div class="item-details-modern">
                                <div class="item-header">
                                    <h4 class="item-title"><?= htmlspecialchars($producto['nombre']) ?></h4>
                                    <button class="item-remove" data-key="<?= $key ?>" title="Eliminar producto">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Variantes -->
                                <?php if (!empty($item['talla']) || !empty($item['color'])): ?>
                                    <div class="item-variants">
                                        <?php if (!empty($item['talla'])): ?>
                                            <span class="variant-tag">Talla: <?= htmlspecialchars($item['talla']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($item['color'])): ?>
                                            <span class="variant-tag">Color: <?= htmlspecialchars($item['color']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Información de Stock -->
                                <div class="item-stock-modern">
                                    <?php if ($producto['stock'] > 0): ?>
                                        <span class="stock-badge in-stock">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            En stock (<?= $producto['stock'] ?> disponibles)
                                        </span>
                                    <?php else: ?>
                                        <span class="stock-badge out-of-stock">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                            Agotado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Precio y Cantidad -->
                                <div class="item-controls">
                                    <div class="price-section">
                                        <span class="item-price-modern">S/ <?= number_format($precio, 2) ?></span>
                                        <?php if (!empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0): ?>
                                            <span class="original-price">S/ <?= number_format($producto['precio'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="quantity-controls-modern">
                                        <form method="POST" class="quantity-form">
                                            <input type="hidden" name="key" value="<?= $key ?>">
                                            <button type="submit" name="actualizar_cantidad" 
                                                    class="qty-btn-modern decrease" 
                                                    onclick="this.form.cantidad.value = Math.max(1, parseInt(this.form.cantidad.value) - 1)"
                                                    <?= $item['cantidad'] <= 1 ? 'disabled' : '' ?>>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                </svg>
                                            </button>
                                            <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" 
                                                   min="1" max="<?= $producto['stock'] ?>" 
                                                   class="qty-input-modern" readonly>
                                            <button type="submit" name="actualizar_cantidad" 
                                                    class="qty-btn-modern increase"
                                                    onclick="this.form.cantidad.value = Math.min(<?= $producto['stock'] ?>, parseInt(this.form.cantidad.value) + 1)"
                                                    <?= $item['cantidad'] >= $producto['stock'] ? 'disabled' : '' ?>>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Subtotal -->
                                <div class="item-subtotal-modern">
                                    <span>Subtotal:</span>
                                    <span class="subtotal-amount">S/ <?= number_format($subtotal, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Resumen y Acciones -->
                <div class="carrito-footer-modern">
                    <!-- Beneficios -->
                    <div class="cart-benefits">
                        <div class="benefit-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            <span>Envío gratis en compras mayores a S/ 100</span>
                        </div>
                        <div class="benefit-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            <span>Pago seguro con cualquier método</span>
                        </div>
                    </div>
                    
                    <!-- Resumen de Compra -->
                    <div class="cart-summary-modern">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>S/ <?= number_format($total, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Envío:</span>
                            <span class="free-shipping">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Gratis
                            </span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-total">
                            <span>Total:</span>
                            <span class="total-amount-modern">S/ <?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="cart-actions-modern">
                        <form method="POST" class="clear-cart-form">
                            <button type="submit" name="vaciar_carrito" class="btn-clear-cart" onclick="return confirm('¿Estás seguro de que quieres vaciar todo el carrito?')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Vaciar Carrito
                            </button>
                        </form>
                        <a href="<?= APP_URL ?>checkout.php" class="btn-checkout-modern" onclick="return validarCheckout()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"></path>
                            </svg>
                            Proceder al Pago
                        </a>
                    </div>
                    
                    <!-- Seguridad -->
                    <div class="security-notice">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <circle cx="12" cy="16" r="1"></circle>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Compra 100% segura - Tus datos están protegidos</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="carritoOverlay" class="carrito-overlay-modern"></div>