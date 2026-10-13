<?php
// kardex_productos.php - Sistema de Kardex Mejorado

// Conexión a la base de datos
try {
    $host = 'localhost';
    $dbname = 'tiendaropa';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

class KardexProductos {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function registrarMovimiento($producto_id, $tipo_movimiento, $cantidad, $observaciones = '') {
        try {
            $this->db->beginTransaction();
            
            // Registrar en kardex
            $sql = "INSERT INTO kardex (producto_id, tipo_movimiento, cantidad, observaciones, fecha_movimiento) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$producto_id, $tipo_movimiento, $cantidad, $observaciones]);
            
            // Actualizar stock en tabla productos
            $operacion = ($tipo_movimiento == 'ENTRADA') ? '+' : '-';
            $sql_update = "UPDATE productos SET stock = stock $operacion ? WHERE id = ?";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([$cantidad, $producto_id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al registrar movimiento: " . $e->getMessage());
            return false;
        }
    }
    
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
    
    public function eliminarMovimiento($id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener datos del movimiento antes de eliminar
            $sql = "SELECT producto_id, tipo_movimiento, cantidad FROM kardex WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($movimiento) {
                // Revertir el movimiento en el stock
                $operacion = ($movimiento['tipo_movimiento'] == 'ENTRADA') ? '-' : '+';
                $sql_update = "UPDATE productos SET stock = stock $operacion ? WHERE id = ?";
                $stmt_update = $this->db->prepare($sql_update);
                $stmt_update->execute([$movimiento['cantidad'], $movimiento['producto_id']]);
                
                // Eliminar el movimiento
                $sql_delete = "DELETE FROM kardex WHERE id = ?";
                $stmt_delete = $this->db->prepare($sql_delete);
                $stmt_delete->execute([$id]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al eliminar movimiento: " . $e->getMessage());
            return false;
        }
    }
}

// Obtener lista de productos activos
try {
    $stmt = $db->query("SELECT id, nombre, stock FROM productos WHERE activo = 1 ORDER BY nombre");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
    error_log("Error al cargar productos: " . $e->getMessage());
}

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['success'])) {
    $mensaje = 'Operación realizada exitosamente';
    $tipo_mensaje = 'success';
} elseif (isset($_GET['error'])) {
    $mensaje = 'Error al procesar la operación';
    $tipo_mensaje = 'danger';
}

$kardex = new KardexProductos($db);
$estadisticas = $kardex->obtenerEstadisticas($_GET['producto_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex de Productos | Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            padding: 2rem 0;
        }
        
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .page-header h1 i {
            color: var(--primary-color);
            font-size: 2.5rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card.entradas .icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .stats-card.salidas .icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .stats-card.movimientos .icon {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
        }
        
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--dark-color);
        }
        
        .stats-card p {
            color: #6b7280;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .card-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 2rem;
        }
        
        .card-custom .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.25rem 1.5rem;
            border: none;
        }
        
        .card-custom .card-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-custom {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
            color: white;
        }
        
        .table-wrapper {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: #f9fafb;
            color: var(--dark-color);
            font-weight: 600;
            border: none;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f3f4f6;
        }
        
        .badge-entrada {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .badge-salida {
            background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .stock-highlight {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .stock-highlight h5 {
            color: var(--dark-color);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }
        
        .stock-highlight .stock-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid main-container">
        <!-- Encabezado -->
        <div class="page-header">
            <h1>
                <i class="fas fa-clipboard-list"></i>
                Kardex de Productos
            </h1>
        </div>

        <!-- Mensajes de alerta -->
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-custom alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="stats-card entradas">
                    <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <h3><?php echo number_format($estadisticas['total_entradas'] ?? 0); ?></h3>
                    <p>Total Entradas</p>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="stats-card salidas">
                    <div class="icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h3><?php echo number_format($estadisticas['total_salidas'] ?? 0); ?></h3>
                    <p>Total Salidas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card movimientos">
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3><?php echo number_format($estadisticas['total_movimientos'] ?? 0); ?></h3>
                    <p>Total Movimientos</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-custom">
            <div class="card-header">
                <h5><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="producto_id" class="form-label fw-bold">
                            <i class="fas fa-box me-2"></i>Producto
                        </label>
                        <select name="producto_id" id="producto_id" class="form-select">
                            <option value="">Todos los productos</option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?php echo $producto['id']; ?>" 
                                        <?php echo (isset($_GET['producto_id']) && $_GET['producto_id'] == $producto['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($producto['nombre']); ?> 
                                    (Stock: <?php echo $producto['stock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label fw-bold">
                            <i class="fas fa-calendar me-2"></i>Fecha Inicio
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                               value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label fw-bold">
                            <i class="fas fa-calendar me-2"></i>Fecha Fin
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                               value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-custom btn-custom w-100">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock actual (si hay producto seleccionado) -->
        <?php if (isset($_GET['producto_id']) && !empty($_GET['producto_id'])): 
            $stmt = $db->prepare("SELECT nombre, stock FROM productos WHERE id = ?");
            $stmt->execute([$_GET['producto_id']]);
            $producto_info = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="stock-highlight">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fas fa-box-open me-2"></i>Producto Seleccionado</h5>
                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($producto_info['nombre']); ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-muted d-block">Stock Actual</small>
                    <span class="stock-value"><?php echo number_format($producto_info['stock']); ?></span>
                    <small class="text-muted">unidades</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabla de movimientos -->
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Historial de Movimientos</h5>
                <button class="btn btn-success-custom btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#nuevoMovimientoModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Movimiento
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-wrapper">
                    <table id="tablaKardex" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>ID</th>
                                <th><i class="fas fa-calendar me-2"></i>Fecha</th>
                                <th><i class="fas fa-box me-2"></i>Producto</th>
                                <th><i class="fas fa-exchange-alt me-2"></i>Tipo</th>
                                <th><i class="fas fa-sort-numeric-up me-2"></i>Cantidad</th>
                                <th><i class="fas fa-warehouse me-2"></i>Stock Actual</th>
                                <th><i class="fas fa-comment me-2"></i>Observaciones</th>
                                <th class="text-center"><i class="fas fa-cog me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtener movimientos según filtros
                            if (isset($_GET['producto_id']) && !empty($_GET['producto_id'])) {
                                if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && 
                                    !empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
                                    $movimientos = $kardex->obtenerMovimientosPorFecha(
                                        $_GET['fecha_inicio'], 
                                        $_GET['fecha_fin'], 
                                        $_GET['producto_id']
                                    );
                                } else {
                                    $movimientos = $kardex->obtenerKardex($_GET['producto_id']);
                                }
                            } else {
                                if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && 
                                    !empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
                                    $movimientos = $kardex->obtenerMovimientosPorFecha(
                                        $_GET['fecha_inicio'], 
                                        $_GET['fecha_fin']
                                    );
                                } else {
                                    $movimientos = $kardex->obtenerKardex();
                                }
                            }
                            
                            if (empty($movimientos)):
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No se encontraron movimientos</p>
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($movimientos as $movimiento):
                                    $badge_class = $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'badge-entrada' : 'badge-salida';
                                    $icono = $movimiento['tipo_movimiento'] == 'ENTRADA' ? 'fa-arrow-down' : 'fa-arrow-up';
                            ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $movimiento['id']; ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($movimiento['fecha_movimiento'])); ?>
                                        </small><br>
                                        <small class="text-muted">
                                            <?php echo date('H:i', strtotime($movimiento['fecha_movimiento'])); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($movimiento['producto_nombre']); ?></td>
                                    <td>
                                        <span class="<?php echo $badge_class; ?>">
                                            <i class="fas <?php echo $icono; ?>"></i>
                                            <?php echo $movimiento['tipo_movimiento']; ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?php echo number_format($movimiento['cantidad']); ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo number_format($movimiento['stock_actual']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($movimiento['observaciones']) ?: '-'; ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger-custom btn-sm" 
                                                onclick="eliminarMovimiento(<?php echo $movimiento['id']; ?>)"
                                                title="Eliminar movimiento">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo movimiento -->
    <div class="modal fade" id="nuevoMovimientoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Movimiento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="procesar_movimiento.php" method="POST" id="formMovimiento">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="producto_id_modal" class="form-label fw-bold">
                                <i class="fas fa-box me-2"></i>Producto *
                            </label>
                            <select name="producto_id" id="producto_id_modal" class="form-select" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo $producto['id']; ?>">
                                        <?php echo htmlspecialchars($producto['nombre']); ?> 
                                        (Stock: <?php echo $producto['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_movimiento" class="form-label fw-bold">
                                <i class="fas fa-exchange-alt me-2"></i>Tipo de Movimiento *
                            </label>
                            <select name="tipo_movimiento" id="tipo_movimiento" class="form-select" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="ENTRADA">
                                    <i class="fas fa-arrow-down"></i> Entrada (Compra/Devolución)
                                </option>
                                <option value="SALIDA">
                                    <i class="fas fa-arrow-up"></i> Salida (Venta/Devolución)
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label fw-bold">
                                <i class="fas fa-sort-numeric-up me-2"></i>Cantidad *
                            </label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control" 
                                   min="1" step="1" required placeholder="Ingrese la cantidad">
                        </div>
                        <div class="mb-3">
                            <label for="observaciones" class="form-label fw-bold">
                                <i class="fas fa-comment me-2"></i>Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" class="form-control" 
                                      rows="3" placeholder="Motivo del movimiento (opcional)"></textarea>
                        </div>
                        <div class="alert alert-info alert-custom">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>El stock del producto se actualizará automáticamente según el tipo de movimiento.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success-custom btn-custom">
                            <i class="fas fa-save me-2"></i>Registrar Movimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tablaKardex').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
            
            // Validación del formulario
            $('#formMovimiento').on('submit', function(e) {
                const cantidad = parseInt($('#cantidad').val());
                const tipoMovimiento = $('#tipo_movimiento').val();
                const productoSelect = $('#producto_id_modal');
                const stockActual = parseInt(productoSelect.find(':selected').text().match(/\d+/)[0]);
                
                if (tipoMovimiento === 'SALIDA' && cantidad > stockActual) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock Insuficiente',
                        text: `La cantidad (${cantidad}) supera el stock disponible (${stockActual})`,
                        confirmButtonColor: '#ef4444'
                    });
                    return false;
                }
            });
        });
        
        function eliminarMovimiento(id) {
            Swal.fire({
                title: '¿Eliminar Movimiento?',
                text: "Esta acción revertirá el cambio en el stock del producto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'eliminar_movimiento.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>