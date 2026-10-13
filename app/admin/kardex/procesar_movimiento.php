<?php
/**
 * Procesar Movimiento de Kardex
 * Ruta: app/admin/kardex/procesar_movimiento.php
 */

// Incluir el controlador
require_once dirname(__DIR__, 2) . '/controllers/KardexController.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?error=metodo_invalido');
    exit;
}

try {
    // Crear instancia del controlador
    $controller = new KardexController();
    
    // Limpiar y validar datos
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    $tipo_movimiento = strtoupper(trim($_POST['tipo_movimiento'] ?? ''));
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $observaciones = trim($_POST['observaciones'] ?? '');
    
    // Preparar datos para el controlador
    $datos = [
        'producto_id' => $producto_id,
        'tipo_movimiento' => $tipo_movimiento,
        'cantidad' => $cantidad,
        'observaciones' => $observaciones,
        'usuario_id' => $_SESSION['usuario_id'] ?? null // Si usas sesiones
    ];
    
    // Registrar el movimiento
    $resultado = $controller->registrarMovimiento($datos);
    
    // Redirigir según el resultado
    if ($resultado['success']) {
        header('Location: index.php?success=1&msg=' . urlencode($resultado['message']));
    } else {
        header('Location: index.php?error=1&msg=' . urlencode($resultado['message']));
    }
    
} catch (Exception $e) {
    error_log("Error en procesar_movimiento.php: " . $e->getMessage());
    header('Location: index.php?error=1&msg=' . urlencode('Error al procesar el movimiento'));
}

exit;
?>