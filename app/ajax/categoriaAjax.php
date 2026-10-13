
<?php
require_once dirname(__DIR__, 2) . "/config/app.php";	
require_once dirname(__DIR__, 2) . "/app/views/inc/session_start.php";
require_once dirname(__DIR__, 1) . "/admin/autoloader.php";

use app\controllers\categoryController;

if (!isset($_SESSION['usuario'])) {
    $alerta = [
        "tipo" => "simple",
        "titulo" => "Sesión no válida",
        "texto" => "Debes iniciar sesión para realizar esta acción",
        "icono" => "error"
    ];
    exit();
}

$insCategoria = new categoryController();

if(isset($_POST['modulo_categoria'])) {
    
    if($_POST['modulo_categoria'] == "registrar") {
        echo $insCategoria->registrarCategoriaControlador();
    }

    if($_POST['modulo_categoria'] == "eliminar") {
        echo $insCategoria->eliminarCategoriaControlador();
    }

    if($_POST['modulo_categoria'] == "actualizar") {
        echo $insCategoria->actualizarCategoriaControlador();
    }
    
} else {
    $alerta = [
        "tipo" => "simple",
        "titulo" => "Ocurrió un error inesperado",
        "texto" => "No se ha especificado ninguna acción válida",
        "icono" => "error"
    ];
    
}

?>