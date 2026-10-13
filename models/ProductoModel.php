<?php
/**
 * Modelo para manejar operaciones de productos
 * Ruta: models/ProductoModel.php
 */

class ProductoModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Obtener productos con filtros opcionales
     */
    public function obtenerProductos($categoria_id = null, $busqueda = '', $limit = null) {
        try {
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                    FROM productos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
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
            
            // Límite opcional
            if ($limit) {
                $sql .= " LIMIT ?";
                $params[] = $limit;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerProductos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                    FROM productos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
                    WHERE p.id = ? AND p.activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener productos destacados
     */
    public function obtenerDestacados($limit = 8) {
        try {
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                    FROM productos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
                    WHERE p.destacado = 1 AND p.activo = 1 AND c.activo = 1
                    ORDER BY p.fecha_creacion DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerDestacados: " . $e->getMessage());
            return [];
        }
    }
}
?>