<?php
/**
 * Modelo de Kardex
 * Ruta: app/models/KardexModel.php
 * Maneja todas las operaciones de base de datos relacionadas con el kardex
 */

class KardexModel {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Registrar un nuevo movimiento en el kardex
     */
    public function registrarMovimiento($producto_id, $tipo_movimiento, $cantidad, $observaciones = '', $usuario_id = null) {
        try {
            $this->db->beginTransaction();
            
            // Validar que el producto existe y está activo
            $stmt = $this->db->prepare("SELECT id, stock, nombre FROM productos WHERE id = ? AND activo = 1");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                throw new Exception("Producto no encontrado o inactivo");
            }
            
            // Validar stock suficiente para salidas
            if ($tipo_movimiento == 'SALIDA' && $producto['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente. Stock actual: " . $producto['stock'] . " unidades");
            }
            
            // Registrar en kardex
            $sql = "INSERT INTO kardex (producto_id, tipo_movimiento, cantidad, observaciones, fecha_movimiento, usuario_id) 
                    VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$producto_id, $tipo_movimiento, $cantidad, $observaciones, $usuario_id]);
            
            // Actualizar stock en tabla productos
            $operacion = ($tipo_movimiento == 'ENTRADA') ? '+' : '-';
            $sql_update = "UPDATE productos SET stock = stock $operacion ? WHERE id = ?";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([$cantidad, $producto_id]);
            
            // Obtener el nuevo stock
            $stmt_stock = $this->db->prepare("SELECT stock FROM productos WHERE id = ?");
            $stmt_stock->execute([$producto_id]);
            $nuevo_stock = $stmt_stock->fetchColumn();
            
            $this->db->commit();
            
            return [
                'success' => true, 
                'message' => 'Movimiento registrado exitosamente',
                'nuevo_stock' => $nuevo_stock,
                'producto' => $producto['nombre']
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al registrar movimiento: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener historial de movimientos
     */
    public function obtenerKardex($producto_id = null, $limit = 100) {
        $sql = "SELECT k.*, p.nombre as producto_nombre, p.stock as stock_actual
                FROM kardex k 
                INNER JOIN productos p ON k.producto_id = p.id";
        
        if ($producto_id) {
            $sql .= " WHERE k.producto_id = ?";
            $sql .= " ORDER BY k.fecha_movimiento DESC LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $producto_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql .= " ORDER BY k.fecha_movimiento DESC LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener movimientos por rango de fechas
     */
    public function obtenerMovimientosPorFecha($fecha_inicio, $fecha_fin, $producto_id = null) {
        $sql = "SELECT k.*, p.nombre as producto_nombre, p.stock as stock_actual
                FROM kardex k 
                INNER JOIN productos p ON k.producto_id = p.id 
                WHERE DATE(k.fecha_movimiento) BETWEEN ? AND ?";
        
        $params = [$fecha_inicio, $fecha_fin];
        
        if ($producto_id) {
            $sql .= " AND k.producto_id = ?";
            $params[] = $producto_id;
        }
        
        $sql .= " ORDER BY k.fecha_movimiento DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas del kardex
     */
    public function obtenerEstadisticas($producto_id = null) {
        $where = $producto_id ? "WHERE producto_id = ?" : "";
        
        $sql = "SELECT 
                    SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END) as total_salidas,
                    COUNT(*) as total_movimientos
                FROM kardex $where";
        
        $stmt = $this->db->prepare($sql);
        if ($producto_id) {
            $stmt->execute([$producto_id]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Eliminar un movimiento y revertir el stock
     */
    public function eliminarMovimiento($id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener datos del movimiento antes de eliminar
            $sql = "SELECT k.producto_id, k.tipo_movimiento, k.cantidad, p.stock, p.nombre 
                    FROM kardex k 
                    INNER JOIN productos p ON k.producto_id = p.id 
                    WHERE k.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$movimiento) {
                throw new Exception("Movimiento no encontrado");
            }
            
            // Validar que al revertir no quede stock negativo
            if ($movimiento['tipo_movimiento'] == 'ENTRADA') {
                $nuevo_stock = $movimiento['stock'] - $movimiento['cantidad'];
                if ($nuevo_stock < 0) {
                    throw new Exception("No se puede eliminar: el stock quedaría negativo. Stock actual: " . 
                                      $movimiento['stock'] . ", cantidad a revertir: " . $movimiento['cantidad']);
                }
            }
            
            // Revertir el movimiento en el stock
            $operacion = ($movimiento['tipo_movimiento'] == 'ENTRADA') ? '-' : '+';
            $sql_update = "UPDATE productos SET stock = stock $operacion ? WHERE id = ?";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([$movimiento['cantidad'], $movimiento['producto_id']]);
            
            // Eliminar el movimiento
            $sql_delete = "DELETE FROM kardex WHERE id = ?";
            $stmt_delete = $this->db->prepare($sql_delete);
            $stmt_delete->execute([$id]);
            
            $this->db->commit();
            
            return [
                'success' => true, 
                'message' => 'Movimiento eliminado y stock revertido correctamente',
                'producto' => $movimiento['nombre']
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar movimiento: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener información de un producto
     */
    public function obtenerProducto($producto_id) {
        $sql = "SELECT id, nombre, stock FROM productos WHERE id = ? AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$producto_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener lista de productos activos
     */
    public function obtenerProductosActivos() {
        $sql = "SELECT id, nombre, stock FROM productos WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validar datos del movimiento
     */
    public function validarDatos($datos) {
        $errores = [];
        
        if (empty($datos['producto_id']) || !is_numeric($datos['producto_id'])) {
            $errores[] = "Debe seleccionar un producto válido";
        }
        
        if (empty($datos['tipo_movimiento']) || !in_array($datos['tipo_movimiento'], ['ENTRADA', 'SALIDA'])) {
            $errores[] = "Debe seleccionar un tipo de movimiento válido";
        }
        
        if (empty($datos['cantidad']) || !is_numeric($datos['cantidad']) || $datos['cantidad'] <= 0) {
            $errores[] = "La cantidad debe ser mayor a 0";
        }
        
        return $errores;
    }
    
    /**
     * Obtener resumen por producto
     */
    public function obtenerResumenPorProducto() {
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.stock,
                    COALESCE(SUM(CASE WHEN k.tipo_movimiento = 'ENTRADA' THEN k.cantidad ELSE 0 END), 0) as total_entradas,
                    COALESCE(SUM(CASE WHEN k.tipo_movimiento = 'SALIDA' THEN k.cantidad ELSE 0 END), 0) as total_salidas,
                    COALESCE(COUNT(k.id), 0) as total_movimientos
                FROM productos p
                LEFT JOIN kardex k ON p.id = k.producto_id
                WHERE p.activo = 1
                GROUP BY p.id, p.nombre, p.stock
                ORDER BY total_movimientos DESC
                LIMIT 10";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>