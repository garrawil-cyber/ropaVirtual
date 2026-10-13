<?php
/**
 * Método SMS simple usando email-to-SMS
 * Archivo: includes/sms_simple.php
 */

class SMSSimple {
    
    private $operadoras = [
        'claro' => '@claro.com.pe',
        'movistar' => '@movistar.com.pe',
        'bitel' => '@bitel.com.pe',
        'entel' => '@entel.com.pe'
    ];
    
    /**
     * Detectar operadora por número
     */
    private function detectarOperadora($numero) {
        $prefijo = substr($numero, 0, 3);
        
        // Prefijos Perú
        $prefijos = [
            '914' => 'claro',
            '930' => 'claro',
            '931' => 'claro',
            '932' => 'claro',
            '933' => 'claro',
            '934' => 'claro',
            '935' => 'claro',
            '936' => 'claro',
            '937' => 'claro',
            '938' => 'claro',
            '939' => 'claro',
            '940' => 'movistar',
            '941' => 'movistar',
            '942' => 'movistar',
            '943' => 'movistar',
            '944' => 'movistar',
            '945' => 'movistar',
            '946' => 'movistar',
            '947' => 'movistar',
            '948' => 'movistar',
            '949' => 'movistar',
            '950' => 'bitel',
            '951' => 'bitel',
            '952' => 'bitel',
            '953' => 'bitel',
            '954' => 'bitel',
            '955' => 'bitel',
            '956' => 'bitel',
            '957' => 'bitel',
            '958' => 'bitel',
            '959' => 'bitel',
            '970' => 'entel',
            '971' => 'entel',
            '972' => 'entel',
            '973' => 'entel',
            '974' => 'entel',
            '975' => 'entel',
            '976' => 'entel',
            '977' => 'entel',
            '978' => 'entel',
            '979' => 'entel'
        ];
        
        return $prefijos[$prefijo] ?? 'claro'; // Default a Claro
    }
    
    /**
     * Enviar SMS por email (funciona con algunas operadoras)
     */
    public function enviarSMS($numero, $mensaje) {
        $numero_limpio = preg_replace('/[^0-9]/', '', $numero);
        
        // Remover el 0 inicial si existe
        if (substr($numero_limpio, 0, 1) === '0') {
            $numero_limpio = substr($numero_limpio, 1);
        }
        
        // Tomar últimos 9 dígitos
        $numero_limpio = substr($numero_limpio, -9);
        
        // Detectar operadora
        $operadora = $this->detectarOperadora($numero_limpio);
        
        // Email de la operadora
        $email_operadora = $numero_limpio . $this->operadoras[$operadora];
        
        // Enviar email (configurar SMTP primero)
        $asunto = "Notificación Boutique Sixta";
        
        // Usar PHPMailer configurado
        require_once 'config/mail.php';
        $configMail = new ConfigMail();
        $mail = $configMail->crearInstancia();
        
        if ($mail) {
            $mail->addAddress($email_operadora);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            $mail->AltBody = $mensaje;
            
            return $mail->send();
        }
        
        return false;
    }
    
    /**
     * Mensaje corto para SMS
     */
    public function crearMensajeContacto($nombre, $asunto) {
        return "Boutique Sixta: Hola $nombre, recibimos tu consulta sobre $asunto. Te contactaremos pronto. ID#" . time();
    }
}

// Instancia global
$sms_simple = new SMSSimple();
?>