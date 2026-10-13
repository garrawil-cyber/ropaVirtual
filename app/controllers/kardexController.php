<?php
/**
 * Controlador de Kardex
 * Ruta: app/admin/kardex/KardexController.php
 * Maneja la lógica de negocio del kardex
 */

// Incluir dependencias
require_once dirname(__DIR__, 2) . "/config/database.php";
require_once dirname(__DIR__, 2) . '/app/models/kardexModel.php';

class KardexController {
    private $kardexModel;
    private $db;
    
    public function __construct() {
        $this->db = getDB();
        $this->kardexModel = new KardexModel($this->db);
    }
    
    /**
     * Obtener lista de productos activos
     */
    public function obtenerProductos() {
        try {
            return $this->kardexModel->obtenerProductosActivos();
        } catch (Exception $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas del kardex
     */
    public function obtenerEstadisticas($producto_id = null) {
        try {
            return $this->kardexModel->obtenerEstadisticas($producto_id);
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_entradas' => 0,
                'total_salidas' => 0,
                'total_movimientos' => 0
            ];
        }
    }
    
    /**
     * Obtener movimientos del kardex con filtros
     */
    public function obtenerMovimientos($filtros = []) {
        try {
            $producto_id = $filtros['producto_id'] ?? null;
            $fecha_inicio = $filtros['fecha_inicio'] ?? null;
            $fecha_fin = $filtros['fecha_fin'] ?? null;
            
            // Si hay filtros de fecha
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                return $this->kardexModel->obtenerMovimientosPorFecha(
                    $fecha_inicio,
                    $fecha_fin,
                    $producto_id
                );
            }
            
            // Si solo hay filtro de producto
            if (!empty($producto_id)) {
                return $this->kardexModel->obtenerKardex($producto_id);
            }
            
            // Sin filtros, obtener todos
            return $this->kardexModel->obtenerKardex();
            
        } catch (Exception $e) {
            error_log("Error al obtener movimientos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener información de un producto
     */
    public function obtenerInfoProducto($producto_id) {
        try {
            return $this->kardexModel->obtenerProducto($producto_id);
        } catch (Exception $e) {
            error_log("Error al obtener producto: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Procesar mensajes de la URL
     */
    public function procesarMensajes() {
        $mensaje = '';
        $tipo_mensaje = '';
        
        if (isset($_GET['success'])) {
            $mensaje = isset($_GET['msg']) ? urldecode($_GET['msg']) : 'Operación realizada exitosamente';
            $tipo_mensaje = 'success';
        } elseif (isset($_GET['error'])) {
            $mensaje = isset($_GET['msg']) ? urldecode($_GET['msg']) : 'Error al procesar la operación';
            $tipo_mensaje = 'danger';
        }
        
        return [
            'mensaje' => $mensaje,
            'tipo' => $tipo_mensaje
        ];
    }
    
    /**
     * Registrar un nuevo movimiento
     */
    public function registrarMovimiento($datos) {
        // Validar datos
        $errores = $this->kardexModel->validarDatos($datos);
        
        if (!empty($errores)) {
            return [
                'success' => false,
                'message' => implode(', ', $errores)
            ];
        }
        
        // Registrar movimiento
        return $this->kardexModel->registrarMovimiento(
            $datos['producto_id'],
            $datos['tipo_movimiento'],
            $datos['cantidad'],
            $datos['observaciones'] ?? '',
            $datos['usuario_id'] ?? null
        );
    }
    
    /**
     * Eliminar un movimiento
     */
    public function eliminarMovimiento($id) {
        if (!is_numeric($id) || $id <= 0) {
            return [
                'success' => false,
                'message' => 'ID de movimiento inválido'
            ];
        }
        
        return $this->kardexModel->eliminarMovimiento($id);
    }
    
    /**
     * Obtener resumen por producto
     */
    public function obtenerResumen() {
        try {
            return $this->kardexModel->obtenerResumenPorProducto();
        } catch (Exception $e) {
            error_log("Error al obtener resumen: " . $e->getMessage());
            return [];
        }
    }
}
?>