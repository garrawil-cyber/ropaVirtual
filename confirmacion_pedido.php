<?php
/**
 * BOUTIQUE SIXTA COVEÑAS
 * Página de Confirmación de Pedido
 * confirmacion_pedido.php
 */

// Iniciar sesión
session_start();

// Incluir archivos necesarios
require_once 'config/app.php';
require_once 'config/database.php';

// ============================================
// VERIFICAR QUE EXISTE UN PEDIDO COMPLETADO
// ============================================
if (!isset($_SESSION['pedido_completado'])) {
    header('Location: index.php');
    exit();
}

// Obtener datos del pedido
$pedido = $_SESSION['pedido_completado'];

// Obtener valores individuales
$pedido_id = $pedido['pedido_id'];
$nombre = $pedido['nombre'];
$email = $pedido['email'];
$total = $pedido['total'];
$items = $pedido['items'];
$metodo_pago = $pedido['metodo_pago'];

// Verificar si hubo advertencia de email
$email_warning = isset($_SESSION['email_warning']) ? $_SESSION['email_warning'] : null;
if ($email_warning) {
    unset($_SESSION['email_warning']);
}

// Limpiar la sesión del pedido (para que no se pueda recargar)
unset($_SESSION['pedido_completado']);

// Calcular items_carrito para el header (debe ser 0 ahora)
$items_carrito = 0;

// ============================================
// INCLUIR HEADER
// ============================================
include 'includes/header.php';
?>

<link rel="stylesheet" href="<?= APP_URL ?>css/confirmacion_pedido.css">

<!-- ============================================
     CONTENIDO DE CONFIRMACIÓN
     ============================================ -->
<div class="confirmacion-container">
    <div class="confirmacion-card">
        
        <!-- Icono de éxito -->
        <div class="confirmacion-icon">✅</div>
        
        <!-- Título -->
        <h1>¡Pedido Confirmado!</h1>
        
        <!-- Mensaje -->
        <p>
            Gracias <strong><?= htmlspecialchars($nombre) ?></strong> por tu compra en Boutique Sixta Coveñas. 
            Tu pedido ha sido recibido y está siendo procesado.
        </p>
        
        <!-- Advertencia de email si existe -->
        <?php if ($email_warning): ?>
        <div class="alert-warning">
            <strong>⚠️ Aviso:</strong>
            <p><?= htmlspecialchars($email_warning) ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Número de pedido -->
        <div class="pedido-numero">
            <strong>Número de Pedido:</strong>
            <span>#<?= str_pad($pedido_id, 6, '0', STR_PAD_LEFT) ?></span>
        </div>
        
        <!-- Detalles del pedido -->
        <div class="pedido-detalles">
            <h3>📋 Detalles del Pedido</h3>
            
            <div class="detalle-row">
                <span class="detalle-label">Email:</span>
                <span class="detalle-valor"><?= htmlspecialchars($email) ?></span>
            </div>
            
            <div class="detalle-row">
                <span class="detalle-label">Artículos:</span>
                <span class="detalle-valor"><?= $items ?> producto<?= $items > 1 ? 's' : '' ?></span>
            </div>
            
            <div class="detalle-row">
                <span class="detalle-label">Método de Pago:</span>
                <span class="detalle-valor">
                    <span class="metodo-pago-badge">
                        <?php
                        $metodos = [
                            'tarjeta' => '💳 Tarjeta',
                            'paypal' => '💰 PayPal',
                            'transferencia' => '🏦 Transferencia',
                            'efectivo' => '💵 Contra Entrega'
                        ];
                        echo $metodos[$metodo_pago] ?? htmlspecialchars(ucfirst($metodo_pago));
                        ?>
                    </span>
                </span>
            </div>
            
            <div class="detalle-row">
                <span class="detalle-label">Total Pagado:</span>
                <span class="detalle-valor">S/ <?= number_format($total, 2) ?></span>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="confirmacion-info">
            <h4>📧 Confirmación por Email</h4>
            <p>
                Hemos enviado un correo de confirmación a <strong><?= htmlspecialchars($email) ?></strong> 
                con todos los detalles de tu pedido y el número de seguimiento.
            </p>
        </div>
        
        <!-- Acciones -->
        <div class="confirmacion-acciones">
            <a href="index.php" class="btn btn-primary">
                🛍️ Seguir Comprando
            </a>
            <a href="mis_pedidos.php" class="btn btn-secondary">
                📦 Ver Mis Pedidos
            </a>
        </div>
        
    </div>
</div>

<!-- Información adicional fuera de la card -->
<div style="max-width: 800px; margin: 2rem auto; padding: 0 2rem; text-align: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 1rem 0; color: #1f2937;">🚚 ¿Qué sigue?</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            
            <div style="padding: 1rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📦</div>
                <strong style="display: block; margin-bottom: 0.5rem; color: #1f2937;">Procesamiento</strong>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Tu pedido está siendo preparado</p>
            </div>
            
            <div style="padding: 1rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚚</div>
                <strong style="display: block; margin-bottom: 0.5rem; color: #1f2937;">Envío</strong>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Te notificaremos cuando sea enviado</p>
            </div>
            
            <div style="padding: 1rem;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏠</div>
                <strong style="display: block; margin-bottom: 0.5rem; color: #1f2937;">Entrega</strong>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Recibirás tu pedido en 3-5 días</p>
            </div>
            
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>