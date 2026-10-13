</main>

<!-- Footer Elegante y Moderno -->
<footer class="footer-elegant">
    <div class="footer-container">
        <!-- Sección superior del footer -->
        <div class="footer-top">
            <div class="footer-grid">
                <!-- Columna 1: Brand -->
                <div class="footer-column">
                    <div class="footer-brand">
                        <div class="brand-logo">
                            <svg viewBox="0 0 40 40" fill="currentColor">
                                <path d="M20 5c-8.284 0-15 6.716-15 15 0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15 0-8.284-6.716-15-15-15zm0 2c7.18 0 13 5.82 13 13s-5.82 13-13 13-13-5.82-13-13 5.82-13 13-13zm-2 5v11l7-5.5-7-5.5z"/>
                            </svg>
                        </div>
                        <div class="brand-text">
                            <h3>Boutique Sixta</h3>
                            <p class="brand-tagline">Elegancia en cada detalle</p>
                        </div>
                    </div>
                    <p class="footer-description">
                        Tu destino de moda y estilo en Coveñas. Descubre las últimas tendencias 
                        con calidad premium y precios accesibles. Viste con elegancia y personalidad.
                    </p>
                    <div class="footer-badges">
                        <div class="badge-item">
                            <span class="badge-icon">🚚</span>
                            <span class="badge-text">Envío Gratis</span>
                        </div>
                        <div class="badge-item">
                            <span class="badge-icon">💳</span>
                            <span class="badge-text">Pago Seguro</span>
                        </div>
                    </div>
                </div>
                
                <!-- Columna 2: Navegación -->
                <div class="footer-column">
                    <h4 class="footer-title">Navegación</h4>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>index.php" class="footer-link">Inicio</a></li>
                        <li><a href="<?= APP_URL ?>index.php#products" class="footer-link">Productos</a></li>
                        <li><a href="<?= APP_URL ?>cuenta.php" class="footer-link">Mi Cuenta</a></li>
                        <li><a href="#" class="footer-link" onclick="abrirCarrito(); return false;">Carrito</a></li>
                    </ul>
                </div>
                
                <!-- Columna 3: Categorías -->
                <div class="footer-column">
                    <h4 class="footer-title">Categorías</h4>
                    <ul class="footer-links">
                        <?php if (isset($categorias) && !empty($categorias)): ?>
                            <?php foreach (array_slice($categorias, 0, 4) as $categoria): ?>
                                <li>
                                    <a href="<?= APP_URL ?>index.php?categoria=<?= $categoria['id'] ?>" class="footer-link">
                                        <?= htmlspecialchars($categoria['nombre']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?= APP_URL ?>index.php?categoria=1" class="footer-link">Vestidos</a></li>
                            <li><a href="<?= APP_URL ?>index.php?categoria=2" class="footer-link">Blusas</a></li>
                            <li><a href="<?= APP_URL ?>index.php?categoria=3" class="footer-link">Pantalones</a></li>
                            <li><a href="<?= APP_URL ?>index.php?categoria=4" class="footer-link">Accesorios</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Columna 4: Contacto -->
                <div class="footer-column">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-contact">
                        <li class="contact-item">
                            <span class="contact-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </span>
                            <div class="contact-info">
                                <span>Urb. Santa Margarita, Piura, Peru</span>
                            </div>
                        </li>
                        <li class="contact-item">
                            <span class="contact-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                            </span>
                            <div class="contact-info">
                                <span>contacto@boutiquesixta.com</span>
                            </div>
                        </li>
                        <li class="contact-item">
                            <span class="contact-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57-.35-.11-.74-.03-1.02.24l-2.2 2.2c-2.83-1.44-5.15-3.75-6.59-6.59l2.2-2.21c.28-.26.36-.65.25-1C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/>
                                </svg>
                            </span>
                            <div class="contact-info">
                                <span>+51 914 914 644</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Sección inferior del footer -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; <?= date('Y') ?> Boutique Sixta Coveñas. Todos los derechos reservados.</p>
                    <div class="footer-legal">
                        <a href="#" class="legal-link" onclick="mostrarTerminos(); return false;">Términos y Condiciones</a>
                        <span class="separator">•</span>
                        <a href="#" class="legal-link" onclick="mostrarPrivacidad(); return false;">Política de Privacidad</a>
                    </div>
                </div>
                
                <div class="footer-social">
                    <div class="social-links">
                        <a href="https://facebook.com" target="_blank" class="social-link" title="Facebook" aria-label="Síguenos en Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="social-link" title="Instagram" aria-label="Síguenos en Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <circle cx="17.5" cy="6.5" r="1.5"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/51939332728" target="_blank" class="social-link" title="WhatsApp" aria-label="Contáctanos por WhatsApp">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                        <a href="https://twitter.com" target="_blank" class="social-link" title="Twitter" aria-label="Síguenos en Twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botón scroll to top -->
    <button class="scroll-to-top" onclick="scrollToTop()" aria-label="Volver arriba">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    </button>
</footer>

<!-- CSS del Footer -->
<link rel="stylesheet" href="<?= APP_URL ?>css/footer.css">

<script>
// Función para abrir el modal del carrito
function abrirCarrito() {
    const modal = document.getElementById('carritoModal');
    const overlay = document.getElementById('carritoOverlay');
    
    if (modal && overlay) {
        modal.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}



// Scroll to top functionality mejorado
let scrollTimer;
window.addEventListener('scroll', function() {
    const scrollBtn = document.querySelector('.scroll-to-top');
    if (!scrollBtn) return;
    
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    }, 50);
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Funciones para modales de términos y privacidad
function mostrarTerminos() {
    alert('Términos y Condiciones:\n\n' +
          '1. Uso del sitio web\n' +
          '2. Política de devoluciones (30 días)\n' +
          '3. Garantía de productos\n' +
          '4. Métodos de pago aceptados\n\n' +
          'Para más información, contáctanos.');
}

function mostrarPrivacidad() {
    alert('Política de Privacidad:\n\n' +
          'Protegemos tus datos personales.\n' +
          'No compartimos información con terceros.\n' +
          'Usamos cookies para mejorar tu experiencia.\n\n' +
          'Para más detalles, contáctanos.');
}

// Prevenir envío de formulario en enlaces de términos
document.addEventListener('DOMContentLoaded', function() {
    // Animación de aparición del footer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const footerObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const footer = document.querySelector('.footer-elegant');
    if (footer) {
        footer.style.opacity = '0';
        footer.style.transform = 'translateY(30px)';
        footer.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        footerObserver.observe(footer);
    }
});
</script>

<!-- JavaScript principal -->
<script src="<?= APP_URL ?>js/main.js"></script>

</body>
</html>