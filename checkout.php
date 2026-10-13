<?php
/**
 * BOUTIQUE SIXTA COVEÑAS
 * Página de Checkout - Finalizar Compra
 * checkout.php - VERSIÓN MEJORADA CON DISEÑO PROFESIONAL
 */

// Iniciar sesión
session_start();

// Incluir archivos necesarios
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'includes/funciones.php';

// ============================================
// VERIFICAR QUE EL CARRITO NO ESTÉ VACÍO
// ============================================
if (empty($_SESSION['carrito'])) {
    $_SESSION['mensaje'] = [
        'tipo' => 'info',
        'texto' => 'Tu carrito está vacío. Agrega productos antes de continuar.'
    ];
    header('Location: index.php');
    exit();
}

// Inicializar variables
$errores = [];
$datos_formulario = [];

// ============================================
// PROCESAR FORMULARIO DE CHECKOUT
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos del formulario
    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $tipo_documento = trim($_POST['tipo_documento'] ?? 'DNI');
    $documento = trim($_POST['documento'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $direccion_envio = trim($_POST['direccion_envio'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $referencia = trim($_POST['referencia'] ?? '');
    
    // Guardar datos para repoblar formulario en caso de error
    $datos_formulario = [
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'email' => $email,
        'telefono' => $telefono,
        'tipo_documento' => $tipo_documento,
        'documento' => $documento,
        'direccion' => $direccion,
        'direccion_envio' => $direccion_envio,
        'metodo_pago' => $metodo_pago,
        'referencia' => $referencia
    ];
    
    // ============================================
    // VALIDACIONES
    // ============================================
    if (empty($nombres) || strlen($nombres) < 2) {
        $errores[] = "El nombre debe tener al menos 2 caracteres";
    }
    
    if (empty($apellidos) || strlen($apellidos) < 2) {
        $errores[] = "Los apellidos deben tener al menos 2 caracteres";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email válido es obligatorio";
    }
    
    if (empty($telefono) || !preg_match('/^[0-9+\-\s]{7,15}$/', $telefono)) {
        $errores[] = "Teléfono válido es obligatorio (7-15 dígitos)";
    }
    
    if (empty($documento) || strlen($documento) < 6) {
        $errores[] = "Documento de identidad válido es obligatorio";
    }
    
    if (empty($direccion) || strlen($direccion) < 10) {
        $errores[] = "La dirección debe tener al menos 10 caracteres";
    }
    
    if (empty($direccion_envio) || strlen($direccion_envio) < 10) {
        $errores[] = "La dirección de envío debe tener al menos 10 caracteres";
    }
    
    if (empty($metodo_pago)) {
        $errores[] = "Seleccione un método de pago";
    }
    
    // Verificar stock de productos
    foreach ($_SESSION['carrito'] as $item) {
        $producto = obtenerProductoPorId($db, $item['producto_id']);
        if (!$producto) {
            $errores[] = "Producto no encontrado (ID: {$item['producto_id']})";
        } elseif ($producto['stock'] < $item['cantidad']) {
            $errores[] = "Stock insuficiente para: {$producto['nombre']} (Disponible: {$producto['stock']})";
        }
    }
    
    // ============================================
    // SI NO HAY ERRORES, PROCESAR EL PEDIDO
    // ============================================
    if (empty($errores)) {
        try {
            $db->beginTransaction();
            
            // ============================================
            // 1. VERIFICAR SI EL CLIENTE YA EXISTE
            // ============================================
            $sql = "SELECT id_cliente FROM clientes WHERE email = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$email]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente) {
                // Cliente existente - actualizar datos
                $cliente_id = $cliente['id_cliente'];
                
                $sql = "UPDATE clientes SET 
                            nombres = ?, 
                            apellidos = ?, 
                            telefono = ?, 
                            direccion = ?,
                            tipo_documento = ?,
                            documento = ?
                        WHERE id_cliente = ?";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $nombres, 
                    $apellidos, 
                    $telefono, 
                    $direccion,
                    $tipo_documento,
                    $documento,
                    $cliente_id
                ]);
            } else {
                // Nuevo cliente - insertar
                $sql = "INSERT INTO clientes (
                            nombres, 
                            apellidos, 
                            email, 
                            telefono, 
                            direccion,
                            tipo_documento,
                            documento,
                            estado,
                            fecha_pedido
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $nombres, 
                    $apellidos, 
                    $email, 
                    $telefono, 
                    $direccion,
                    $tipo_documento,
                    $documento
                ]);
                
                $cliente_id = $db->lastInsertId();
                $_SESSION['cliente_id'] = $cliente_id;
            }
            
            // ============================================
            // 2. INSERTAR PEDIDO
            // ============================================
            $sql = "INSERT INTO pedidos (
                        id_cliente,
                        fecha_pedido, 
                        direccion_envio,
                        metodo_pago,
                        referencia,
                        estado
                    ) VALUES (?, NOW(), ?, ?, ?, 'pendiente')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $cliente_id,
                $direccion_envio,
                $metodo_pago,
                $referencia
            ]);
            
            $pedido_id = $db->lastInsertId();
            $_SESSION['orden_id'] = $pedido_id;
            
            // ============================================
            // 3. INSERTAR DETALLES DEL PEDIDO
            // ============================================
            $total_pedido = 0;
            
            foreach ($_SESSION['carrito'] as $item) {
                $producto = obtenerProductoPorId($db, $item['producto_id']);
                $precio = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
                          ? $producto['precio_oferta'] 
                          : $producto['precio'];
                $subtotal = $precio * $item['cantidad'];
                $total_pedido += $subtotal;
                
                $sql = "INSERT INTO detalle_pedidos (
                            pedido_id, 
                            producto_id, 
                            cantidad, 
                            precio_unitario, 
                            talla, 
                            color, 
                            subtotal
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $pedido_id, 
                    $item['producto_id'], 
                    $item['cantidad'], 
                    $precio, 
                    $item['talla'] ?? null, 
                    $item['color'] ?? null, 
                    $subtotal
                ]);
                
                // ============================================
                // 4. ACTUALIZAR STOCK
                // ============================================
                $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$item['cantidad'], $item['producto_id']]);
            }
            
            $db->commit();

        // ============================================
        // 5. ENVIAR EMAIL DE CONFIRMACIÓN
        // ============================================
        try {
            require_once 'enviar_email.php';
    
        // Preparar datos para el email
        $datos_cliente_email = [
        'nombre' => $nombres . ' ' . $apellidos,
        'email' => $email,
        'telefono' => $telefono,
        'documento' => $documento
    ];
    
    $datos_pedido_email = [
        'id' => $pedido_id,
        'metodo_pago' => $metodo_pago,
        'direccion_envio' => $direccion_envio,
        'referencia' => $referencia,
        'items' => []
    ];
    
    // Agregar items al email
    foreach ($_SESSION['carrito'] as $item) {
        $producto = obtenerProductoPorId($db, $item['producto_id']);
        $precio = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
                  ? $producto['precio_oferta'] 
                  : $producto['precio'];
        
        $datos_pedido_email['items'][] = [
            'nombre' => $producto['nombre'],
            'cantidad' => $item['cantidad'],
            'precio' => $precio,
            'talla' => $item['talla'] ?? null,
            'color' => $item['color'] ?? null
        ];
    }
    
    // Enviar email al cliente
    $email_cliente_enviado = enviarEmailConfirmacion($datos_pedido_email, $datos_cliente_email);
    
    // Enviar email al administrador
    $email_admin_enviado = enviarEmailAdmin($datos_pedido_email, $datos_cliente_email);
    
    if (!$email_cliente_enviado) {
        $_SESSION['email_warning'] = "El pedido se procesó correctamente pero hubo un problema al enviar el email de confirmación.";
    }
    
} catch (Exception $emailError) {
    error_log("Error en el proceso de email: " . $emailError->getMessage());
    $_SESSION['email_warning'] = "El pedido se procesó correctamente pero hubo un problema al enviar el email de confirmación.";
}
            // ============================================
            // 6. GUARDAR DATOS Y LIMPIAR CARRITO
            // ============================================
            $_SESSION['pedido_completado'] = [
                'pedido_id' => $pedido_id,
                'cliente_id' => $cliente_id,
                'nombre' => $nombres . ' ' . $apellidos,
                'email' => $email,
                'total' => $total_pedido,
                'items' => count($_SESSION['carrito']),
                'metodo_pago' => $metodo_pago
            ];
            
            unset($_SESSION['carrito']);
            
            // Redirigir a confirmación
            header('Location: confirmacion_pedido.php');
            exit();
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error en checkout: " . $e->getMessage());
            $errores[] = "Error al procesar el pedido. Por favor, intente nuevamente.";
        }
    }
}

// ============================================
// CALCULAR TOTALES
// ============================================
$total_carrito = calcularTotalCarrito($db, $_SESSION['carrito']);
$items_carrito = 0;
foreach ($_SESSION['carrito'] as $item) {
    $items_carrito += $item['cantidad'];
}

// ============================================
// INCLUIR HEADER
// ============================================
include 'includes/header.php';
?>

<link rel="stylesheet" href="<?= APP_URL ?>css/checkout.css">

<!-- ============================================
     CONTENEDOR PRINCIPAL DE CHECKOUT
     ============================================ -->
<div class="checkout-container">
    
    <!-- Header del Checkout -->
    <div class="checkout-header">
        <h1>Finalizar Compra</h1>
        <a href="index.php" class="btn-seguir-comprando">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Seguir Comprando
        </a>
    </div>

    <!-- Pasos del proceso -->
    <div class="checkout-steps">
        <div class="checkout-step step-completed">
            <div class="step-number">1</div>
            <span class="step-label">Carrito</span>
        </div>
        <div class="checkout-step step-active">
            <div class="step-number">2</div>
            <span class="step-label">Información</span>
        </div>
        <div class="checkout-step">
            <div class="step-number">3</div>
            <span class="step-label">Confirmación</span>
        </div>
    </div>

    <!-- Mostrar errores si existen -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Errores encontrados:
            </h3>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ============================================
         CONTENIDO DEL CHECKOUT
         ============================================ -->
    <div class="checkout-content">
        
        <!-- ============================================
             FORMULARIO DE DATOS
             ============================================ -->
        <div class="checkout-form-section">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Información Personal
            </h2>
            
            <form method="POST" class="checkout-form" id="checkoutForm">
                
                <!-- Nombres y Apellidos -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombres">Nombres *</label>
                        <input type="text" 
                               id="nombres" 
                               name="nombres" 
                               value="<?= htmlspecialchars($datos_formulario['nombres'] ?? '') ?>" 
                               required 
                               placeholder="Ej: María">
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos *</label>
                        <input type="text" 
                               id="apellidos" 
                               name="apellidos" 
                               value="<?= htmlspecialchars($datos_formulario['apellidos'] ?? '') ?>" 
                               required 
                               placeholder="Ej: García López">
                    </div>
                </div>
                
                <!-- Email y Teléfono -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($datos_formulario['email'] ?? '') ?>" 
                               required 
                               placeholder="ejemplo@correo.com">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               value="<?= htmlspecialchars($datos_formulario['telefono'] ?? '') ?>" 
                               required 
                               placeholder="999 888 777">
                    </div>
                </div>
                
                <!-- Tipo Documento y Número -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_documento">Tipo Documento *</label>
                        <select id="tipo_documento" name="tipo_documento" required>
                            <option value="DNI" <?= ($datos_formulario['tipo_documento'] ?? 'DNI') === 'DNI' ? 'selected' : '' ?>>DNI</option>
                            <option value="Pasaporte" <?= ($datos_formulario['tipo_documento'] ?? '') === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                            <option value="CE" <?= ($datos_formulario['tipo_documento'] ?? '') === 'CE' ? 'selected' : '' ?>>Carnet de Extranjería</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="documento">Número de Documento *</label>
                        <input type="text" 
                               id="documento" 
                               name="documento" 
                               value="<?= htmlspecialchars($datos_formulario['documento'] ?? '') ?>" 
                               required 
                               placeholder="12345678">
                    </div>
                </div>
                
                <!-- Dirección -->
                <div class="form-group">
                    <label for="direccion">Dirección *</label>
                    <textarea id="direccion" 
                              name="direccion" 
                              rows="2" 
                              required 
                              placeholder="Dirección principal del cliente..."><?= htmlspecialchars($datos_formulario['direccion'] ?? '') ?></textarea>
                </div>
                
                <!-- Dirección de Envío -->
                <div class="form-group">
                    <label for="direccion_envio">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-right: 8px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Dirección de Envío *
                    </label>
                    <textarea id="direccion_envio" 
                              name="direccion_envio" 
                              rows="3" 
                              required 
                              placeholder="Calle, número, distrito, ciudad, referencias..."><?= htmlspecialchars($datos_formulario['direccion_envio'] ?? '') ?></textarea>
                </div>
                
                <!-- Método de Pago -->
                <div class="form-group">
                    <label for="metodo_pago">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-right: 8px;">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Método de Pago *
                    </label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="">Seleccione método de pago</option>
                        <option value="tarjeta" <?= ($datos_formulario['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>
                            💳 Tarjeta de Crédito/Débito
                        </option>
                        <option value="yape" <?= ($datos_formulario['metodo_pago'] ?? '') === 'yape' ? 'selected' : '' ?>>
                            📱 Yape
                        </option>
                        <option value="plin" <?= ($datos_formulario['metodo_pago'] ?? '') === 'plin' ? 'selected' : '' ?>>
                            📱 Plin
                        </option>
                        <option value="transferencia" <?= ($datos_formulario['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>
                            🏦 Transferencia Bancaria
                        </option>
                        <option value="efectivo" <?= ($datos_formulario['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>
                            💵 Pago Contra Entrega
                        </option>
                    </select>
                </div>
                
                <!-- Referencia/Comprobante -->
                <div class="form-group">
                    <label for="referencia">Referencia de Pago (opcional)</label>
                    <input type="text" 
                           id="referencia" 
                           name="referencia" 
                           value="<?= htmlspecialchars($datos_formulario['referencia'] ?? '') ?>" 
                           placeholder="Número de operación, comprobante, etc.">
                </div>
                
                <!-- Botón de Confirmar -->
                <button type="submit" class="btn-confirmar-pedido">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Confirmar Pedido
                </button>
                
            </form>
        </div>
        
        <!-- ============================================
             RESUMEN DEL PEDIDO
             ============================================ -->
        <div class="checkout-summary">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Resumen del Pedido
            </h2>
            
            <!-- Items del carrito -->
            <div class="resumen-items">
                <?php foreach ($_SESSION['carrito'] as $key => $item): 
                    $producto = obtenerProductoPorId($db, $item['producto_id']);
                    if (!$producto) continue;
                    
                    $precio = !empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 
                              ? $producto['precio_oferta'] 
                              : $producto['precio'];
                    $subtotal = $precio * $item['cantidad'];
                    
                ?>
                    <div class="resumen-item">
                        <div class="resumen-item-imagen">
                            <img src="<?= IMG_PRODUCTOS_URL . htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        </div>
                        <div class="resumen-item-info">
                            <h4><?= htmlspecialchars($producto['nombre']) ?></h4>
                            <div class="resumen-detalles">
                                <?php if (!empty($item['talla'])): ?>
                                    <span>Talla: <?= htmlspecialchars($item['talla']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['color'])): ?>
                                    <span>Color: <?= htmlspecialchars($item['color']) ?></span>
                                <?php endif; ?>
                                <span>Cant: <?= $item['cantidad'] ?></span>
                            </div>
                            <div class="resumen-precio">
                                S/ <?= number_format($subtotal, 2) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
<!-- Totales - DISEÑO CON TOTAL AMPLIADO -->
<div class="resumen-total">
    <div class="total-items">
        <!-- Subtotal -->
        <div class="total-item total-item-subtotal">
            <div class="total-label">Subtotal</div>
            <div class="total-value subtotal-value">S/ <?= number_format($total_carrito, 2) ?></div>
        </div>
        
        <!-- Envío -->
        <div class="total-item total-item-envio">
            <div class="total-label">Envío</div>
            <div class="total-value envio-value">
                <span class="envio-gratis-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    GRATIS
                </span>
            </div>
        </div>
        
        <!-- Separador -->
        <div class="total-separator-line"></div>
        
        <!-- Total Final - CAJA AMPLIADA -->
        <div class="total-final-box">
            <div class="total-final-content">
                <div class="total-final-label">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                    TOTAL A PAGAR
                </div>
                <div class="total-final-amount">
                    S/ <span class="total-number"><?= number_format($total_carrito, 2) ?></span>
                </div>
                <div class="total-currency">SOLES PERUANOS</div>
            </div>
        </div>
        
        <!-- Nota de impuestos -->
        <div class="total-nota">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            Incluye impuestos
        </div>
    </div>
</div>

 </div>
</div>
</div>

<!-- ============================================
     JAVASCRIPT PARA CHECKOUT
     ============================================ -->
<script>
// Mostrar/ocultar información de tarjeta según método de pago
document.getElementById('metodo_pago').addEventListener('change', function() {
    const tarjetaInfo = document.getElementById('tarjeta-info');
    if (tarjetaInfo) {
        tarjetaInfo.style.display = this.value === 'tarjeta' ? 'block' : 'none';
    }
});

// Validación adicional del formulario
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const telefono = document.getElementById('telefono').value;
    const telefonoRegex = /^[0-9+\-\s]{7,15}$/;
    
    if (!telefonoRegex.test(telefono)) {
        e.preventDefault();
        alert('Por favor ingrese un número de teléfono válido (7-15 dígitos)');
        document.getElementById('telefono').focus();
        return false;
    }
    
    // Mostrar loading state
    const submitBtn = this.querySelector('.btn-confirmar-pedido');
    submitBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg> Procesando...';
    submitBtn.disabled = true;
});

// Formateo automático de número de tarjeta
const numeroTarjeta = document.getElementById('numero_tarjeta');
if (numeroTarjeta) {
    numeroTarjeta.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });
}

// Formateo de fecha de vencimiento
const fechaVencimiento = document.getElementById('fecha_vencimiento');
if (fechaVencimiento) {
    fechaVencimiento.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });
}

// Mostrar campos de tarjeta si ya estaba seleccionado
window.addEventListener('DOMContentLoaded', function() {
    const metodoPago = document.getElementById('metodo_pago').value;
    const tarjetaInfo = document.getElementById('tarjeta-info');
    if (tarjetaInfo && metodoPago === 'tarjeta') {
        tarjetaInfo.style.display = 'block';
    }
    
    // Animación de entrada para elementos
    const items = document.querySelectorAll('.resumen-item');
    items.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });
});

// Mejorar experiencia en móviles
if (window.innerWidth <= 768) {
    document.querySelectorAll('input, textarea, select').forEach(element => {
        element.addEventListener('focus', function() {
            this.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
}

// Mejoras para los totales
document.addEventListener('DOMContentLoaded', function() {
    // Asegurar que los totales sean visibles
    const totalItems = document.querySelectorAll('.total-item');
    totalItems.forEach((item, index) => {
        item.style.display = 'flex';
        item.style.flexDirection = 'row';
        item.style.justifyContent = 'space-between';
        item.style.alignItems = 'center';
        item.style.width = '100%';
    });
    
    // Resaltar el total final
    const totalFinal = document.querySelector('.total-final-item');
    if (totalFinal) {
        setTimeout(() => {
            totalFinal.style.animation = 'fadeInUp 0.5s ease forwards, highlightPulse 2s ease-in-out 0.6s';
        }, 100);
    }
});

</script>

<?php include 'includes/footer.php'; ?>