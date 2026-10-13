<?php
// Asegurar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: " . APP_URL . "login/");
    exit();
}

use app\controllers\clientController;
$insUsuario = new clientController();


 require_once APP_PATH . "app/views/inc/head.php"; 
// Incluir navbar.php usando ruta del sistema de archivos
$navbar_path = __DIR__ . '/../inc/navbar.php';
	
if (file_exists($navbar_path)) {
    require_once $navbar_path;
} else {
    die("Error: No se pudo cargar el navbar");
}


// Llamar al método sin parámetros de paginación ya que DataTables lo maneja
echo $insUsuario->listarClienteControlador("", "", "", "");

#cargar el archivo footer.php usando ruta del sistema de archivos
$footer_path = __DIR__ . '/../inc/footer.php';
	
if (file_exists($footer_path)) {
	require_once $footer_path;
} else {
	die("Error: No se pudo cargar el footer");
}
?>




