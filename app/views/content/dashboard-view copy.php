<?php
// Verificar si el usuario está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['usuario']) || empty($_SESSION['id']) || empty($_SESSION['usuario'])) {
    echo "<script>
        window.location.href='".APP_URL."login/';
    </script>";
    exit();
}
?>

<?php require_once APP_PATH . "views/inc/head.php"; ?>

<!-- Navegación Principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo ADMIN_URL; ?>dashboard/">
            <i class="fas fa-shield-alt me-2"></i>
            <span class="fw-bold"><?php echo APP_NAME; ?></span>
            <span class="badge bg-light text-primary ms-2 small">Admin</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo ADMIN_URL; ?>dashboard/">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-1"></i> Gestión de Usuarios
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="<?php echo ADMIN_URL; ?>userNew/">
                                <i class="fas fa-user-plus text-success me-2"></i>Nuevo Usuario
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo ADMIN_URL; ?>userList/">
                                <i class="fas fa-list text-primary me-2"></i>Lista de Usuarios
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo ADMIN_URL; ?>userSearch/">
                                <i class="fas fa-search text-info me-2"></i>Buscar Usuario
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="d-none d-md-inline"><?php echo $_SESSION['usuario']; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted d-block">Sesión iniciada como</small>
                            <strong><?php echo $_SESSION['usuario']; ?></strong>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo ADMIN_URL; ?>userPhoto/">
                                <i class="fas fa-camera text-primary me-2"></i>Mi Foto de Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form class="FormularioAjax" action="<?php echo ADMIN_URL; ?>logOut/" method="POST">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container-fluid px-4 py-5">
    
    <!-- Encabezado del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-tachometer-alt text-primary me-2"></i>Panel de Control
            </h1>
            <p class="text-muted mb-0">Bienvenido/a de nuevo, <?php echo $_SESSION['usuario']; ?></p>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-4 mb-4">
        <!-- Tarjeta Usuarios -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-primary fw-bold small mb-1">
                                Total Usuarios
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800">---</div>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i>Gestionar
                            </small>
                        </div>
                        <div class="icon-circle bg-primary-soft">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo ADMIN_URL; ?>userList/" class="btn btn-sm btn-outline-primary w-100">
                        Ver usuarios <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta Productos -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-success fw-bold small mb-1">
                                Productos
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800">---</div>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i>En catálogo
                            </small>
                        </div>
                        <div class="icon-circle bg-success-soft">
                            <i class="fas fa-box text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="btn btn-sm btn-outline-success w-100">
                        Ver productos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta Categorías -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-info fw-bold small mb-1">
                                Categorías
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800">---</div>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i>Activas
                            </small>
                        </div>
                        <div class="icon-circle bg-info-soft">
                            <i class="fas fa-tags text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="btn btn-sm btn-outline-info w-100">
                        Ver categorías <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta Reportes -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-warning fw-bold small mb-1">
                                Reportes
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800">---</div>
                            <small class="text-muted">
                                <i class="fas fa-chart-line me-1"></i>Disponibles
                            </small>
                        </div>
                        <div class="icon-circle bg-warning-soft">
                            <i class="fas fa-chart-bar text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="#" class="btn btn-sm btn-outline-warning w-100">
                        Ver reportes <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones Rápidas -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-bolt text-warning me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?php echo ADMIN_URL; ?>userNew/" class="btn btn-outline-primary w-100 py-3 quick-action-btn">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <div class="fw-bold">Nuevo Usuario</div>
                                <small class="text-muted">Registrar usuario</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo ADMIN_URL; ?>userSearch/" class="btn btn-outline-info w-100 py-3 quick-action-btn">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <div class="fw-bold">Buscar Usuario</div>
                                <small class="text-muted">Buscar y filtrar</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo ADMIN_URL; ?>userList/" class="btn btn-outline-success w-100 py-3 quick-action-btn">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <div class="fw-bold">Ver Todos</div>
                                <small class="text-muted">Lista completa</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-info me-2"></i>Información del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-server text-muted me-2"></i>Versión</span>
                            <span class="badge bg-primary">1.0.0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-clock text-muted me-2"></i>Estado</span>
                            <span class="badge bg-success">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-calendar text-muted me-2"></i>Fecha</span>
                            <span class="text-muted small"><?php echo date('d/m/Y'); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Gradiente para navbar */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Avatar circular */
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

/* Tarjetas con efecto hover */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Iconos circulares */
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary-soft {
    background-color: rgba(78, 115, 223, 0.1);
}

.bg-success-soft {
    background-color: rgba(28, 200, 138, 0.1);
}

.bg-info-soft {
    background-color: rgba(54, 185, 204, 0.1);
}

.bg-warning-soft {
    background-color: rgba(246, 194, 62, 0.1);
}

/* Botones de acción rápida */
.quick-action-btn {
    border: 2px dashed;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    border-style: solid;
    transform: scale(1.05);
}

/* Cards personalizadas */
.stats-card {
    border-radius: 10px;
    overflow: hidden;
}

/* Colores de texto personalizados */
.text-gray-800 {
    color: #5a5c69;
}

/* Navegación activa */
.nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
}

/* Dropdown mejorado */
.dropdown-menu {
    border: none;
    border-radius: 10px;
}

.dropdown-item {
    padding: 0.7rem 1.5rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .icon-circle {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .h4 {
        font-size: 1.3rem;
    }
}
</style>

<?php require_once APP_PATH . "views/inc/footer.php"; ?>