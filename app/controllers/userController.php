<?php

namespace app\controllers;
use app\models\mainModel;

class userController extends mainModel{

    /*----------  Controlador registrar usuario  ----------*/
    public function registrarUsuarioControlador(){

        # Almacenando datos#
        $nombre=$this->limpiarCadena($_POST['usuario_nombre']);
        $apellido=$this->limpiarCadena($_POST['usuario_apellido']);

        $usuario=$this->limpiarCadena($_POST['usuario_usuario']);
        $email=$this->limpiarCadena($_POST['usuario_email']);
        $clave1=$this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2=$this->limpiarCadena($_POST['usuario_clave_2']);

        # Verificando campos obligatorios #
        if($nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El NOMBRE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El APELLIDO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El USUARIO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave2)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Las CLAVES no coinciden con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando email #
        if($email!=""){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_email=$this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                }
            }else{
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Ha ingresado un correo electrónico no válido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
        }

        # Verificando claves #
        if($clave1!=$clave2){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Las contraseñas que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }else{
            $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]);
        }

        # Verificando usuario #
        $check_usuario=$this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
        if($check_usuario->rowCount()>0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Directorio de imagenes #
        $img_dir="../views/fotos/";

        # Comprobar si se selecciono una imagen #
        if($_FILES['usuario_foto']['name']!="" && $_FILES['usuario_foto']['size']>0){

            # Creando directorio #
            if(!file_exists($img_dir)){
                if(!mkdir($img_dir,0777)){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"Error al crear el directorio",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                } 
            }

            # Verificando formato de imagenes #
            if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"La imagen que ha seleccionado es de un formato no permitido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }

            # Verificando peso de imagen #
            if(($_FILES['usuario_foto']['size']/1024)>5120){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"La imagen que ha seleccionado supera el peso permitido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }

            # Nombre de la foto #
            $foto=str_ireplace(" ","_",$nombre);
            $foto=$foto."_".rand(0,100);

            # Extension de la imagen #
            switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
                case 'image/jpeg':
                    $foto=$foto.".jpg";
                break;
                case 'image/png':
                    $foto=$foto.".png";
                break;
            }

            chmod($img_dir,0777);

            # Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"No podemos subir la imagen al sistema en este momento",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }

        }else{
            $foto="";
        }

        $usuario_datos_reg=[
            [
                "campo_nombre"=>"usuario_nombre",
                "campo_marcador"=>":Nombre",
                "campo_valor"=>$nombre
            ],
            [
                "campo_nombre"=>"usuario_apellido",
                "campo_marcador"=>":Apellido",
                "campo_valor"=>$apellido
            ],
            [
                "campo_nombre"=>"usuario_usuario",
                "campo_marcador"=>":Usuario",
                "campo_valor"=>$usuario
            ],
            [
                "campo_nombre"=>"usuario_email",
                "campo_marcador"=>":Email",
                "campo_valor"=>$email
            ],
            [
                "campo_nombre"=>"usuario_clave",
                "campo_marcador"=>":Clave",
                "campo_valor"=>$clave
            ],
            [
                "campo_nombre"=>"usuario_foto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>$foto
            ],
            [
                "campo_nombre"=>"usuario_creado",
                "campo_marcador"=>":Creado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $registrar_usuario=$this->guardarDatos("usuario",$usuario_datos_reg);

        if($registrar_usuario->rowCount()==1){
            $alerta=[
				"tipo"=>"redireccionar", // CORREGIDO: Cambiado de "limpiar" a "redireccionar"
                //"tipo"=>"limpiar",
                "titulo"=>"Usuario registrado",
                "texto"=>"El usuario ".$nombre." ".$apellido." se registro con éxito",
                "icono"=>"success",
                "url"=>APP_URL."userList/"
            ];
        }else{
            
            if(is_file($img_dir.$foto)){
                chmod($img_dir.$foto,0777);
                unlink($img_dir.$foto);
            }

            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No se pudo registrar el usuario, por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }
/*----------  Controlador listar usuario (CORREGIDO PARA DATATABLES)  ----------*/
public function listarUsuarioControlador($pagina,$registros,$url,$busqueda){

    $busqueda=$this->limpiarCadena($busqueda);
    $tabla="";
    // Consulta sin limitación de paginación - DataTables se encargará
    if(isset($busqueda) && $busqueda!=""){
        $consulta_datos="SELECT * FROM usuario WHERE ((usuario_id!='".$_SESSION['id']."' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC";
    }else{
        $consulta_datos="SELECT * FROM usuario WHERE usuario_id!='".$_SESSION['id']."' AND usuario_id!='1' ORDER BY usuario_nombre ASC";
    }

    $datos = $this->ejecutarConsulta($consulta_datos);
    $datos = $datos->fetchAll();

    $total = count($datos);

    $tabla.='
    
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light text-center rounded p-4 shadow">
            <div class="d-flex align-items-center justify-content-between mb-4">
                 <a href="'.APP_URL.'userNew/" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Usuario
                </a>
            </div>
            <div class="table-responsive">
                <table id="tablaUsuarios" class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-dark">
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">NOMBRE</th>
                            <th scope="col" class="text-center">USUARIO</th>
                            <th scope="col" class="text-center">EMAIL</th>
                            <th scope="col" class="text-center">CREADO</th>
                       
                            <th scope="col" class="text-center">FOTO</th>
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
                    <td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
                    <td>'.$rows['usuario_usuario'].'</td>
                    <td>'.$rows['usuario_email'].'</td>
                    <td>'.date("d-m-Y h:i A",strtotime($rows['usuario_creado'])).'</td>
                    <td>';
                
            if($rows['usuario_foto']!=""){
                $tabla.='<img src="'.APP_URL.'app/views/fotos/'.$rows['usuario_foto'].'" 
                alt="Foto usuario" 
                class="rounded-circle" 
                width="40" 
                height="40" 
                style="object-fit: cover; cursor: pointer;"
                onclick="verImagen(\''.APP_URL.'app/views/fotos/'.$rows['usuario_foto'].'\', \''.addslashes($rows['usuario_nombre'].' '.$rows['usuario_apellido']).'\')">';
            }else{
                $tabla.='<div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-user text-white"></i>
                </div>';
            }
                
                $tabla.='
                    </td>
                    <td>
                        <a href="'.APP_URL.'userUpdate/'.$rows['usuario_id'].'/" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off">
                            <input type="hidden" name="modulo_usuario" value="eliminar">
                            <input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">
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
                <td colspan="9">
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
    
    /*----------  Controlador eliminar usuario  ----------*/
    public function eliminarUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']);

        if($id==1){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No podemos eliminar el usuario principal del sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }else{
            $datos=$datos->fetch();
        }

        $eliminarUsuario=$this->eliminarRegistro("usuario","usuario_id",$id);

        if($eliminarUsuario->rowCount()==1){

            if(is_file("../views/fotos/".$datos['usuario_foto'])){
                chmod("../views/fotos/".$datos['usuario_foto'],0777);
                unlink("../views/fotos/".$datos['usuario_foto']);
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Usuario eliminado",
                "texto"=>"El usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." ha sido eliminado del sistema correctamente",
                "icono"=>"success"
            ];

        }else{

            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido eliminar el usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." del sistema, por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador actualizar usuario  ----------*/
    public function actualizarUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }else{
            $datos=$datos->fetch();
        }

        $admin_usuario=$this->limpiarCadena($_POST['administrador_usuario']);
        $admin_clave=$this->limpiarCadena($_POST['administrador_clave']);

        # Verificando campos obligatorios admin #
        if($admin_usuario=="" || $admin_clave==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No ha llenado todos los campos que son obligatorios, que corresponden a su USUARIO y CLAVE",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Su USUARIO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"Su CLAVE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando administrador #
        $check_admin=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_id='".$_SESSION['id']."'");
        if($check_admin->rowCount()==1){

            $check_admin=$check_admin->fetch();

            if($check_admin['usuario_usuario']!=$admin_usuario || !password_verify($admin_clave,$check_admin['usuario_clave'])){

                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"USUARIO o CLAVE de administrador incorrectos",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"USUARIO o CLAVE de administrador incorrectos",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Almacenando datos#
        $nombre=$this->limpiarCadena($_POST['usuario_nombre']);
        $apellido=$this->limpiarCadena($_POST['usuario_apellido']);

        $usuario=$this->limpiarCadena($_POST['usuario_usuario']);
        $email=$this->limpiarCadena($_POST['usuario_email']);
        $clave1=$this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2=$this->limpiarCadena($_POST['usuario_clave_2']);

        # Verificando campos obligatorios #
        if($nombre=="" || $apellido=="" || $usuario==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No has llenado todos los campos que son obligatorios",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El NOMBRE no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El APELLIDO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"El USUARIO no coincide con el formato solicitado",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando email #
        if($email!="" && $datos['usuario_email']!=$email){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_email=$this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                }
            }else{
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Ha ingresado un correo electrónico no válido",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
        }

        # Verificando claves #
        if($clave1!="" || $clave2!=""){
            if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave2)){

                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Las CLAVES no coinciden con el formato solicitado",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }else{
                if($clave1!=$clave2){

                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"Las nuevas CLAVES que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                }else{
                    $clave=password_hash($clave1,PASSWORD_BCRYPT,["cost"=>10]);
                }
            }
        }else{
            $clave=$datos['usuario_clave'];
        }

        # Verificando usuario #
        if($datos['usuario_usuario']!=$usuario){
            $check_usuario=$this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
            if($check_usuario->rowCount()>0){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
        }

        $usuario_datos_up=[
            [
                "campo_nombre"=>"usuario_nombre",
                "campo_marcador"=>":Nombre",
                "campo_valor"=>$nombre
            ],
            [
                "campo_nombre"=>"usuario_apellido",
                "campo_marcador"=>":Apellido",
                "campo_valor"=>$apellido
            ],
            [
                "campo_nombre"=>"usuario_usuario",
                "campo_marcador"=>":Usuario",
                "campo_valor"=>$usuario
            ],
            [
                "campo_nombre"=>"usuario_email",
                "campo_marcador"=>":Email",
                "campo_valor"=>$email
            ],
            [
                "campo_nombre"=>"usuario_clave",
                "campo_marcador"=>":Clave",
                "campo_valor"=>$clave
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $condicion=[
            "condicion_campo"=>"usuario_id",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

            if($id==$_SESSION['id']){
                $_SESSION['nombre']=$nombre;
                $_SESSION['apellido']=$apellido;
                $_SESSION['usuario']=$usuario;
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Usuario actualizado",
                "texto"=>"Los datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se actualizaron correctamente",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos podido actualizar los datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido'].", por favor intente nuevamente",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador eliminar foto usuario  ----------*/
    public function eliminarFotoUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }else{
            $datos=$datos->fetch();
        }

        # Directorio de imagenes #
        $img_dir="../views/fotos/";

        chmod($img_dir,0777);

        if(is_file($img_dir.$datos['usuario_foto'])){

            chmod($img_dir.$datos['usuario_foto'],0777);

            if(!unlink($img_dir.$datos['usuario_foto'])){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al intentar eliminar la foto del usuario, por favor intente nuevamente",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            }
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado la foto del usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        $usuario_datos_up=[
            [
                "campo_nombre"=>"usuario_foto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>""
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $condicion=[
            "condicion_campo"=>"usuario_id",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

            if($id==$_SESSION['id']){
                $_SESSION['foto']="";
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se elimino correctamente",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto eliminada",
                "texto"=>"No hemos podido actualizar algunos datos del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido'].", sin embargo la foto ha sido eliminada correctamente",
                "icono"=>"warning"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador actualizar foto usuario  ----------*/
    public function actualizarFotoUsuarioControlador(){

        $id=$this->limpiarCadena($_POST['usuario_id']);

        # Verificando usuario #
        $datos=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if($datos->rowCount()<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No hemos encontrado el usuario en el sistema",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }else{
            $datos=$datos->fetch();
        }

        # Directorio de imagenes #
        $img_dir="../views/fotos/";

        # Comprobar si se selecciono una imagen #
        if($_FILES['usuario_foto']['name']=="" && $_FILES['usuario_foto']['size']<=0){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No ha seleccionado una foto para el usuario",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Creando directorio #
        if(!file_exists($img_dir)){
            if(!mkdir($img_dir,0777)){
                $alerta=[
                    "tipo"=>"simple",
                    "titulo"=>"Ocurrió un error inesperado",
                    "texto"=>"Error al crear el directorio",
                    "icono"=>"error"
                ];
                return json_encode($alerta);
            } 
        }

        # Verificando formato de imagenes #
        if(mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name'])!="image/png"){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La imagen que ha seleccionado es de un formato no permitido",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Verificando peso de imagen #
        if(($_FILES['usuario_foto']['size']/1024)>5120){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"La imagen que ha seleccionado supera el peso permitido",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        # Nombre de la foto #
        if($datos['usuario_foto']!=""){
            $foto=explode(".", $datos['usuario_foto']);
            $foto=$foto[0];
        }else{
            $foto=str_ireplace(" ","_",$datos['usuario_nombre']);
            $foto=$foto."_".rand(0,100);
        }

        # Extension de la imagen #
        switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){
            case 'image/jpeg':
                $foto=$foto.".jpg";
            break;
            case 'image/png':
                $foto=$foto.".png";
            break;
        }

        chmod($img_dir,0777);

        # Eliminar imagen anterior #
        if(is_file($img_dir.$datos['usuario_foto']) && $datos['usuario_foto']!=$foto){
            chmod($img_dir.$datos['usuario_foto'],0777);
            unlink($img_dir.$datos['usuario_foto']);
        }

        # Moviendo imagen al directorio #
        if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'],$img_dir.$foto)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No podemos subir la imagen al sistema en este momento",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        $usuario_datos_up=[
            [
                "campo_nombre"=>"usuario_foto",
                "campo_marcador"=>":Foto",
                "campo_valor"=>$foto
            ],
            [
                "campo_nombre"=>"usuario_actualizado",
                "campo_marcador"=>":Actualizado",
                "campo_valor"=>date("Y-m-d H:i:s")
            ]
        ];

        $condicion=[
            "condicion_campo"=>"usuario_id",
            "condicion_marcador"=>":ID",
            "condicion_valor"=>$id
        ];

        if($this->actualizarDatos("usuario",$usuario_datos_up,$condicion)){

            if($id==$_SESSION['id']){
                $_SESSION['foto']=$foto;
            }

            $alerta=[
                "tipo"=>"recargar",
                "titulo"=>"Foto actualizada",
                "texto"=>"La foto del usuario ".$datos['usuario_nombre']." ".$datos['usuario_apellido']." se actualizo correctamente",
                "icono"=>"success"
            ];
        }else{

            if(is_file($img_dir.$foto)){
                chmod($img_dir.$foto,0777);
                unlink($img_dir.$foto);
            }

            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Ocurrió un error inesperado",
                "texto"=>"No podemos subir la imagen al sistema en este momento",
                "icono"=>"error"
            ];
        }

        return json_encode($alerta);
    }
}
?>