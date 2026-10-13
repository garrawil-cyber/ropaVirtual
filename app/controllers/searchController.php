<?php

namespace app\controllers;
use app\models\mainModel;

class searchController extends mainModel{

    /*----------  Controlador iniciar buscador  ----------*/
    public function iniciarBuscadorControlador(){
        
        if(isset($_POST['modulo_url']) && isset($_POST['txt_buscador'])){
            
            $url = $this->limpiarCadena($_POST['modulo_url']);
            $texto = $this->limpiarCadena($_POST['txt_buscador']);

            if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El texto de búsqueda no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
            }

            $_SESSION[$url] = $texto;

            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];

        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo procesar la petición",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }


    /*----------  Controlador eliminar buscador  ----------*/
    public function eliminarBuscadorControlador(){
        
        if(isset($_POST['modulo_url'])){
            
            $url = $this->limpiarCadena($_POST['modulo_url']);

            if(isset($_SESSION[$url])){
                unset($_SESSION[$url]);
            }

            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];

        }else{
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo procesar la petición",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }
}