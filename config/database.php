<?php
/**
 * Configuración de la base de datos
 * Ruta: config/database.php
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'tiendaropa');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false, // Conexiones no persistentes
    ];

    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Variable global para usar en toda la aplicación
    $GLOBALS['db'] = $db;

} catch (PDOException $e) {
    error_log("Error de conexión BD: " . $e->getMessage());
    
    if (defined('DEBUG') && DEBUG) {
        die("⚠️ Error de conexión a la base de datos: " . $e->getMessage());
    } else {
        die("Error al conectar con la base de datos. Contacte al administrador.");
    }
}

/**
 * Función helper para obtener la conexión
 */
function getDB() {
    return $GLOBALS['db'];
}
?>