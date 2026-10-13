<?php
/**
 * Método simple para WhatsApp sin API
 * Archivo: includes/whatsapp_simple.php
 */

class WhatsAppSimple {
    
    /**
     * Generar enlace de WhatsApp
     */
    public function generarEnlace($numero, $mensaje) {
        $numero_limpio = preg_replace('/[^0-9]/', '', $numero);
        
        // Si el número empieza con 0, asumir Perú
        if (substr($numero_limpio, 0, 1) === '0') {
            $numero_limpio = '51' . substr($numero_limpio, 1);
        }
        
        // Codificar mensaje para URL
        $mensaje_codificado = urlencode($mensaje);
        
        return "https://wa.me/$numero_limpio?text=$mensaje_codificado";
    }
    
    /**
     * Enviar notificación por enlace (redirección)
     */
    public function enviarNotificacion($numero, $mensaje, $redirect = false) {
        $enlace = $this->generarEnlace($numero, $mensaje);
        
        if ($redirect) {
            header("Location: $enlace");
            exit;
        }
        
        return $enlace;
    }
    
    /**
     * Plantilla de mensaje para nuevo contacto
     */
    public function mensajeContactoCliente($nombre, $asunto) {
        return "¡Hola $nombre! 👋

        Gracias por contactar a *Boutique Sixta Coveñas*.

        ✅ Hemos recibido tu consulta sobre: *$asunto*
        📅 Te responderemos en máximo 24 horas hábiles.

        🆔 N° de seguimiento: #" . time() . "

        📞 Contactos:
        • WhatsApp: +51 999 999 999
        • Teléfono: +51 999 999 999

        📍 Ubicación:
        Urb. Santa Margarita, Piura

        ¡Gracias por elegirnos! 💕";
    }
    
    /**
     * Plantilla para notificación al admin
     */
    public function mensajeContactoAdmin($contacto_id, $nombre, $telefono, $asunto) {
        return "📞 *NUEVO CONTACTO*

        👤 Nombre: $nombre
        📱 Teléfono: $telefono
        🏷️ Asunto: $asunto
        🆔 ID: #$contacto_id
        ⏰ Hora: " . date('H:i') . "

        👉 Contactar:
        - Llamar: tel:$telefono
        - WhatsApp: https://wa.me/51" . preg_replace('/[^0-9]/', '', $telefono);
    }
}

// Instancia global
$whatsapp_simple = new WhatsAppSimple();
?>