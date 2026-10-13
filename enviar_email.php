<?php
/**
 * Script para enviar emails de confirmación de pedidos
 * BOUTIQUE SIXTA COVEÑAS
 */

// Incluir configuración
require_once 'config/mail.php';

function enviarEmailConfirmacion($datos_pedido, $datos_cliente) {
    $configMail = new ConfigMail();
    $mail = $configMail->crearInstancia();
    
    if (!$mail) {
        return false;
    }
    
    try {
        // Destinatario
        $mail->addAddress($datos_cliente['email'], $datos_cliente['nombre']);
        
        // Asunto
        $mail->Subject = '✅ Confirmación de Pedido - BOUTIQUE SIXTA COVEÑAS';
        
        // Cuerpo del email (HTML)
        $mail->Body = crearTemplateEmail($datos_pedido, $datos_cliente);
        
        // Versión alternativa en texto plano
        $mail->AltBody = crearTemplateEmailTexto($datos_pedido, $datos_cliente);
        
        // Enviar email
        $mail->send();
        
        // Registrar en log
        error_log("Email de confirmación enviado a: " . $datos_cliente['email']);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar email: " . $mail->ErrorInfo);
        return false;
    }
}

function crearTemplateEmail($pedido, $cliente) {
    $items_html = '';
    $total = 0;
    
    foreach ($pedido['items'] as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        
        $items_html .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . htmlspecialchars($item['nombre']) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">' . $item['cantidad'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">S/ ' . number_format($item['precio'], 2) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">S/ ' . number_format($subtotal, 2) . '</td>
        </tr>';
    }
    
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmación de Pedido</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #ff4081, #7c4dff); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .order-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff4081; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
            .total { font-size: 1.2em; font-weight: bold; color: #ff4081; }
            .footer { text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>¡Gracias por tu compra!</h1>
                <p>BOUTIQUE SIXTA COVEÑAS</p>
            </div>
            
            <div class="content">
                <p>Hola <strong>' . htmlspecialchars($cliente['nombre']) .'</strong>,</p>
                <p>Tu pedido ha sido procesado exitosamente. Aquí tienes los detalles:</p>
                
                <div class="order-info">
                    <h3>📦 Información del Pedido</h3>
                    <p><strong>Número de Pedido:</strong> #' . $pedido['id'] . '</p>
                    <p><strong>Fecha:</strong> ' . date('d/m/Y H:i') . '</p>
                    <p><strong>Método de Pago:</strong> ' . htmlspecialchars($pedido['metodo_pago']) . '</p>
                </div>
                
                <h3>🛍️ Detalles del Pedido</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $items_html . '
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 10px; border-top: 2px solid #dee2e6;"><strong>Total:</strong></td>
                            <td style="text-align: right; padding: 10px; border-top: 2px solid #dee2e6;" class="total">S/ ' . number_format($total, 2) . '</td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="order-info">
                    <h3>🚚 Información de Envío</h3>
                    <p><strong>Dirección:</strong> ' . htmlspecialchars($pedido['direccion_envio']) . '</p>
                    <p><strong>Teléfono:</strong> ' . htmlspecialchars($cliente['telefono']) . '</p>
                </div>
                
                <div class="footer">
                    <p>📞 <strong>Soporte al Cliente:</strong> +51 123 456 789</p>
                    <p>📧 <strong>Email:</strong> info@boutiquesixta.com</p>
                    <p>⏰ <strong>Horario de Atención:</strong> Lunes a Sábado 9:00 AM - 7:00 PM</p>
                    <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
                        Este es un email automático, por favor no respondas a este mensaje.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>';
}

function crearTemplateEmailTexto($pedido, $cliente) {
    $texto = "CONFIRMACIÓN DE PEDIDO - BOUTIQUE SIXTA COVEÑAS\n\n";
    $texto .= "Hola " . $cliente['nombre'] . ",\n\n";
    $texto .= "Tu pedido ha sido procesado exitosamente.\n\n";
    $texto .= "INFORMACIÓN DEL PEDIDO:\n";
    $texto .= "Número de Pedido: #" . $pedido['id'] . "\n";
    $texto .= "Fecha: " . date('d/m/Y H:i') . "\n";
    $texto .= "Método de Pago: " . $pedido['metodo_pago'] . "\n\n";
    
    $texto .= "DETALLES DEL PEDIDO:\n";
    $total = 0;
    foreach ($pedido['items'] as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        $texto .= "- " . $item['nombre'] . " x" . $item['cantidad'] . " = S/ " . number_format($subtotal, 2) . "\n";
    }
    $texto .= "TOTAL: S/ " . number_format($total, 2) . "\n\n";
    
    $texto .= "INFORMACIÓN DE ENVÍO:\n";
    $texto .= "Dirección: " . $pedido['direccion_envio'] . "\n";
    $texto .= "Teléfono: " . $cliente['telefono'] . "\n\n";
    
    $texto .= "Soporte al Cliente: +51 123 456 789\n";
    $texto .= "Email: info@boutiquesixta.com\n";
    $texto .= "Horario: Lunes a Sábado 9:00 AM - 7:00 PM\n\n";
    $texto .= "Este es un email automático, por favor no respondas a este mensaje.";
    
    return $texto;
}

// Función para enviar email al administrador
function enviarEmailAdmin($datos_pedido, $datos_cliente) {
    $configMail = new ConfigMail();
    $mail = $configMail->crearInstancia();
    
    if (!$mail) {
        return false;
    }
    
    try {
        // Destinatario (administrador)
        $mail->addAddress('garrawil@gmail.com', 'Administrador Boutique');
        
        // Asunto
        $mail->Subject = '🛒 Nuevo Pedido Recibido - #' . $datos_pedido['id'];
        
        // Cuerpo del email para admin
        $mail->Body = crearTemplateEmailAdmin($datos_pedido, $datos_cliente);
        
        // Enviar email
        $mail->send();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar email al admin: " . $mail->ErrorInfo);
        return false;
    }
}

function crearTemplateEmailAdmin($pedido, $cliente) {
    $items_html = '';
    $total = 0;
    
    foreach ($pedido['items'] as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
        
        $items_html .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . htmlspecialchars($item['nombre']) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">' . $item['cantidad'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">S/ ' . number_format($item['precio'], 2) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">S/ ' . number_format($subtotal, 2) . '</td>
        </tr>';
    }
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .info-box { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 10px; border-bottom: 1px solid #ddd; }
            .total { font-weight: bold; color: #dc3545; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>NUEVO PEDIDO RECIBIDO</h2>
                <p>Pedido #' . $pedido['id'] . '</p>
            </div>
            <div class="content">
                <div class="info-box">
                    <h3>Información del Cliente</h3>
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($cliente['nombre']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($cliente['email']) . '</p>
                    <p><strong>Teléfono:</strong> ' . htmlspecialchars($cliente['telefono']) . '</p>
                    <p><strong>Documento:</strong> ' . htmlspecialchars($cliente['documento']) . '</p>
                </div>
                
                <div class="info-box">
                    <h3>Detalles del Pedido</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $items_html . '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                <td class="total">S/ ' . number_format($total, 2) . '</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="info-box">
                    <h3>Envío y Pago</h3>
                    <p><strong>Dirección de Envío:</strong> ' . htmlspecialchars($pedido['direccion_envio']) . '</p>
                    <p><strong>Método de Pago:</strong> ' . htmlspecialchars($pedido['metodo_pago']) . '</p>
                    <p><strong>Referencia:</strong> ' . ($pedido['referencia'] ? htmlspecialchars($pedido['referencia']) : 'N/A') . '</p>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?>