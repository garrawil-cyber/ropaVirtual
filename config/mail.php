<?php
/**
 * Configuración de PHPMailer para BOUTIQUE SIXTA COVEÑAS
 */

// Ruta a PHPMailer (ajusta según tu método de instalación)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

class ConfigMail {
    
    // Configuración del servidor SMTP
    private $host = 'smtp.gmail.com';       // Servidor SMTP
    private $username = 'garrawil@gmail.com'; // Tu email
    private $password = 'phzulksupkdpkrgk';   // Contraseña de aplicación
    private $port = 587;                     // Puerto SMTP
    private $encryption = 'tls';             // Encriptación
    
    // Configuración general
    private $fromEmail = 'garrawilmer@gmail.com';  // Email del remitente
    private $fromName = 'BOUTIQUE SIXTA COVEÑAS'; // Nombre del remitente
    private $replyTo = 'info@boutiquesixta.com'; // Email de respuesta
    private $adminEmail = 'garrawil@gmail.com';  // Email del administrador
    
    public function crearInstancia() {
        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();            // Usar SMTP    
            $mail->Host = $this->host;  // Servidor SMTP
            $mail->SMTPAuth = true;     // Habilitar autenticación SMTP
            $mail->Username = $this->username; // Usuario SMTP
            $mail->Password = $this->password;         // Contraseña SMTP
            $mail->SMTPSecure = $this->encryption;     // Encriptación
            $mail->Port = $this->port;                  // Puerto SMTP
            
            // Configuración del remitente
            $mail->setFrom($this->fromEmail, $this->fromName);  // Remitente
            $mail->addReplyTo($this->replyTo, $this->fromName); // Responder a
            
            // Configuración general
            $mail->CharSet = 'UTF-8';  // Codificación UTF-8
            $mail->isHTML(true);       // Habilitar formato HTML
            
            return $mail;
            
        } catch (Exception $e) {
            error_log("Error al crear instancia de PHPMailer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar email al administrador
     */
    public function enviarEmailAdministrador($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id) {
        try {
            $mail = $this->crearInstancia();
            if (!$mail) return false;
            
            // Destinatario
            $mail->addAddress($this->adminEmail);  // Email del admin
            $mail->addReplyTo($email, $nombre); // Responder al cliente
            
            // Asunto
            $asunto_texto = $this->obtenerAsuntoTexto($asunto);
            $mail->Subject = '[Contacto Web] ' . $asunto_texto;
            
            // Contenido HTML
            $mail->Body = $this->crearPlantillaEmail($nombre, $email, $telefono, $asunto_texto, $mensaje, $contacto_id);
            
            // Versión texto plano
            $mail->AltBody = $this->crearEmailTextoPlano($nombre, $email, $telefono, $asunto_texto, $mensaje, $contacto_id);
            
            return $mail->send();
            
        } catch (Exception $e) {
            error_log("Error enviarEmailAdministrador: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Enviar email de confirmación al cliente
     */
    public function enviarEmailConfirmacion($nombre, $email_cliente, $asunto_tipo) {
        try {
            $mail = $this->crearInstancia();
            if (!$mail) return false;
            
            // Destinatario
            $mail->addAddress($email_cliente, $nombre);
            
            // Asunto
            $mail->Subject = 'Confirmación de contacto - Boutique Sixta Coveñas';
            
            // Contenido HTML
            $asunto_texto = $this->obtenerAsuntoTexto($asunto_tipo);
            $mail->Body = $this->crearPlantillaConfirmacion($nombre, $asunto_texto);
            
            // Versión texto plano
            $mail->AltBody = "Hola $nombre,\n\nHemos recibido tu mensaje sobre $asunto_texto.\nTe responderemos en máximo 24 horas hábiles.\n\nAtentamente,\nBoutique Sixta Coveñas";
            
            return $mail->send();
            
        } catch (Exception $e) {
            error_log("Error enviarEmailConfirmacion: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Crear plantilla HTML para email al administrador
     */
    private function crearPlantillaEmail($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id) {
        $fecha = date('d/m/Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        $telefono_formateado = $telefono ?: 'No proporcionado';
        
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Nuevo Contacto Web</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 700px; margin: 0 auto; }
                .header { background: #e8b4bc; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f9f5f6; }
                .info-box { background: white; border-left: 4px solid #e8b4bc; padding: 15px; margin: 15px 0; }
                .label { font-weight: bold; color: #d4a5a5; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📧 Nuevo Mensaje de Contacto</h1>
                    <p>ID: #$contacto_id | $fecha</p>
                </div>
                
                <div class='content'>
                    <div class='info-box'>
                        <p><span class='label'>👤 Nombre:</span> $nombre</p>
                        <p><span class='label'>📧 Email:</span> <a href='mailto:$email'>$email</a></p>
                        <p><span class='label'>📞 Teléfono:</span> $telefono_formateado</p>
                        <p><span class='label'>🏷️ Asunto:</span> $asunto</p>
                        <p><span class='label'>🌐 IP:</span> $ip</p>
                    </div>
                    
                    <div class='info-box'>
                        <p><span class='label'>💬 Mensaje:</span></p>
                        <p>{$this->sanitizarMensaje($mensaje)}</p>
                    </div>
                    
                    <div class='info-box'>
                        <p><span class='label'>⚡ Acciones rápidas:</span></p>
                        <p>
                            <a href='mailto:$email?subject=Re: $asunto'>📨 Responder</a> | 
                            <a href='tel:$telefono'>📞 Llamar</a> | 
                            <a href='https://wa.me/51{$this->limpiarTelefono($telefono)}'>💬 WhatsApp</a>
                        </p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>⚠️ Este mensaje fue enviado desde el formulario de contacto del sitio web.</p>
                    <p>© {$this->fromName} - Sistema Automático</p>
                </div>
            </div>
        </body>
        </html>
HTML;
    }
    
    /**
     * Crear plantilla de confirmación para el cliente
     */
    private function crearPlantillaConfirmacion($nombre, $asunto) {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Confirmación de Contacto</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #e8b4bc; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f5f6; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Gracias por contactarnos!</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>Hemos recibido tu mensaje sobre <strong>$asunto</strong>.</p>
                    <p>Nuestro equipo revisará tu consulta y te responderá en un plazo máximo de 24 horas hábiles.</p>
                    <p><strong>Número de seguimiento:</strong> #" . time() . "</p>
                    <hr>
                    <p>Atentamente,<br>
                    <strong>Equipo {$this->fromName}</strong><br>
                    📧 {$this->replyTo}<br>
                    📞 +51 999 999 999</p>
                </div>
                <div class='footer'>
                    <p>Este es un email automático, por favor no responder.</p>
                </div>
            </div>
        </body>
        </html>
HTML;
    }
    
    /**
     * Crear versión texto plano para email
     */
    private function crearEmailTextoPlano($nombre, $email, $telefono, $asunto, $mensaje, $contacto_id) {
        $telefono_formateado = $telefono ?: 'No proporcionado';
        $fecha = date('d/m/Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        
        return "NUEVO MENSAJE DE CONTACTO
        ============================
        📋 ID: #$contacto_id
        📅 Fecha: $fecha
        👤 Nombre: $nombre
        📧 Email: $email
        📞 Teléfono: $telefono_formateado
        🏷️ Asunto: $asunto
        🌐 IP: $ip

        💬 MENSAJE:
        $mensaje

        ⚡ ACCIONES:
        - Responder: mailto:$email
        - Llamar: tel:$telefono
        - WhatsApp: https://wa.me/51{$this->limpiarTelefono($telefono)}

        ⚠️ Enviado desde el formulario web.";
    }
    
    /**
     * Convertir código de asunto a texto
     */
    private function obtenerAsuntoTexto($codigo) {
        $asuntos = [
            'consulta_producto' => 'Consulta sobre Productos',
            'pedido' => 'Seguimiento de Pedido',
            'devolucion' => 'Devoluciones y Cambios',
            'sugerencia' => 'Sugerencias',
            'queja' => 'Quejas o Reclamos',
            'otro' => 'Otro'
        ];
        
        return $asuntos[$codigo] ?? 'Asunto no especificado';
    }
    
    /**
     * Sanitizar mensaje para HTML
     */
    private function sanitizarMensaje($mensaje) {
        return nl2br(htmlspecialchars($mensaje));
    }
    
    /**
     * Limpiar número de teléfono
     */
    private function limpiarTelefono($telefono) {
        return preg_replace('/[^0-9]/', '', $telefono);
    }
    
    // Getters
    public function getAdminEmail() { 
        return $this->adminEmail;
    }

    public function getFromEmail() {
        return $this->fromEmail;
    }
    
    public function getFromName() {
        return $this->fromName;
    }
}

// Crear instancia global
$configMail = new ConfigMail();
?>