<!-- ============================================
   MENÚ SUPERIOR PROFESIONAL - BOUTIQUE SIXTA COVEÑAS
   ============================================ -->
<div class="top-menu-professional">
    <div class="top-menu-wrapper">
        
        <!-- Navegación Principal -->
        <nav class="main-navigation">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🏠</span>
                        <span>Inicio</span>
                    </a>
                </li>
                
                <li class="nav-item dropdown-categoria">
                    <a href="#categories" class="nav-link">
                        <span class="nav-icon">👗</span>
                        <span>Categorías</span>
                        <span class="dropdown-arrow">▼</span>
                    </a>
                    <div class="categorias-dropdown">
                        <div class="categorias-dropdown-header">
                            <h4>Explora Nuestras Categorías</h4>
                            <a href="index.php" class="ver-todas">Ver Todas</a>
                        </div>
                        <div class="categorias-grid">
                            <?php
                            // Obtener categorías activas
                            if (isset($categorias) && !empty($categorias)):
                                foreach ($categorias as $cat):
                                    if ($cat['activo'] == 1):
                                        // Construir URL de imagen
                                        $imagen_url = APP_URL . 'app/views/img/categorias/' . $cat['imagen'];
                                        $cat_url = 'index.php?categoria=' . $cat['id'];
                            ?>
                                <a href="<?= $cat_url ?>" class="categoria-dropdown-item">
                                    <div class="categoria-icon-box">
                                        <img src="<?= htmlspecialchars($imagen_url) ?>" 
                                             alt="<?= htmlspecialchars($cat['nombre']) ?>" 
                                             class="categoria-imagen">
                                    </div>
                                    <div class="categoria-info">
                                        <span class="categoria-nombre"><?= htmlspecialchars($cat['nombre']) ?></span>
                                        <?php if (!empty($cat['descripcion'])): ?>
                                            <span class="categoria-desc"><?= htmlspecialchars(substr($cat['descripcion'], 0, 45)) ?>...</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php
                                    endif;
                                endforeach;
                            else:
                            ?>
                                <div class="no-categorias">
                                    <p>No hay categorías disponibles</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a href="<?= APP_URL ?>producto.php" class="nav-link">
                        <span class="nav-icon">🔥</span>
                        <span>Productos</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#beneficios" class="nav-link">
                        <span class="nav-icon">💎</span>
                        <span>Beneficios</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="contacto.php" class="nav-link">
                        <span class="nav-icon">📞</span>
                        <span>Contacto</span>
                    </a>
                </li>
            </ul>
        </nav>


    </div>
</div>

<!-- Menú Móvil Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>

<!-- Menú Móvil -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-header">
        <span class="mobile-brand">Boutique Sixta</span>
        <button class="mobile-close" onclick="closeMobileMenu()">×</button>
    </div>
    
    <div class="mobile-content">
        <nav class="mobile-nav">
            <a href="index.php" class="mobile-nav-item">
                <span class="nav-icon">🏠</span>
                <span>Inicio</span>
            </a>
            <a href="#categories" class="mobile-nav-item">
                <span class="nav-icon">👗</span>
                <span>Categorías</span>
            </a>
            
            <?php
            // Mostrar categorías en móvil
            if (isset($categorias) && !empty($categorias)):
                foreach ($categorias as $cat):
                    if ($cat['activo'] == 1):
                        $cat_url = 'index.php?categoria=' . $cat['id'];
            ?>
                <a href="<?= $cat_url ?>" class="mobile-nav-subitem">
                    <span class="nav-icon">▸</span>
                    <span><?= htmlspecialchars($cat['nombre']) ?></span>
                </a>
            <?php
                    endif;
                endforeach;
            endif;
            ?>
            <a href="#products" class="mobile-nav-item">
                <span class="nav-icon">🔥</span>
                <span>Productos</span>
            </a>
            <a href="#beneficios" class="mobile-nav-item">
                <span class="nav-icon">💎</span>
                <span>Beneficios</span>
            </a>
            <a href="#contacto" class="mobile-nav-item">
                <span class="nav-icon">📞</span>
                <span>Contacto</span>
            </a>
        </nav>

        <div class="mobile-search">
            <form action="index.php" method="GET">
                <input type="text" name="buscar" placeholder="Buscar..." class="mobile-search-input">
                <button type="submit" class="mobile-search-btn">Buscar</button>
            </form>
        </div>

        <div class="mobile-user">
            <?php if (isset($_SESSION['cliente_id'])): ?>
                <a href="mi_cuenta.php" class="mobile-user-link">Mi Cuenta</a>
                <a href="mis_pedidos.php" class="mobile-user-link">Mis Pedidos</a>
                <a href="logout.php" class="mobile-user-link logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="mobile-login-btn">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CSS INTEGRADO -->
<style>
/* ============================================
   MENÚ SUPERIOR PROFESIONAL
   ============================================ */
.top-menu-professional {
    background: #ffffff;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(232, 180, 188, 0.2);
}

.top-menu-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 75px;
    gap: 2rem;
}

/* ============================================
   BRAND/LOGO
   ============================================ */
.brand-section {
    flex-shrink: 0;
}

.brand-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.brand-link:hover {
    transform: scale(1.05);
}

.brand-icon {
    font-size: 2rem;
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.3);
}

.brand-name {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #e8b4bc, #c89595);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* ============================================
   NAVEGACIÓN PRINCIPAL
   ============================================ */
.main-navigation {
    flex: 1;
    display: flex;
    justify-content: center;
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.nav-item {
    position: relative;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    text-decoration: none;
    color: #3a3a3a;
    font-weight: 500;
    font-size: 0.95rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link .nav-icon {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.nav-link:hover {
    background: rgba(232, 180, 188, 0.15);
    color: #c89595;
}

.nav-link:hover .nav-icon {
    transform: scale(1.2);
}

.nav-link.active {
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    color: white;
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.4);
}

/* ============================================
   DROPDOWN DE CATEGORÍAS
   ============================================ */
.dropdown-categoria {
    position: relative;
}

.dropdown-arrow {
    font-size: 0.65rem;
    margin-left: 0.25rem;
    transition: transform 0.3s ease;
}

.dropdown-categoria:hover .dropdown-arrow {
    transform: rotate(180deg);
}

.categorias-dropdown {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    min-width: 550px;
    max-width: 650px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
    opacity: 0;
    visibility: hidden;
    transform: translateX(-50%) translateY(10px);
    transition: all 0.3s ease;
    z-index: 1000;
    padding: 2rem;
    margin-top: 0.75rem;
}

.dropdown-categoria:hover .categorias-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

.categorias-dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f9f5f6;
}

.categorias-dropdown-header h4 {
    margin: 0;
    color: #3a3a3a;
    font-size: 1.1rem;
    font-weight: 600;
}

.ver-todas {
    font-size: 0.85rem;
    color: #c89595;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.ver-todas:hover {
    color: #e8b4bc;
    text-decoration: underline;
}

.categorias-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    max-height: 450px;
    overflow-y: auto;
    padding-right: 5px;
}

.categorias-grid::-webkit-scrollbar {
    width: 8px;
}

.categorias-grid::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 10px;
}

.categorias-grid::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    border-radius: 10px;
}

.categorias-grid::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #d4a5a5, #c89595);
}

.categoria-dropdown-item {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    text-decoration: none;
    color: #3a3a3a;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #fafafa;
    border: 2px solid transparent;
}

.categoria-dropdown-item:hover {
    background: linear-gradient(135deg, rgba(232, 180, 188, 0.15), rgba(212, 165, 165, 0.15));
    transform: translateX(8px);
    box-shadow: 0 6px 20px rgba(232, 180, 188, 0.25);
    border-color: rgba(232, 180, 188, 0.3);
}

.categoria-icon-box {
    width: 60px;
    height: 60px;
    background: #ffffff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    padding: 4px;
    border: 2px solid rgba(232, 180, 188, 0.2);
}

.categoria-imagen {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}

.categoria-icon {
    font-size: 2rem;
}

.categoria-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
}

.categoria-nombre {
    font-size: 1rem;
    font-weight: 600;
    color: #2a2a2a;
    line-height: 1.3;
}

.categoria-desc {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.4;
    margin-top: 2px;
}

.no-categorias {
    grid-column: 1 / -1;
    text-align: center;
    padding: 2rem;
    color: #999;
}

/* ============================================
   ACCIONES DE USUARIO
   ============================================ */
.user-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
}

/* Búsqueda */
.search-box {
    position: relative;
}

.search-form {
    display: flex;
    position: relative;
}

.search-input {
    padding: 0.65rem 3rem 0.65rem 1.25rem;
    border: 2px solid #f9f5f6;
    border-radius: 25px;
    font-size: 0.9rem;
    width: 240px;
    background: #f9f5f6;
    color: #3a3a3a;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #e8b4bc;
    background: white;
    width: 280px;
    box-shadow: 0 0 0 3px rgba(232, 180, 188, 0.1);
}

.search-input::placeholder {
    color: #999;
}

.search-btn {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.search-btn:hover {
    color: #c89595;
    transform: translateY(-50%) scale(1.1);
}

/* Usuario */
.user-section {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: #f9f5f6;
    border-radius: 50%;
    border: none;
    color: #3a3a3a;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-btn:hover {
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.3);
}

.login-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    text-decoration: none;
    color: #3a3a3a;
    font-weight: 500;
    font-size: 0.9rem;
    border-radius: 25px;
    background: #f9f5f6;
    transition: all 0.3s ease;
}

.login-btn:hover {
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.3);
}

/* Dropdown Usuario */
.user-dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 0.75rem);
    right: 0;
    min-width: 200px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1001;
    padding: 0.5rem 0;
}

.user-dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    text-decoration: none;
    color: #3a3a3a;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f9f5f6;
    color: #c89595;
}

.dropdown-divider {
    height: 1px;
    background: #f9f5f6;
    margin: 0.5rem 0;
}

.logout-item {
    color: #e74c3c;
}

.logout-item:hover {
    background: rgba(231, 76, 60, 0.1);
    color: #c0392b;
}

/* ============================================
   MENÚ MÓVIL
   ============================================ */
.mobile-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.mobile-toggle span {
    display: block;
    width: 25px;
    height: 3px;
    background: #3a3a3a;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.mobile-menu-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1100;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mobile-menu-overlay.active {
    display: block;
    opacity: 1;
}

.mobile-menu {
    position: fixed;
    top: 0;
    right: -100%;
    width: 320px;
    max-width: 85%;
    height: 100vh;
    background: white;
    z-index: 1101;
    transition: right 0.3s ease;
    overflow-y: auto;
    box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
}

.mobile-menu.active {
    right: 0;
}

.mobile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #f9f5f6;
}

.mobile-brand {
    font-size: 1.3rem;
    font-weight: 700;
    background: linear-gradient(135deg, #e8b4bc, #c89595);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
}

.mobile-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #3a3a3a;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.mobile-close:hover {
    background: #f9f5f6;
}

.mobile-content {
    padding: 1rem;
}

.mobile-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.mobile-nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    text-decoration: none;
    color: #3a3a3a;
    font-weight: 500;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.mobile-nav-item:hover {
    background: #f9f5f6;
    color: #c89595;
}

.mobile-nav-subitem {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem 0.75rem 2rem;
    text-decoration: none;
    color: #666;
    font-weight: 400;
    font-size: 0.9rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.mobile-nav-subitem:hover {
    background: rgba(232, 180, 188, 0.1);
    color: #c89595;
    border-left-color: #e8b4bc;
    transform: translateX(5px);
}

.mobile-search {
    margin-bottom: 2rem;
}

.mobile-search form {
    display: flex;
    gap: 0.5rem;
}

.mobile-search-input {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #f9f5f6;
    border-radius: 8px;
    font-size: 0.9rem;
    background: #f9f5f6;
}

.mobile-search-input:focus {
    outline: none;
    border-color: #e8b4bc;
    background: white;
}

.mobile-search-btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mobile-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.4);
}

.mobile-user {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid #f9f5f6;
}

.mobile-user-link {
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: #3a3a3a;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.mobile-user-link:hover {
    background: #f9f5f6;
    color: #c89595;
}

.mobile-user-link.logout {
    color: #e74c3c;
}

.mobile-login-btn {
    display: block;
    padding: 0.75rem;
    text-align: center;
    text-decoration: none;
    color: white;
    background: linear-gradient(135deg, #e8b4bc, #d4a5a5);
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.mobile-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(232, 180, 188, 0.4);
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 1200px) {
    .nav-link {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    
    .search-input {
        width: 200px;
    }
}

@media (max-width: 992px) {
    .main-navigation {
        display: none;
    }
    
    .mobile-toggle {
        display: flex;
    }
    
    .search-input {
        width: 180px;
    }
}

@media (max-width: 768px) {
    .top-menu-wrapper {
        padding: 0 1rem;
        height: 65px;
    }
    
    .brand-name {
        display: none;
    }
    
    .search-input {
        width: 150px;
        padding: 0.5rem 2.5rem 0.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .search-box {
        display: none;
    }
    
    .user-actions {
        gap: 0.5rem;
    }
}
</style>

<!-- JAVASCRIPT -->
<script>
// Toggle menú móvil
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');
    
    mobileMenu.classList.toggle('active');
    overlay.classList.toggle('active');
    
    if (mobileMenu.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');
    
    mobileMenu.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Toggle dropdown usuario
function toggleUserMenu(event) {
    event.stopPropagation();
    const dropdown = event.currentTarget.nextElementSibling;
    dropdown.style.opacity = dropdown.style.opacity === '1' ? '0' : '1';
    dropdown.style.visibility = dropdown.style.visibility === 'visible' ? 'hidden' : 'visible';
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
            dropdown.style.opacity = '0';
            dropdown.style.visibility = 'hidden';
        });
    }
});

// Cerrar menú móvil al hacer clic en enlace
document.addEventListener('DOMContentLoaded', function() {
    const mobileLinks = document.querySelectorAll('.mobile-nav-item');
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (!this.classList.contains('has-submenu')) {
                closeMobileMenu();
            }
        });
    });
});

// Smooth scroll para enlaces internos
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});
</script>