<?php
/**
 * Autoloader específico para el Panel Administrativo
 * Carga las clases desde la raíz del proyecto
 */

// Ruta base del proyecto (sube 2 niveles desde app/admin/)
$rootPath = dirname(__DIR__, 2);

// Función de autoload personalizado
spl_autoload_register(function ($className) use ($rootPath) {
    // Remover el prefijo 'app\' si existe
    $classPath = str_replace('app\\', '', $className);
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);

      
    // Posibles ubicaciones de las clases
    $possiblePaths = [
        // Buscar directamente en la ruta con namespace
        $rootPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $classPath . '.php',
        // Buscar en controllers
        $rootPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $classPath . '.php',
        // Buscar en models
        $rootPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $classPath . '.php',
        // Buscar solo el nombre de la clase en controllers
        $rootPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . basename($classPath) . '.php',
        // Buscar solo el nombre de la clase en models
        $rootPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . basename($classPath) . '.php'
    ];
    
    // Intentar cargar desde cada ubicación posible
    foreach ($possiblePaths as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            require_once $filePath;
            return true;
        }
    }
    
    // Si no se encuentra, registrar el error
    error_log("Autoloader: No se pudo cargar la clase: $className");
    foreach ($possiblePaths as $path) {
        error_log("  - Buscado en: $path");
    }
    return false;

});


// Cargar clases esenciales manualmente como respaldo
$essentialFiles = [
    '/app/models/mainModel.php',
    '/app/models/viewsModel.php',
    '/app/controllers/viewsController.php',
    '/app/controllers/loginController.php'
];

foreach ($essentialFiles as $file) {
    $filePath = $rootPath . $file;
    
    if (file_exists($filePath) && is_file($filePath)) {
        require_once $filePath;
    } else {
        error_log("Advertencia: No se encontró el archivo esencial: $filePath");
    }
}
?>