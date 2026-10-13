<?php
/**
 * BOUTIQUE SIXTA COVEÑAS
 * Formulario de Contacto con PHPMailer
 * Archivo: contacto.php (Versión Optimizada)
 */

session_start();

// Incluir archivos de configuración
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'config/mail.php'; // Configuración de email

require_once 'config/whatsapp_simple.php'; // Nuevo
require_once 'config/sms_simple.php'; // Nuevo

// ========== FUNCIONES AUXILIARES (DEBEN IR AL PRINCIPIO) ==========

/**
 * Enviar email al administrador usando configuración centralizada
 */
function enviarEmailAdministrador($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id) {
    global $configMail;
    return $configMail->enviarEmailAdministrador($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id);
}

/**
 * Enviar email de confirmación al cliente
 */
function enviarEmailConfirmacion($nombre, $email_cliente, $asunto_tipo) {
    global $configMail;
    return $configMail->enviarEmailConfirmacion($nombre, $email_cliente, $asunto_tipo);
}

/**
 * Enviar notificaciones WhatsApp y SMS
 */
function enviarNotificacionesContacto($contacto_id, $nombre, $email, $telefono, $asunto) {
    global $whatsapp, $sms;
    
    $notificaciones_enviadas = [];
    
    // 1. WhatsApp al admin (si tiene número)
    if (!empty($telefono)) {
        $notificaciones_enviadas['whatsapp_admin'] = $whatsapp->notificarContactoAdmin(
            $contacto_id, 
            $nombre, 
            $telefono, 
            $asunto
        );
    }
    
    // 2. WhatsApp al cliente
    if (!empty($telefono)) {
        $notificaciones_enviadas['whatsapp_cliente'] = $whatsapp->notificarConfirmacionCliente(
            $nombre,
            $telefono,
            $asunto
        );
    }
    
    // 3. SMS al admin
    $notificaciones_enviadas['sms_admin'] = $sms->smsNotificacionAdmin(
        $contacto_id,
        $nombre,
        $telefono,
        $asunto
    );
    
    // 4. SMS al cliente (si proporcionó teléfono)
    if (!empty($telefono)) {
        $notificaciones_enviadas['sms_cliente'] = $sms->smsConfirmacionCliente(
            $nombre,
            $telefono,
            $asunto
        );
    }
    
    return $notificaciones_enviadas;
}

// Variables para mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_contacto'])) {
    
    // Sanitizar y validar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    
    // Validaciones
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = 'El nombre es obligatorio';
    }
    
    if (empty($email)) {
        $errores[] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido';
    }
    
    if (empty($asunto)) {
        $errores[] = 'El asunto es obligatorio';
    }
    
    if (empty($mensaje)) {
        $errores[] = 'El mensaje es obligatorio';
    } elseif (strlen($mensaje) < 10) {
        $errores[] = 'El mensaje debe tener al menos 10 caracteres';
    }
    
    // Si no hay errores, procesar
if (empty($errores)) {
    try {
        // 1. Guardar en base de datos
        $sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, fecha_envio, leido, ip_address) 
                VALUES (:nombre, :email, :telefono, :asunto, :mensaje, NOW(), 0, :ip)";
        // Verificar conexión
        if (!isset($db)) {
            throw new Exception("Error: No hay conexión a la base de datos");
        }
        $stmt = $db->prepare($sql);
        $resultado = $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':asunto' => $asunto,
            ':mensaje' => $mensaje,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'  
        ]);
            
    if ($resultado) {
            $contacto_id = $db->lastInsertId();
            
            // 2. Enviar emails usando PHPMailer
            $mail_enviado = enviarEmailAdministrador($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id);
            
            // 3. Enviar copia al cliente
            if ($mail_enviado) {
                enviarEmailConfirmacion($nombre, $email, $asunto);
            }

            // 4. ENVIAR NOTIFICACIONES WHATSAPP Y SMS (NUEVO)
            $notificaciones = enviarNotificacionesContacto($contacto_id, $nombre, $email, $telefono, $asunto);
            
            $mensaje_exito = '¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente. ';
            $mensaje_exito .= $mail_enviado 
                ? 'Te hemos enviado una copia a tu email.' 
                : 'Te responderemos pronto.';

                        // Agregar información de notificaciones
            if ($mail_enviado) {
                $mensaje_exito .= '📧 Te hemos enviado un email de confirmación. ';
            }
        
            if (!empty($telefono)) {
                $mensaje_exito .= '📱 También recibirás un mensaje por WhatsApp/SMS. ';
            }
            
            // 4. Limpiar variables
            $nombre = $email = $telefono = $asunto = $mensaje = '';
        } else {
            throw new Exception("Error al ejecutar la consulta");
        }
        
    } catch (PDOException $e) {
        $mensaje_error = 'Error al guardar el mensaje. Por favor, intenta nuevamente.';
        error_log("Error DB contacto: " . $e->getMessage());
    } catch (Exception $e) {
        $mensaje_error = 'Error al enviar las notificaciones. El mensaje fue guardado correctamente.';
        error_log("Error notificaciones: " . $e->getMessage());
    }
} else {
        // Concatenar errores
        $mensaje_error = implode('<br>', $errores);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Boutique Sixta Coveñas</title>
    <link rel="stylesheet" href="<?= APP_URL ?>css/contacto.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="contacto-hero">
        <h1>¡Contáctanos!</h1>
        <p>Estamos aquí para ayudarte. Envíanos tu mensaje y te responderemos lo antes posible.</p>
    </section>

    <!-- Contenedor Principal -->
    <div class="contacto-container">
        <div class="contacto-grid">
            
            <!-- Formulario de Contacto -->
            <div class="contacto-form-card">
                <h2 class="form-title">Envíanos un Mensaje</h2>
                <p class="form-subtitle">Completa el formulario y nos pondremos en contacto contigo pronto.</p>

                <?php if (!empty($mensaje_exito)): ?>
                    <div class="alert alert-success">
                        ✅ <?= $mensaje_exito ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mensaje_error)): ?>
                    <div class="alert alert-error">
                        ⚠️ <?= $mensaje_error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contacto.php" id="contactoForm">
                    <div class="form-group">
                        <label for="nombre" class="form-label required">Nombre Completo</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="form-input" 
                            placeholder="Tu nombre completo"
                            value="<?= htmlspecialchars($nombre ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label required">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="tu@email.com"
                            value="<?= htmlspecialchars($email ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input 
                            type="tel" 
                            id="telefono" 
                            name="telefono" 
                            class="form-input" 
                            placeholder="+51 999 999 999"
                            value="<?= htmlspecialchars($telefono ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="asunto" class="form-label required">Asunto</label>
                        <select id="asunto" name="asunto" class="form-select" required>
                            <option value="">Selecciona un asunto</option>
                            <option value="consulta_producto">Consulta sobre Productos</option>
                            <option value="pedido">Seguimiento de Pedido</option>
                            <option value="devolucion">Devoluciones y Cambios</option>
                            <option value="sugerencia">Sugerencias</option>
                            <option value="queja">Quejas o Reclamos</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensaje" class="form-label required">Mensaje</label>
                        <textarea 
                            id="mensaje" 
                            name="mensaje" 
                            class="form-textarea" 
                            placeholder="Escribe tu mensaje aquí..."
                            required><?= htmlspecialchars($mensaje ?? '') ?></textarea>
                    </div>

                    <button type="submit" name="enviar_contacto" class="form-button">
                        📧 Enviar Mensaje
                    </button>
                </form>
            </div>

            <!-- Información de Contacto -->
            <div class="contacto-info-card">
                <h2 class="info-title">Información de Contacto</h2>

                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <h3>Dirección</h3>
                        <p>Urb. Santa Margarita, Distrito 26 de octubre<br>Piura - Peru</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <h3>Teléfono</h3>
                        <p><a href="tel:+51939332728">+51 939332728</a></p>
                        <p><a href="https://wa.me/51914914644" target="_blank">WhatsApp</a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><a href="mailto:info@boutiquesixta.com">info@boutiquesixta.com</a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🕒</div>
                    <div class="info-content">
                        <h3>Horario de Atención</h3>
                        <p>Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                        <p>Sábados: 9:00 AM - 2:00 PM</p>
                        <p>Domingos: Cerrado</p>
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Síguenos en Redes Sociales</h3>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">📘</a>
                    <a href="#" class="social-link" title="Instagram">📷</a>
                    <a href="#" class="social-link" title="WhatsApp">💬</a>
                    <a href="#" class="social-link" title="TikTok">🎵</a>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="map-section">
            <h2 class="map-title">📍 Encuéntranos</h2>
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.615688084204!2d-80.63445692501915!3d-5.194570894813092!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x904a1a0e4e7c3acb%3A0x2f1d3c3e3e3e3e3e!2sPiura%2C%20Perú!5e0!3m2!1ses!2spe!4v1712345678901"
                    allowfullscreen 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

    <!-- JavaScript para validación -->
    <script>
        // Validación del formulario
        document.getElementById('contactoForm').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const asunto = document.getElementById('asunto').value;
            const mensaje = document.getElementById('mensaje').value.trim();

            if (!nombre || !email || !asunto || !mensaje) {
                e.preventDefault();
                alert('Por favor, completa todos los campos obligatorios (*)');
                return false;
            }

            if (mensaje.length < 10) {
                e.preventDefault();
                alert('El mensaje debe tener al menos 10 caracteres');
                return false;
            }
        });

        // Limpiar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>