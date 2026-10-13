<?php
/**
 * Navegación de categorías con diseño profesional premium
 * BOUTIQUE SIXTA COVEÑAS
 */
?>

<!-- Sección de Categorías Premium -->
<section class="categories-section-premium" id="categories">
        <!-- Grid de Categorías -->
        <div class="categories-grid-premium">
            <!-- Categoría "Todas" -->
            <a href="<?= APP_URL ?>index.php" 
               class="category-card-premium <?= !isset($_GET['categoria']) ? 'active' : '' ?>">
                <div class="card-background-premium"></div>
                <div class="card-content-premium">
                    <div class="category-icon-premium">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h18v18H3zM9 3v18M3 9h18M3 15h18"/>
                        </svg>
                    </div>
                    <h3 class="category-title-premium">Todos</h3>
                    <p class="category-desc-premium">Ver todos los productos</p>
                    <div class="category-arrow-premium">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            
            <!-- Categorías dinámicas -->
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): 
                    $activa = isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id'];
                    $imagen_url = getImagenCategoria($categoria['imagen']);
                    $color_class = getCategoriaColorClass($categoria['id']);
                ?>
                <a href="<?= APP_URL ?>index.php?categoria=<?= $categoria['id'] ?>" 
                   class="category-card-premium <?= $activa ? 'active' : '' ?> <?= $color_class ?>"
                   title="<?= sanitizar($categoria['descripcion'] ?? $categoria['nombre']) ?>">
                    
                    <div class="card-background-premium"></div>
                    
                    <?php if (!empty($categoria['imagen'])): ?>
                        <div class="category-image-premium">
                            <img src="<?= $imagen_url ?>" 
                                 alt="<?= sanitizar($categoria['nombre']) ?>"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.parentElement.parentElement.querySelector('.category-icon-premium').style.display='flex'">
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content-premium">
                        <?php if (empty($categoria['imagen'])): ?>
                            <div class="category-icon-premium"><?= getCategoriaIcon($categoria['nombre']) ?></div>
                        <?php endif; ?>
                        
                        <h3 class="category-title-premium"><?= sanitizar($categoria['nombre']) ?></h3>
                        <p class="category-desc-premium"><?= acortarTexto(sanitizar($categoria['descripcion'] ?? 'Colección exclusiva'), 40) ?></p>
                        
                        <div class="category-arrow-premium">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-categories-premium">
                    <div class="no-categories-icon-premium">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7h-3V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM9 4h6v3H9V4zm11 16H4V9h16v11z"/>
                        </svg>
                    </div>
                    <h3>No hay categorías disponibles</h3>
                    <p>Pronto agregaremos nuevas colecciones para ti</p>
                </div>
            <?php endif; ?>
        </div>
   
</section>

<link rel="stylesheet" href="<?= APP_URL ?>css/categorias_nav.css">

<?php
// Funciones auxiliares
function getCategoriaIcon($nombre_categoria) {
    $iconos = [
        'Accesorios' => '💎',
        'Blusas' => '👚',
        'Conjuntos' => '👗',
        'Faldas' => '👖',
        'Pantalones' => '🩳',
        'Vestidos' => '🥻',
        'Zapatos' => '👠',
        'Bolsos' => '👜',
        'Joyeria' => '👑'
    ];
    
    return $iconos[$nombre_categoria] ?? '📦';
}

function getCategoriaColorClass($categoria_id) {
    $colores = [
        1 => 'color-accent',
        2 => 'color-success', 
        3 => 'color-warning',
        4 => 'color-pink',
        5 => 'color-purple',
        6 => 'color-blue',
        7 => 'color-teal',
        8 => 'color-orange'
    ];
    
    return $colores[$categoria_id] ?? 'color-accent';
}
?>