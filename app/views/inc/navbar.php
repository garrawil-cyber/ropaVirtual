<?php
// Obtener datos de sesión con valores por defecto
$nombre_usuario = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario');
$apellido_usuario = htmlspecialchars($_SESSION['apellido'] ?? '');
$foto = !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'default.png';
$foto_url = APP_URL . 'app/views/fotos/' . $foto;

// ============================================
// DETECTAR PÁGINA ACTIVA
// ============================================
$current_page = '';
$request_uri = $_SERVER['REQUEST_URI'] ?? '';

// Extraer la página actual de la URL
if (preg_match('/\/([^\/]+)\/?$/', $request_uri, $matches)) {
    $current_page = $matches[1];
}

// Función para verificar si un menú está activo
function isActive($page_names) {
    global $current_page;
    if (is_array($page_names)) {
        return in_array($current_page, $page_names) ? 'active' : '';
    }
    return $current_page === $page_names ? 'active' : '';
}

// Función para verificar si un dropdown debe estar expandido
function isDropdownActive($page_names) {
    global $current_page;
    return in_array($current_page, $page_names);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php 
    $head_path = __DIR__ . '/head.php';
    if (file_exists($head_path)) {
        include $head_path;
    }
    ?>
    
  <link rel="stylesheet" href="<?= APP_URL ?>app/views/css/navbar.css">

</head>
<body>

<!-- Debug Info - Remover en producción -->
<div class="debug-info">
    Página actual: <strong><?php echo htmlspecialchars($current_page); ?></strong>
</div>

<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid navbar-container">
            <a class="navbar-brand" href="<?php echo ADMIN_URL; ?>dashboard/">
                <i class="fas fa-store"></i>
                <span>Panel Admin</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive(['userList', 'userNew', 'userUpdate']); ?>" 
                           href="<?php echo ADMIN_URL; ?>userList/">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive(['clientList', 'clientNew', 'clientUpdate']); ?>" 
                           href="<?php echo ADMIN_URL; ?>clientList/">
                            <i class="fas fa-user-tie"></i>
                            <span>Clientes</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive(['categoryList', 'categoryNew', 'categoryUpdate']); ?>" 
                           href="<?php echo ADMIN_URL; ?>categoryList/">
                            <i class="fas fa-tags"></i>
                            <span>Categorías</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown <?php echo isDropdownActive(['productList', 'productNew', 'productUpdate', 'kardex', 'reportProducts']) ? 'active' : ''; ?>">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarProductos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-box"></i>
                            <span>Productos</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarProductos">
                            <li>
                                <a class="dropdown-item <?php echo isActive(['productList', 'productNew', 'productUpdate']); ?>" 
                                   href="<?php echo ADMIN_URL; ?>productList/">
                                    <i class="fas fa-list text-primary"></i>
                                    Listar Productos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo isActive('kardex'); ?>" 
                                   href="<?php echo ADMIN_URL; ?>kardexProductos/">
                                    <i class="fas fa-clipboard-list text-success"></i>
                                    Kardex de Productos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo isActive('reportProducts'); ?>" 
                                   href="<?php echo ADMIN_URL; ?>reportProducts/">
                                    <i class="fas fa-chart-bar text-warning"></i>
                                    Reporte de Productos
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown <?php echo isDropdownActive(['orderList', 'orderNew', 'orderUpdate', 'sale']) ? 'active' : ''; ?>">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarVentas" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Ventas</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarVentas">
                            <li>
                                <a class="dropdown-item <?php echo isActive(['orderList', 'orderUpdate']); ?>" 
                                   href="<?php echo ADMIN_URL; ?>orderList/">
                                    <i class="fas fa-receipt text-info"></i>
                                    Listar Pedidos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo isActive('orderNew'); ?>" 
                                   href="<?php echo ADMIN_URL; ?>orderNew/">
                                    <i class="fas fa-plus-circle text-success"></i>
                                    Generar Pedido
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo isActive('sale'); ?>" 
                                   href="<?php echo ADMIN_URL; ?>saleList/">
                                    <i class="fas fa-cash-register text-primary"></i>
                                    Realizar Ventas
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
              
                <div class="user-profile">
                    <div class="user-info" id="userProfileTrigger">
                        <?php if ($foto != 'default.png'): ?>
                            <img src="<?php echo $foto_url; ?>" 
                                 alt="Foto perfil" 
                                 class="profile-image"
                                 onerror="this.src='<?php echo APP_URL; ?>app/views/fotos/default.png'">
                        <?php else: ?>
                            <div class="profile-image" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="color: white;"></i>
                            </div>
                        <?php endif; ?>
                        <span class="user-name"><?php echo $nombre_usuario; ?></span>
                        <i class="fas fa-chevron-down user-chevron"></i>
                    </div>
                    
                    <div class="user-menu" id="userMenu">
                        <div class="user-menu-header">
                            <img src="<?php echo $foto_url; ?>" 
                                 alt="Foto de perfil" 
                                 onerror="this.src='<?php echo APP_URL; ?>app/views/fotos/default.png'">
                            <h6><?php echo $nombre_usuario . ' ' . $apellido_usuario; ?></h6>
                            <small>Administrador del Sistema</small>
                        </div>
                        <div class="user-menu-items">
                            <a href="<?php echo ADMIN_URL; ?>perfil/" class="user-menu-item">
                                <i class="fas fa-user-cog"></i>
                                Mi Perfil
                            </a>
                            
                            <a href="javascript:void(0);" onclick="confirmarCerrarSesion()" class="user-menu-item text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
// Esperar a que Bootstrap esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar dropdowns de Bootstrap manualmente
    const initializeDropdowns = () => {
        // Verificar si Bootstrap está cargado
        if (typeof bootstrap === 'undefined') {
            setTimeout(initializeDropdowns, 100);
            return;
        }

        // Inicializar todos los dropdowns
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        dropdownElementList.forEach(dropdownToggle => {
            new bootstrap.Dropdown(dropdownToggle, {
                autoClose: true
            });
        });

        // Agregar evento click manual para asegurar funcionamiento
        dropdownElementList.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const menu = this.nextElementSibling;
                const isShown = menu.classList.contains('show');
                
                // Cerrar todos los otros menús
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    if (m !== menu) {
                        m.classList.remove('show');
                    }
                });
                
                // Toggle del menú actual
                menu.classList.toggle('show', !isShown);
            });
        });
    };

    // Iniciar dropdowns
    initializeDropdowns();

    // Manejo del menú de usuario
    const userProfileTrigger = document.getElementById('userProfileTrigger');
    const userMenu = document.getElementById('userMenu');
    
    if (userProfileTrigger && userMenu) {
        const chevronIcon = userProfileTrigger.querySelector('.user-chevron');
        let isMenuOpen = false;

        const toggleMenu = () => {
            isMenuOpen = !isMenuOpen;
            userMenu.classList.toggle('active', isMenuOpen);
            if (chevronIcon) {
                chevronIcon.style.transform = isMenuOpen ? 'rotate(180deg)' : 'rotate(0)';
            }
        };

        userProfileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userProfileTrigger.contains(e.target)) {
                if (isMenuOpen) {
                    isMenuOpen = false;
                    userMenu.classList.remove('active');
                    if (chevronIcon) {
                        chevronIcon.style.transform = 'rotate(0)';
                    }
                }
            }
        });

        // Cerrar menú al hacer clic en un item
        userMenu.querySelectorAll('.user-menu-item').forEach(item => {
            item.addEventListener('click', () => {
                if (!item.getAttribute('onclick')) {
                    isMenuOpen = false;
                    userMenu.classList.remove('active');
                    if (chevronIcon) {
                        chevronIcon.style.transform = 'rotate(0)';
                    }
                }
            });
        });
    }

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // Console log para debug - mostrar página activa
    console.log('📍 Página actual detectada:', '<?php echo $current_page; ?>');
});

function confirmarCerrarSesion() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: 'Serás redirigido a la página de inicio de sesión',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#ef4444',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Sí, cerrar sesión',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
        allowOutsideClick: true, // Cambiado a true para mejor UX
        reverseButtons: true, // Mejor orden visual (Cancelar - Aceptar)
        focusCancel: true, // Enfocar el botón cancelar por defecto

    }).then((result) => {
        if (result.isConfirmed) {
            // Usuario confirmó - cerrar sesión
            Swal.fire({
                title: 'Cerrando sesión...',
                html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                text: 'Hasta pronto',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                allowOutsideClick: false,
                customClass: {
                    popup: 'animate-in'
                },
                willClose: () => {
                    window.location.href = "<?php echo APP_URL; ?>logOut/";
                }
            });
        } else {
            // Usuario canceló - simplemente cerrar el modal
            console.log('Cierre de sesión cancelado');
        }
    });
}

</script>

<main>
    <section>