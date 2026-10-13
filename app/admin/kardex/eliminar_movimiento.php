<?php
/**
 * Eliminar Movimiento de Kardex
 * Ruta: app/admin/kardex/eliminar_movimiento.php
 */

// Incluir el controlador
require_once dirname(__DIR__, 2) . '/controllers/KardexController.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Verificar que se haya enviado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=sin_id');
    exit;
}

try {
    // Crear instancia del controlador
    $controller = new KardexController();
    
    // Obtener y validar el ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$id) {
        header('Location: index.php?error=id_invalido');
        exit;
    }
    
    // Eliminar el movimiento
    $resultado = $controller->eliminarMovimiento($id);
    
    // Redirigir según el resultado
    if ($resultado['success']) {
        header('Location: index.php?success=1&msg=' . urlencode($resultado['message']));
    } else {
        header('Location: index.php?error=1&msg=' . urlencode($resultado['message']));
    }
    
} catch (Exception $e) {
    error_log("Error en eliminar_movimiento.php: " . $e->getMessage());
    header('Location: index.php?error=1&msg=' . urlencode('Error al eliminar el movimiento'));
}

exit;
?>