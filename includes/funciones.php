<?php
/**
 * Funciones helper para la aplicación
 * Ruta: includes/funciones.php
 */

/**
 * Procesar acciones del carrito
 */
function procesarCarrito() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Agregar al carrito
        if (isset($_POST['agregar_carrito'])) {
            agregarAlCarrito();
        }
        
        // Actualizar cantidad
        if (isset($_POST['actualizar_cantidad'])) {
            actualizarCantidadCarrito();
        }
        
        // Eliminar del carrito
        if (isset($_POST['eliminar_carrito'])) {
            eliminarDelCarrito();
        }
        
        // Vaciar carrito
        if (isset($_POST['vaciar_carrito'])) {
            vaciarCarrito();
        }
    }
}

/**
 * Agregar producto al carrito
 */
function agregarAlCarrito() {
    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad'] ?? 1);
    $talla = isset($_POST['talla']) ? trim($_POST['talla']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    // Verificar si el producto ya está en el carrito
    $key = buscarProductoEnCarrito($producto_id, $talla, $color);
    
    if ($key !== false) {
        // Actualizar cantidad
        $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => '¡Producto actualizado en el carrito!'
        ];
    } else {
        // Agregar nuevo item
        $_SESSION['carrito'][] = [
            'producto_id' => $producto_id,
            'cantidad' => $cantidad,
            'talla' => $talla,
            'color' => $color
        ];
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => '¡Producto agregado al carrito!'
        ];
    }
}

/**
 * Buscar producto en el carrito
 */
function buscarProductoEnCarrito($producto_id, $talla, $color) {
    if (!isset($_SESSION['carrito'])) return false;
    
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['producto_id'] == $producto_id && 
            $item['talla'] == $talla && 
            $item['color'] == $color) {
            return $key;
        }
    }
    
    return false;
}

/**
 * Actualizar cantidad en el carrito
 */
function actualizarCantidadCarrito() {
    $key = intval($_POST['key']);
    $cantidad = intval($_POST['cantidad']);
    
    if (isset($_SESSION['carrito'][$key])) {
        if ($cantidad <= 0) {
            unset($_SESSION['carrito'][$key]);
            $_SESSION['mensaje'] = [
                'tipo' => 'info',
                'texto' => 'Producto eliminado del carrito'
            ];
        } else {
            $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => 'Cantidad actualizada'
            ];
        }
    }
}

/**
 * Eliminar producto del carrito
 */
function eliminarDelCarrito() {
    $key = intval($_POST['key']);
    
    if (isset($_SESSION['carrito'][$key])) {
        unset($_SESSION['carrito'][$key]);
        $_SESSION['mensaje'] = [
            'tipo' => 'info',
            'texto' => 'Producto eliminado del carrito'
        ];
    }
}

/**
 * Vaciar carrito completo
 */
function vaciarCarrito() {
    $_SESSION['carrito'] = [];
    $_SESSION['mensaje'] = [
        'tipo' => 'info',
        'texto' => 'Carrito vaciado'
    ];
}

/**
 * Mostrar mensajes del carrito
 */
function mostrarMensajesCarrito() {
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        $clase = $mensaje['tipo'] === 'success' ? 'alert-success' : 
                ($mensaje['tipo'] === 'error' ? 'alert-error' : 'alert-info');
        
        echo '<div class="alert ' . $clase . '" style="margin: 1rem 0; padding: 1rem; border-radius: 8px; text-align: center;">';
        echo sanitizar($mensaje['texto']);
        echo '</div>';
        unset($_SESSION['mensaje']);
    }
}

/**
 * Obtener categorías desde la base de datos
 */
function obtenerCategorias($db) {
    try {
        $sql = "SELECT id, nombre, descripcion, imagen, activo 
                FROM categorias 
                WHERE activo = 1 
                ORDER BY nombre ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerCategorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener productos desde la base de datos con imágenes
 */
function obtenerProductos($db, $categoria_id = null, $busqueda = '') {
    try {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 AND c.activo = 1";
        
        $params = [];
        
        // Filtro por categoría
        if ($categoria_id) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $categoria_id;
        }
        
        // Filtro por búsqueda
        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ?)";
            $searchTerm = "%$busqueda%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY p.destacado DESC, p.fecha_creacion DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerProductos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener producto por ID con imagen
 */
function obtenerProductoPorId($db, $id) {
    try {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = ? AND p.activo = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerProductoPorId: " . $e->getMessage());
        return null;
    }
}

/**
 * Calcular total del carrito
 */
function calcularTotalCarrito($db, $carrito) {
    $total = 0;
    
    foreach ($carrito as $item) {
        $producto = obtenerProductoPorId($db, $item['producto_id']);
        if ($producto) {
            // Usar precio de oferta si existe, sino usar precio normal
            $precio = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
                      ? $producto['precio_oferta'] 
                      : $producto['precio'];
            $total += $precio * $item['cantidad'];
        }
    }
    
    return $total;
}

/**
 * Obtener URL de imagen de producto con verificación
 */
function obtenerImagenProducto($imagen) {
    if (empty($imagen)) {
        return IMG_DEFAULT;
    }
    
    $ruta_completa = IMG_PRODUCTOS_PATH . $imagen;
    
    if (file_exists($ruta_completa)) {
        return IMG_PRODUCTOS_URL . $imagen;
    }
    
    return IMG_DEFAULT;
}

