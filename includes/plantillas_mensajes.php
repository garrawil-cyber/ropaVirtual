

<?php
class PlantillasMensajes {
    
    public static function contactoCliente($nombre, $asunto, $id) {
        return "¡Hola $nombre! 👋

    Boutique Sixta Coveñas te informa:

    ✅ Recibimos tu consulta: *$asunto*
    📅 Te responderemos en 24 horas hábiles
    🆔 Tu ID: #$id

    📞 Contactos:
    • WhatsApp: +51 999 999 999
    • Teléfono: +51 999 999 999

    ¡Gracias por contactarnos! 💕";
 }
    
    public static function nuevoPedidoAdmin($cliente, $pedido_id, $total) {
        return "🛍️ *NUEVO PEDIDO #$pedido_id*

        👤 Cliente: $cliente
        💰 Total: S/ $total
        ⏰ Hora: " . date('H:i') . "

        📊 Ver: " . APP_URL . "admin/pedidos.php?id=$pedido_id";
    }
}
?>