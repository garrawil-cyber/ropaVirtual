<?php
/**
 * Configuración general de la aplicación
 * Ruta: config/app.php
 */

// ==================================================
// CONFIGURACIÓN DE URLs
// ==================================================

// URL base de la aplicación (ajustar según tu servidor)
define('APP_URL', 'http://localhost/ropavirtual_mvc/');

// URL de vistas
const VIEW_URL = "http://localhost/ropaVirtual_mvc/app/views/";

// URL del panel administrativo
define('ADMIN_URL', APP_URL . 'app/admin/');

// ==================================================
// CONFIGURACIÓN DE RUTAS FÍSICAS
// ==================================================

// Ruta física base de la aplicación
define('APP_PATH', dirname(__DIR__) . '/');

// Ruta base del proyecto (para compatibilidad)
define('BASE_PATH', APP_PATH);

// ==================================================
// RUTAS DE DIRECTORIOS
// ==================================================

define('CONFIG_DIR', BASE_PATH . 'config/');
define('MODELS_DIR', BASE_PATH . 'models/');
define('INCLUDES_DIR', BASE_PATH . 'includes/');
define('TEMPLATES_DIR', BASE_PATH . 'templates/');
define('VIEWS_DIR', BASE_PATH . 'views/');

// ==================================================
// RUTAS DE RECURSOS PÚBLICOS
// ==================================================

define('CSS_URL', APP_URL . 'css/');
define('JS_URL', APP_URL . 'js/');
define('IMAGES_URL', APP_URL . 'images/');

// ==================================================
// RUTAS DE IMÁGENES - FRONTEND (app/views/img/)
// ==================================================

// Rutas físicas (para verificar si existen)
define('IMG_PRODUCTOS_PATH', APP_PATH . 'app/views/img/productos/');
define('IMG_CATEGORIAS_PATH', APP_PATH . 'app/views/img/categorias/');

// URLs públicas (para mostrar en HTML)
define('IMG_PRODUCTOS_URL', APP_URL . 'app/views/img/productos/');
define('IMG_CATEGORIAS_URL', APP_URL . 'app/views/img/categorias/');
define('IMG_URL', APP_URL . 'app/views/img/');

// Imagen por defecto
define('IMG_DEFAULT', APP_URL . 'app/views/img/productos/default.png');

// ==================================================
// CONFIGURACIÓN DE ZONA HORARIA
// ==================================================

date_default_timezone_set('America/Lima');

// ==================================================
// CONFIGURACIÓN DE ERRORES
// ==================================================

define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==================================================
// CONSTANTES DE LA APLICACIÓN
// ==================================================

define('APP_NAME', 'Boutique Sixta Coveñas');
define('APP_DESCRIPTION', 'Moda y estilo para toda ocasión');
define('APP_KEYWORDS', 'ropa, moda, boutique, estilo, accesorios');

// ==================================================
// INFORMACIÓN DE CONTACTO
// ==================================================

define('CONTACT_EMAIL', 'info@boutiquesixta.com');
define('CONTACT_PHONE', '+57 300 123 4567');
define('CONTACT_WHATSAPP', '573001234567');
define('CONTACT_ADDRESS', 'Coveñas, Sucre - Colombia');

// ==================================================
// REDES SOCIALES
// ==================================================

define('SOCIAL_FACEBOOK', 'https://facebook.com/boutiquesixta');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/boutiquesixta');
define('SOCIAL_WHATSAPP', 'https://wa.me/' . CONTACT_WHATSAPP);

// ==================================================
// FUNCIONES HELPER PARA IMÁGENES
// ==================================================

/**
 * Obtener la URL completa de una imagen de producto
 * @param string $imagen Nombre del archivo de imagen
 * @return string URL de la imagen o imagen por defecto
 */
function getImagenProducto($imagen) {
    if (empty($imagen)) {
        return IMG_DEFAULT;
    }
    
    // Verificar si la imagen existe físicamente
    $ruta_fisica = IMG_PRODUCTOS_PATH . $imagen;
    
    if (file_exists($ruta_fisica)) {
        return IMG_PRODUCTOS_URL . $imagen;
    }
    
    // Intentar con extensiones comunes si no tiene extensión
    if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $imagen)) {
        $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($extensiones as $ext) {
            $ruta_con_ext = IMG_PRODUCTOS_PATH . $imagen . '.' . $ext;
            if (file_exists($ruta_con_ext)) {
                return IMG_PRODUCTOS_URL . $imagen . '.' . $ext;
            }
        }
    }
    
    return IMG_DEFAULT;
}

/**
 * Obtener la URL completa de una imagen de categoría
 * @param string $imagen Nombre del archivo de imagen
 * @return string URL de la imagen o imagen por defecto
 */
function getImagenCategoria($imagen) {
    if (empty($imagen)) {
        return IMG_DEFAULT;
    }
    
    // Verificar si la imagen existe físicamente
    $ruta_fisica = IMG_CATEGORIAS_PATH . $imagen;
    
    if (file_exists($ruta_fisica)) {
        return IMG_CATEGORIAS_URL . $imagen;
    }
    
    // Intentar con extensiones comunes si no tiene extensión
    if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $imagen)) {
        $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($extensiones as $ext) {
            $ruta_con_ext = IMG_CATEGORIAS_PATH . $imagen . '.' . $ext;
            if (file_exists($ruta_con_ext)) {
                return IMG_CATEGORIAS_URL . $imagen . '.' . $ext;
            }
        }
    }
    
    return IMG_DEFAULT;
}

/**
 * Formatear precio en soles
 * @param float $precio Precio a formatear
 * @return string Precio formateado
 */
function formatearPrecio($precio) {
    return 'S/ ' . number_format($precio, 2);
}

/**
 * Calcular porcentaje de descuento
 * @param float $precio_original Precio original
 * @param float $precio_oferta Precio de oferta
 * @return int Porcentaje de descuento
 */
function calcularDescuento($precio_original, $precio_oferta) {
    if ($precio_original <= 0 || $precio_oferta >= $precio_original) {
        return 0;
    }
    
    $descuento = (($precio_original - $precio_oferta) / $precio_original) * 100;
    return round($descuento);
}

/**
 * Sanitizar texto para HTML
 * @param string $texto Texto a sanitizar
 * @return string Texto sanitizado
 */
function sanitizar($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Acortar texto a una longitud específica
 * @param string $texto Texto a acortar
 * @param int $longitud Longitud máxima
 * @return string Texto acortado
 */
function acortarTexto($texto, $longitud = 100) {
    if (strlen($texto) <= $longitud) {
        return $texto;
    }
    
    return substr($texto, 0, $longitud) . '...';
}

/**
 * Verificar si una imagen de producto existe
 * @param string $imagen Nombre del archivo
 * @return bool
 */
function imagenProductoExiste($imagen) {
    if (empty($imagen)) {
        return false;
    }
    
    return file_exists(IMG_PRODUCTOS_PATH . $imagen);
}

/**
 * Verificar si una imagen de categoría existe
 * @param string $imagen Nombre del archivo
 * @return bool
 */
function imagenCategoriaExiste($imagen) {
    if (empty($imagen)) {
        return false;
    }
    
    return file_exists(IMG_CATEGORIAS_PATH . $imagen);
}

/**
 * Obtener todas las imágenes de la carpeta de productos
 * @return array Lista de archivos de imagen
 */
function listarImagenesProductos() {
    $imagenes = [];
    
    if (is_dir(IMG_PRODUCTOS_PATH)) {
        $archivos = scandir(IMG_PRODUCTOS_PATH);
        foreach ($archivos as $archivo) {
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $archivo)) {
                $imagenes[] = $archivo;
            }
        }
    }
    
    return $imagenes;
}

// ==================================================
// DEBUG: Mostrar información de rutas (solo en modo DEBUG)
// ==================================================

if (DEBUG && isset($_GET['debug_paths'])) {
    echo "<pre style='background: #f4f4f4; padding: 20px; border-radius: 8px; font-family: monospace;'>";
    echo "<h3>🔍 DEBUG - Rutas de la Aplicación</h3>";
    echo "<strong>APP_URL:</strong> " . APP_URL . "\n";
    echo "<strong>APP_PATH:</strong> " . APP_PATH . "\n\n";
    
    echo "<strong>IMG_PRODUCTOS_PATH:</strong> " . IMG_PRODUCTOS_PATH . "\n";
    echo "<strong>Existe carpeta productos:</strong> " . (is_dir(IMG_PRODUCTOS_PATH) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "<strong>IMG_CATEGORIAS_PATH:</strong> " . IMG_CATEGORIAS_PATH . "\n";
    echo "<strong>Existe carpeta categorías:</strong> " . (is_dir(IMG_CATEGORIAS_PATH) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "<strong>IMG_PRODUCTOS_URL:</strong> " . IMG_PRODUCTOS_URL . "\n";
    echo "<strong>IMG_CATEGORIAS_URL:</strong> " . IMG_CATEGORIAS_URL . "\n";
    echo "<strong>IMG_DEFAULT:</strong> " . IMG_DEFAULT . "\n\n";
    
    echo "<strong>Imágenes en productos:</strong>\n";
    if (is_dir(IMG_PRODUCTOS_PATH)) {
        $archivos = scandir(IMG_PRODUCTOS_PATH);
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                echo "  - $archivo\n";
            }
        }
    } else {
        echo "  ❌ La carpeta no existe\n";
    }
    
    echo "\n<strong>Imágenes en categorías:</strong>\n";
    if (is_dir(IMG_CATEGORIAS_PATH)) {
        $archivos = scandir(IMG_CATEGORIAS_PATH);
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                echo "  - $archivo\n";
            }
        }
    } else {
        echo "  ❌ La carpeta no existe\n";
    }
    
    echo "</pre>";
    exit;
}
?>