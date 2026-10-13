<?php
	require_once dirname(__DIR__,2)."/config/app.php";	
	require_once dirname(__DIR__,1)."/views/inc/session_start.php";
	require_once dirname(__DIR__,1)."app/autoloader.php";

	use app\controllers\clientController;

	if(isset($_POST['modulo_cliente'])){

		$insUsuario = new clientController();

		if($_POST['modulo_cliente']=="registrar"){
			echo $insUsuario->registrarClienteControlador();

		}

		if($_POST['modulo_cliente']=="eliminar"){
			echo $insUsuario->eliminarClienteControlador();
		}

		if($_POST['modulo_cliente']=="actualizar"){
			echo $insUsuario->actualizarClienteControlador();
		}
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}
	
    
    ?>