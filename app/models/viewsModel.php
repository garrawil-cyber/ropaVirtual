<?php
	
namespace app\models;

class viewsModel{

	/*---------- Modelo obtener vista ----------*/
	protected function obtenerVistasModelo($vista){

		$listaBlanca = ["dashboard", "userNew", "userList", "userPhoto", "logOut",
						"categoryList", "categoryNew", "kardexProductos",
						"productNew", "productList",  
						"clientList", "clientNew",  
						"providerNew", "providerList", 
						"purchaseNew", "purchaseList", 
						"saleNew", "saleList",  "orderList","ordeNew",
						"reportSaleDate", "reportSaleClient", "reportSaleProduct",
						"reportPurchaseDate", "reportPurchaseProvider", "reportProducts",
						"profile", "settings"];

		// Primero manejar casos especiales
		if($vista == "login" || $vista == "index"){
			return "login";
		}
		
		// Luego verificar la lista blanca
		if(in_array($vista, $listaBlanca)){
			// Verificar si el archivo existe en la ruta correcta
			$rutaVista = APP_PATH . "app/views/content/" . $vista . "-view.php";
			
			if(is_file($rutaVista)){
				return $rutaVista;
			} else {
				return APP_PATH . "app/views/content/404-view.php";
			}
		} else {
			return APP_PATH . "app/views/content/404-view.php";
		}
	}

}