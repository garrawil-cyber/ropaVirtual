<?php
/**
 * Panel de control de notificaciones
 * Archivo: admin/notificaciones.php
 */

session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/whatsapp.php';
require_once '../config/sms.php';

// Verificar autenticación
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Enviar notificación manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_notificacion'])) {
    $tipo = $_POST['tipo'];
    $destino = $_POST['destino'];
    $mensaje = $_POST['mensaje'];
    
    switch($tipo) {
        case 'whatsapp':
            $resultado = $whatsapp->enviarMensajeAPI($destino, $mensaje);
            $mensaje_exito = 'Notificación WhatsApp enviada';
            break;
        case 'sms':
            $resultado = $sms->enviarSMS($destino, $mensaje);
            $mensaje_exito = 'SMS enviado';
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones - Panel Admin</title>
    <style>
        .panel-notificaciones {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            background: #e8b4bc;
            color: white;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>🔔 Panel de Notificaciones</h1>
    
    <div class="panel-notificaciones">
        
        <!-- Enviar Notificación Manual -->
        <div class="card">
            <h2>📤 Enviar Notificación</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Tipo:</label>
                    <select name="tipo" class="form-control">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Destino (número):</label>
                    <input type="text" name="destino" class="form-control" 
                           placeholder="+51 999 999 999" required>
                </div>
                
                <div class="form-group">
                    <label>Mensaje:</label>
                    <textarea name="mensaje" class="form-control" rows="5" required></textarea>
                </div>
                
                <button type="submit" name="enviar_notificacion" class="btn">
                    📨 Enviar Notificación
                </button>
            </form>
        </div>
        
        <!-- Plantillas Rápidas -->
        <div class="card">
            <h2>📋 Plantillas Rápidas</h2>
            
            <h3>Para Contactos:</h3>
            <button onclick="cargarPlantilla('contacto')" class="btn">
                📞 Confirmación de Contacto
            </button>
            
            <h3>Para Pedidos:</h3>
            <button onclick="cargarPlantilla('pedido_confirmado')" class="btn">
                ✅ Pedido Confirmado
            </button>
            
            <button onclick="cargarPlantilla('pedido_enviado')" class="btn">
                🚚 Pedido Enviado
            </button>
            
            <h3>Recordatorios:</h3>
            <button onclick="cargarPlantilla('recordatorio')" class="btn">
                ⏰ Recordatorio de Cita
            </button>
        </div>
    </div>
    
    <script>
    function cargarPlantilla(tipo) {
        const plantillas = {
            contacto: "¡Hola! 👋\n\nGracias por contactar a Boutique Sixta Coveñas.\nHemos recibido tu consulta y te responderemos en máximo 24 horas.\n\n📞 Contacto: +51 999 999 999",
            
            pedido_confirmado: "¡Hola! 👋\n\nTu pedido ha sido confirmado ✅\nEstamos preparando todo para enviártelo.\nTe avisaremos cuando esté en camino.\n\nBoutique Sixta Coveñas",
            
            pedido_enviado: "¡Buenas noticias! 🎉\n\nTu pedido ya está en camino 🚚\nRecibirás actualizaciones del envío.\n\nGracias por tu compra,\nBoutique Sixta Coveñas",
            
            recordatorio: "Recordatorio 📅\n\nTienes una cita programada con nosotros.\nPor favor confirma tu asistencia.\n\nBoutique Sixta Coveñas"
        };
        
        document.querySelector('[name="mensaje"]').value = plantillas[tipo] || '';
    }
    </script>
</body>
</html>