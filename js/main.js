// ===================================
// CARRITO MODAL - VERSIÓN CORREGIDA
// ===================================

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Botones de eliminar individual
    document.querySelectorAll('.item-remove').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const key = this.getAttribute('data-key');
            if (confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'key';
                input.value = key;
                
                const submit = document.createElement('input');
                submit.type = 'hidden';
                submit.name = 'eliminar_carrito';
                
                form.appendChild(input);
                form.appendChild(submit);
                document.body.appendChild(form);
                form.submit();
            }
        })
    });
    
    // ===================================
    // CONFIGURAR BOTÓN DEL CARRITO
    // ===================================
    const cartToggle = document.getElementById('cartToggle');
    if (cartToggle) {
        // Remover cualquier evento anterior
        cartToggle.removeAttribute('onclick');
        
        // Agregar nuevo evento
        cartToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleCarrito();
        });
        
        console.log('✅ Botón del carrito configurado');
    } else {
        console.error('❌ Botón del carrito no encontrado');
    }
    
    // ===================================
    // CERRAR MODAL CON OVERLAY
    // ===================================
    const overlay = document.getElementById('carritoOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleCarrito();
        });
        console.log('✅ Overlay configurado');
    }
    
    // ===================================
    // CERRAR MODAL CON BOTÓN X - CORREGIDO
    // ===================================
    const closeButtons = document.querySelectorAll('.carrito-close-modern'); // CAMBIADO: .carrito-close a .carrito-close-modern
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleCarrito();
        });
    });
    
    if (closeButtons.length > 0) {
        console.log('✅ Botones de cerrar configurados:', closeButtons.length);
    } else {
        console.log('ℹ️ No se encontraron botones .carrito-close-modern');
    }
    
    // ===================================
    // CERRAR CON TECLA ESC
    // ===================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('carritoModal');
            if (modal && modal.classList.contains('active')) {
                toggleCarrito();
            }
        }
    });
    
    // ===================================
    // VERIFICAR BADGE DEL CARRITO
    // ===================================
    const badge = document.getElementById('cartBadge');
    if (badge) {
        console.log('✅ Badge encontrado:', {
            text: badge.textContent,
            display: window.getComputedStyle(badge).display,
            visibility: window.getComputedStyle(badge).visibility
        });
    }
    
    // ===================================
    // ANIMACIONES AL SCROLL
    // ===================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar elementos animables
    const elements = document.querySelectorAll('.product-card, .category-card');
    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(el);
    });
    
    // ===================================
    // VALIDACIÓN DE CANTIDAD
    // ===================================
    const cantidadInputs = document.querySelectorAll('.input-cantidad');
    cantidadInputs.forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.max);
            const min = parseInt(this.min) || 0;
            let value = parseInt(this.value);
            
            if (value > max) this.value = max;
            if (value < min) this.value = min;
        });
    });
    
    // ===================================
    // BÚSQUEDA EN TIEMPO REAL
    // ===================================
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const value = this.value.trim();
            
            if (value.length >= 3) {
                searchTimeout = setTimeout(() => {
                    console.log('🔍 Buscando:', value);
                }, 500);
            }
        });
    }
    
    // ===================================
    // MENÚ MÓVIL
    // ===================================
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileCategories = document.getElementById('mobileCategories');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    
    if (mobileMenuToggle && mobileCategories && mobileOverlay) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileCategories.classList.add('active');
            mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        const closeMobile = function() {
            mobileCategories.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        };
        
        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', closeMobile);
        }
        
        mobileOverlay.addEventListener('click', closeMobile);
    }
    
    // ===================================
    // CONFIRMACIONES
    // ===================================
    document.querySelectorAll('form[name="vaciar_carrito"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de vaciar el carrito?')) {
                e.preventDefault();
            }
        });
    });
    
    // ===================================
    // LAZY LOADING DE IMÁGENES
    // ===================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('🛍️ Boutique Sixta - Sistema cargado correctamente');
});

// ===================================
// FUNCIONES GLOBALES
// ===================================

// Validar cantidad en formularios
function validarCantidad(form, stockMaximo) {
    const cantidad = parseInt(form.cantidad.value);
    if (cantidad < 1) {
        alert('La cantidad debe ser al menos 1');
        return false;
    }
    if (cantidad > stockMaximo) {
        alert('No hay suficiente stock disponible');
        form.cantidad.value = stockMaximo;
        return false;
    }
    return true;
}

// Validar checkout
function validarCheckout() {
    const carritoVacio = document.querySelectorAll('.carrito-item-modern').length === 0;
    if (carritoVacio) {
        alert('Tu carrito está vacío');
        return false;
    }
    return true;
}

// Función para abrir/cerrar el carrito
function toggleCarrito() {
    const modal = document.getElementById('carritoModal');
    const overlay = document.getElementById('carritoOverlay');
    
    if (modal && modal.classList.contains('active')) {
        // Cerrar
        modal.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
        console.log('🔒 Carrito cerrado');
    } else {
        // Abrir
        if (modal) modal.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log('🔓 Carrito abierto');
    }
}

// Función específica para cerrar
function cerrarCarrito() {
    const modal = document.getElementById('carritoModal');
    const overlay = document.getElementById('carritoOverlay');
    
    if (modal) modal.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
    console.log('🔒 Carrito cerrado (función específica)');
}

// Función específica para abrir
function abrirCarrito() {
    const modal = document.getElementById('carritoModal');
    const overlay = document.getElementById('carritoOverlay');
    
    if (modal) modal.classList.add('active');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    console.log('🔓 Carrito abierto (función específica)');
}

// Actualizar contador del carrito
function actualizarContadorCarrito(cantidad) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = cantidad;
        badge.classList.add('pulse');
        setTimeout(() => badge.classList.remove('pulse'), 600);
    }
}

// Mostrar notificación
function mostrarNotificacion(mensaje, tipo = 'success') {
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.textContent = mensaje;
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${tipo === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.3s;
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.animation = 'fadeOut 0.3s';
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

// Formatear precio
function formatearPrecio(precio) {
    return 'S/ ' + Number(precio).toFixed(2);
}

// Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});