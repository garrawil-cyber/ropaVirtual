<?php
// ropavirtual_mvc/app/admin/kardex_productos.php

// Conexión directa a la base de datos como alternativa
try {
    $host = 'localhost';
    $dbname = 'tiendaropa'; // Cambiar por el nombre real
    $username = 'root'; // Cambiar si es necesario
    $password = ''; // Cambiar si es necesario
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// El resto del código permanece igual...

//require_once dirname(__DIR__, 3) . "/config/database.php";

class KardexProductos {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Registrar movimiento en el kardex
    public function registrarMovimiento($producto_id, $tipo_movimiento, $cantidad, $observaciones = '') {
        $sql = "INSERT INTO kardex (producto_id, tipo_movimiento, cantidad, observaciones, fecha_movimiento) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$producto_id, $tipo_movimiento, $cantidad, $observaciones]);
    }
    
    // Obtener historial completo del kardex
    public function obtenerKardex($producto_id = null) {
        $sql = "SELECT k.*, p.nombre as producto_nombre 
                FROM kardex k 
                INNER JOIN productos p ON k.producto_id = p.id";
        
        if ($producto_id) {
            $sql .= " WHERE k.producto_id = ?";
            $sql .= " ORDER BY k.fecha_movimiento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$producto_id]);
        } else {
            $sql .= " ORDER BY k.fecha_movimiento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calcular stock actual
    public function calcularStockActual($producto_id) {
        $sql = "SELECT 
                SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad 
                         WHEN tipo_movimiento = 'SALIDA' THEN -cantidad 
                         ELSE 0 END) as stock_actual 
                FROM kardex 
                WHERE producto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$producto_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['stock_actual'] ?? 0;
    }
    
    // Obtener movimientos por rango de fechas
    public function obtenerMovimientosPorFecha($fecha_inicio, $fecha_fin, $producto_id = null) {
        $sql = "SELECT k.*, p.nombre as producto_nombre 
                FROM kardex k 
                INNER JOIN productos p ON k.producto_id = p.id 
                WHERE k.fecha_movimiento BETWEEN ? AND ?";
        
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
}

// Obtener lista de productos para los filtros
try {
    $stmt = $db->query("SELECT id, nombre FROM productos WHERE activo = 1 ORDER BY nombre");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
    error_log("Error al cargar productos: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <div class="container-fluid">
        <h1 class="my-4"><i class="fas fa-boxes"></i> Kardex de Productos</h1>
        
        <!-- Filtros -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="producto_id" class="form-label">Producto</label>
                        <select name="producto_id" id="producto_id" class="form-select">
                            <option value="">Todos los productos</option>
                            <?php
                            foreach ($productos as $producto) {
                                $selected = (isset($_GET['producto_id']) && $_GET['producto_id'] == $producto['id']) ? 'selected' : '';
                                echo "<option value='{$producto['id']}' $selected>{$producto['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                               value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                               value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resumen de Stock -->
        <?php
        if (isset($_GET['producto_id']) && !empty($_GET['producto_id'])) {
            $kardex = new KardexProductos($db);
            $stock_actual = $kardex->calcularStockActual($_GET['producto_id']);
            
            // Obtener nombre del producto
            $stmt = $db->prepare("SELECT nombre FROM productos WHERE id = ?");
            $stmt->execute([$_GET['producto_id']]);
            $producto_nombre = $stmt->fetchColumn();
            ?>
            <div class="stock-actual">
                <h5>Stock Actual: <span class="text-primary"><?php echo $stock_actual; ?></span> unidades</h5>
                <p class="mb-0">Producto: <strong><?php echo htmlspecialchars($producto_nombre); ?></strong></p>
            </div>
            <?php
        }
        ?>

        <!-- Tabla de Movimientos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Movimientos del Kardex</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#nuevoMovimientoModal">
                    <i class="fas fa-plus"></i> Nuevo Movimiento
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kardex = new KardexProductos($db);
                            
                            // Aplicar filtros
                            if (isset($_GET['producto_id']) && !empty($_GET['producto_id'])) {
                                if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && !empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
                                    $movimientos = $kardex->obtenerMovimientosPorFecha(
                                        $_GET['fecha_inicio'], 
                                        $_GET['fecha_fin'], 
                                        $_GET['producto_id']
                                    );
                                } else {
                                    $movimientos = $kardex->obtenerKardex($_GET['producto_id']);
                                }
                            } else {
                                if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && !empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
                                    $movimientos = $kardex->obtenerMovimientosPorFecha(
                                        $_GET['fecha_inicio'], 
                                        $_GET['fecha_fin']
                                    );
                                } else {
                                    $movimientos = $kardex->obtenerKardex();
                                }
                            }
                            
                            if (empty($movimientos)) {
                                echo '<tr><td colspan="6" class="text-center">No se encontraron movimientos</td></tr>';
                            } else {
                                foreach ($movimientos as $movimiento) {
                                    $clase_tipo = $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'entrada' : 'salida';
                                    $icono = $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'fa-arrow-down' : 'fa-arrow-up';
                                    echo "
                                    <tr>
                                        <td>" . date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) . "</td>
                                        <td>{$movimiento['producto_nombre']}</td>
                                        <td class='$clase_tipo'>
                                            <i class='fas $icono'></i> {$movimiento['tipo_movimiento']}
                                        </td>
                                        <td>{$movimiento['cantidad']}</td>
                                        <td>{$movimiento['observaciones']}</td>
                                        <td>
                                            <button class='btn btn-sm btn-outline-danger' onclick='eliminarMovimiento({$movimiento['id']})'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo movimiento -->
    <div class="modal fade" id="nuevoMovimientoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Movimiento en Kardex</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="procesar_movimiento.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="producto_id_modal" class="form-label">Producto *</label>
                            <select name="producto_id" id="producto_id_modal" class="form-select" required>
                                <option value="">Seleccionar producto</option>
                                <?php
                                foreach ($productos as $producto) {
                                    echo "<option value='{$producto['id']}'>{$producto['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_movimiento" class="form-label">Tipo de Movimiento *</label>
                            <select name="tipo_movimiento" id="tipo_movimiento" class="form-select" required>
                                <option value="ENTRADA">Entrada</option>
                                <option value="SALIDA">Salida</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad *</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function eliminarMovimiento(id) {
        if (confirm('¿Estás seguro de eliminar este movimiento?')) {
            window.location.href = 'eliminar_movimiento.php?id=' + id;
        }
    }
    </script>
</body>
</html>