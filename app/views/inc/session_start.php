<?php

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Regenerar ID de sesión periódicamente para mayor seguridad
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Tiempo de vida de la sesión (en segundos) - 2 horas
$inactive = 7200;

// Verificar inactividad del usuario
if (isset($_SESSION['timeout'])) {
    $session_life = time() - $_SESSION['timeout'];
    if ($session_life > $inactive) {
        session_destroy();
        header("Location: " . APP_URL . "login/");
        exit();
    }
}

// Actualizar tiempo de última actividad
$_SESSION['timeout'] = time();

// Función para verificar si el usuario está logueado
function verificarSesion() {
    if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
        return false;
    }
    return true;
}

// Función para obtener datos de sesión
function obtenerDatosSesion($campo) {
    if (isset($_SESSION[$campo])) {
        return $_SESSION[$campo];
    }
    return null;
}

// Función para cerrar sesión
function cerrarSesion() {
    session_unset();
    session_destroy();
    header("Location: " . APP_URL . "login/");
    exit();
}

// Función para establecer datos de sesión
function establecerSesion($campo, $valor) {
    $_SESSION[$campo] = $valor;
}

// Protección CSRF - Token de seguridad
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Función para generar token CSRF
function generarTokenCSRF() {
    return $_SESSION['token'];
}

// Función para verificar token CSRF
function verificarTokenCSRF($token) {
    if (!isset($_SESSION['token']) || $token !== $_SESSION['token']) {
        return false;
    }
    return true;
}
?>


