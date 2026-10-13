<?php
/**
 * Archivo principal del PANEL ADMINISTRATIVO
 */

// Configuración de rutas
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/ropaVirtual_mvc';

require_once $rootPath . '/config/database.php';
require_once $rootPath . '/config/app.php';
require_once "autoloader.php";

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar la variable $url correctamente
if(isset($_GET['views']) && !empty($_GET['views'])){
    $url = explode("/", $_GET['views']);
    $url = array_filter($url);
    $url = array_values($url);
} else {
    $url = ["login"];
}

// Importar clases necesarias
use app\controllers\viewsController;
use app\controllers\loginController;

// Instanciar controladores
try {
    $insLogin = new loginController();
    $viewsController = new viewsController();
} catch (Error $e) {
    die("Error al cargar controladores: " . $e->getMessage());
}

// Obtener la vista solicitada
$vista = $viewsController->obtenerVistasControlador($url[0]);

// PASAR CONSTANTES A LAS VISTAS definiéndolas como variables globales
$GLOBALS['APP_URL'] = APP_URL;
$GLOBALS['VIEW_URL'] = VIEW_URL;
$GLOBALS['APP_NAME'] = APP_NAME;

// Manejo de vistas
if($vista == "login" || $vista == "404"){
    // ✅ CORRECCIÓN: Usar $rootPath correctamente
    $vistaArchivo = $rootPath . "/app/views/content/" . $vista . "-view.php";
    
    if(file_exists($vistaArchivo)){
        require_once $vistaArchivo;
    } else {
        echo "<h1>Error: Vista no encontrada</h1>";
        echo "<p>Archivo buscado: " . $vistaArchivo . "</p>";
        echo "<p>¿Existe el archivo? " . (file_exists($vistaArchivo) ? "SÍ" : "NO") . "</p>";
    }
} else {
    # Cerrar sesión si no hay usuario autenticado #
    if(!isset($_SESSION['id']) || $_SESSION['id']=="" || !isset($_SESSION['usuario']) || $_SESSION['usuario']==""){
        $insLogin->cerrarSesionControlador();
        exit();
    }
    
    // Verificar que la vista exista antes de incluirla
    if(file_exists($vista)){
        require_once $vista;
    } else {
        echo "<h1>Error: Vista no encontrada</h1>";
        echo "<p>Archivo buscado: " . $vista . "</p>";
    }
}
?>