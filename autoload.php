<?php
/**
 * Carga automática de clases del proyecto
 */

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }

    if (defined('DEBUG') && DEBUG) {
        error_log("⚠️ Clase no encontrada: $class");
    }
});
