<?php
/**
 * Modelo para manejar operaciones de categorías
 * Ruta: models/CategoriaModel.php
 */

class CategoriaModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Obtener todas las categorías activas
     */
    public function obtenerTodas() {
        try {
            $sql = "SELECT id, nombre, descripcion, imagen, activo 
                    FROM categorias 
                    WHERE activo = 1 
                    ORDER BY nombre ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en CategoriaModel::obtenerTodas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener categoría por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id, nombre, descripcion, imagen, activo 
                    FROM categorias 
                    WHERE id = ? AND activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en CategoriaModel::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
}
?>