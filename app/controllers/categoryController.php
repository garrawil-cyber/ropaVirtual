<?php

namespace app\controllers;
use app\models\mainModel;

class categoryController extends mainModel{

    /*----------  Controlador registrar categoría  ----------*/
    public function registrarCategoriaControlador(){

        # Almacenando datos#
        $nombre = $this->limpiarCadena($_POST['categoria_nombre']);
        $descripcion = $this->limpiarCadena($_POST['categoria_descripcion']);
        $activo = isset($_POST['categoria_estado']) ? 1 : 0;

        # Verificando campos obligatorios #
        if($nombre == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El nombre de la categoría es obligatorio",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando que no exista la categoría #
        $check_categoria = $this->ejecutarConsulta("SELECT nombre FROM categorias WHERE nombre='$nombre'");
        if($check_categoria->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La categoría '$nombre' ya existe en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Directorio de imágenes #
        $img_dir = APP_PATH . "views/img/categorias/";

        # Verificar si se seleccionó una imagen #
        if($_FILES['categoria_foto']['name'] != "" && $_FILES['categoria_foto']['size'] > 0) {

            # Crear directorio si no existe #
            if(!file_exists($img_dir)) {
                if(!mkdir($img_dir, 0777, true)) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Error al crear el directorio de imágenes",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }

            # Verificando formato de imágenes #
            if(mime_content_type($_FILES['categoria_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['categoria_foto']['tmp_name']) != "image/png") {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen debe ser JPG o PNG",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando peso de imagen #
            if(($_FILES['categoria_foto']['size'] / 1024) > 5120) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen supera el peso permitido (5MB)",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Extensión de imágenes #
            switch(mime_content_type($_FILES['categoria_foto']['tmp_name'])) {
                case 'image/jpeg':
                    $img_ext = ".jpg";
                    break;
                case 'image/png':
                    $img_ext = ".png";
                    break;
            }

            # Nombre final de la imagen #
            $img_nombre = $this->renombrar_fotos($nombre);
            $foto = $img_nombre . $img_ext;

            # Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES['categoria_foto']['tmp_name'], $img_dir . $foto)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo subir la imagen al sistema",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        } else {
            $foto = "";
        }

        # Preparando datos para insertar #
        $categoria_datos_reg = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "imagen",
                "campo_marcador" => ":Foto",
                "campo_valor" => $foto
            ],
            [
                "campo_nombre" => "activo",
                "campo_marcador" => ":Activo",
                "campo_valor" => $activo
            ]
        ];

        $registrar_categoria = $this->guardarDatos("categorias", $categoria_datos_reg);

        if($registrar_categoria->rowCount() == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Categoría registrada",
                "texto" => "La categoría '$nombre' se registró exitosamente",
                "icono" => "success"
            ];
        } else {
            
            if(is_file($img_dir . $foto)) {
                chmod($img_dir . $foto, 0777);
                unlink($img_dir . $foto);
            }

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo registrar la categoría",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador listar categoría  ----------*/
    public function listarCategoriaControlador($pagina,$registros,$url,$busqueda){

        $busqueda=$this->limpiarCadena($busqueda);
        $tabla="";

        // Consulta sin limitación de paginación - DataTables se encargará
        if(isset($busqueda) && $busqueda!=""){
            $consulta_datos="SELECT * FROM categorias WHERE (descripcion LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%') ORDER BY descripcion ASC";
        }else{
            $consulta_datos="SELECT * FROM categorias ORDER BY id ASC";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = count($datos);


        $tabla.='
        <div class="container-fluid pt-4 px-4">
            <div class="bg-light text-center rounded p-4 shadow">
                <div class="d-flex align-items-center justify-content-between mb-4">
                     <a href="'.APP_URL.'categoryNew/" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Categoría
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="tablaCategorias" class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">NOMBRE</th>
                                <th scope="col" class="text-center">DESCRIPCIÓN</th>
                                <th scope="col" class="text-center">IMAGEN</th>
                                <th scope="col" class="text-center">ACTIVO</th>
                                <th scope="col" class="text-center">EDITAR</th>
                                <th scope="col" class="text-center">ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>
        ';

        if($total>=1){
            $contador=1;
            foreach($datos as $rows){
                $tabla.='
                    <tr class="text-center">
                        <td>'.$contador.'</td>
                        <td>'.$rows['nombre'].'</td>
                        <td>'.$rows['descripcion'].'</td>
                        <td>';
                    
                        if($rows['imagen']!=""){
                            $tabla.='<img src="'.APP_URL.'app/views/img/categorias/'.$rows['imagen'].'" 
                            alt="'.htmlspecialchars($rows['nombre']).'"
                            class="rounded" 
                            width="50" 
                            height="50" 
                            style="object-fit: cover; cursor: pointer;"
                            onclick="verImagen(\''.APP_URL.'app/views/img/categorias/'.$rows['imagen'].'\', \''.addslashes($rows['descripcion']).'\')">';
                    }else{
                        $tabla.='<div class="rounded bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-white"></i>
                                </div>';
                    }
                    
                    $tabla.='
                        </td>
                        <td>';
                            if($rows['activo']==1){
                                $tabla.='<span class="badge bg-success">Activo</span>';
                            }else{
                                $tabla.='<span class="badge bg-danger">Inactivo</span>';
                            }
                    $tabla.='
                        </td>
                        <td>
                            <a href="'.APP_URL.'categoryUpdate/'.$rows['id'].'/" class="btn btn-warning btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.APP_URL.'app/ajax/categoriaAjax.php" method="POST" autocomplete="off" >
                                <input type="hidden" name="modulo_categoria" value="eliminar">
                                <input type="hidden" name="categoria_id" value="'.$rows['id'].'">
                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                ';
                $contador++;
            }
        }else{
            $tabla.='
                <tr class="text-center">
                    <td colspan="7">
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-info-circle"></i> No hay registros en el sistema
                        </div>
                    </td>
                </tr>
            ';
        }

        $tabla.='
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        ';

        return $tabla;
    }

    /*----------  Controlador eliminar categoría  ----------*/
    public function eliminarCategoriaControlador() {

        $id = $this->limpiarCadena($_POST['categoria_id']);

        # Verificando categoría #
        $datos = $this->ejecutarConsulta("SELECT * FROM categorias WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se encontró la categoría en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        # Verificar si hay productos asociados #
        $check_productos = $this->ejecutarConsulta("SELECT id FROM productos WHERE categoria_id='$id' LIMIT 1");
        if($check_productos->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "No se puede eliminar",
                "texto" => "Esta categoría tiene productos asociados. Elimina o reasigna los productos primero",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $eliminarCategoria = $this->eliminarRegistro("categorias", "id", $id);

        if($eliminarCategoria->rowCount() == 1) {

            # Eliminar imagen si existe #
            $img_dir = APP_PATH . "views/img/categorias/";
            if(is_file($img_dir . $datos['imagen'])) {
                chmod($img_dir . $datos['imagen'], 0777);
                unlink($img_dir . $datos['imagen']);
            }

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Categoría eliminada",
                "texto" => "La categoría '" . $datos['nombre'] . "' fue eliminada correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo eliminar la categoría",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador actualizar categoría  ----------*/
    public function actualizarCategoriaControlador() {
        
        $id = $this->limpiarCadena($_POST['categoria_id']);

        # Verificando categoría #
        $datos = $this->ejecutarConsulta("SELECT * FROM categorias WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se encontró la categoría en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos #
        $nombre = $this->limpiarCadena($_POST['categoria_nombre']);
        $descripcion = $this->limpiarCadena($_POST['categoria_descripcion']);
        $activo = isset($_POST['categoria_estado']) ? 1 : 0;

        # Verificando campos obligatorios #
        if($nombre == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El nombre de la categoría es obligatorio",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{3,100}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando nombre duplicado #
        if($nombre != $datos['nombre']) {
            $check_categoria = $this->ejecutarConsulta("SELECT nombre FROM categorias WHERE nombre='$nombre'");
            if($check_categoria->rowCount() > 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La categoría '$nombre' ya existe",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        # Directorio de imágenes #
        $img_dir = APP_PATH . "views/img/categorias/";

        # Verificar si se seleccionó una nueva imagen #
        if($_FILES['categoria_foto']['name'] != "" && $_FILES['categoria_foto']['size'] > 0) {

            # Crear directorio si no existe #
            if(!file_exists($img_dir)) {
                if(!mkdir($img_dir, 0777, true)) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Error al crear el directorio",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }

            # Verificando formato de imágenes #
            if(mime_content_type($_FILES['categoria_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['categoria_foto']['tmp_name']) != "image/png") {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen debe ser JPG o PNG",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando peso de imagen #
            if(($_FILES['categoria_foto']['size'] / 1024) > 5120) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen supera el peso permitido (5MB)",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Extensión de imágenes #
            switch(mime_content_type($_FILES['categoria_foto']['tmp_name'])) {
                case 'image/jpeg':
                    $img_ext = ".jpg";
                    break;
                case 'image/png':
                    $img_ext = ".png";
                    break;
            }

            # Nombre final de la imagen #
            $img_nombre = $this->renombrar_fotos($nombre);
            $foto = $img_nombre . $img_ext;

            # Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES['categoria_foto']['tmp_name'], $img_dir . $foto)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo subir la imagen",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Eliminando imagen anterior #
            if(is_file($img_dir . $datos['imagen']) && $datos['imagen'] != $foto) {
                chmod($img_dir . $datos['imagen'], 0777);
                unlink($img_dir . $datos['imagen']);
            }

        } else {
            $foto = $datos['imagen'];
        }

        # Preparando datos para actualizar #
        $categoria_datos_up = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "descripcion",
                "campo_marcador" => ":Descripcion",
                "campo_valor" => $descripcion
            ],
            [
                "campo_nombre" => "imagen",
                "campo_marcador" => ":Foto",
                "campo_valor" => $foto
            ],
            [
                "campo_nombre" => "activo",
                "campo_marcador" => ":Activo",
                "campo_valor" => $activo
            ]
        ];

        $condicion = [
            "condicion_campo" => "id",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if($this->actualizarDatos("categorias", $categoria_datos_up, $condicion)) {
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Categoría actualizada",
                "texto" => "La categoría '$nombre' se actualizó correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo actualizar la categoría",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }
}
?>