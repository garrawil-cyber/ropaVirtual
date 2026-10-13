<?php
namespace app\controllers;
use app\models\mainModel;

class clientController extends mainModel {

    /*----------  Controlador registrar cliente  ----------*/
    public function registrarClienteControlador() {
        
        # Almacenando datos #
        $nombre = $this->limpiarCadena($_POST['cliente_nombre']);
        $apellidos = $this->limpiarCadena($_POST['cliente_apellidos']);
        $dni = $this->limpiarCadena($_POST['cliente_dni']);
        $email = $this->limpiarCadena($_POST['cliente_email']);
        $telefono = $this->limpiarCadena($_POST['cliente_telefono']);
        $direccion = $this->limpiarCadena($_POST['cliente_direccion']);

        # Verificando campos obligatorios #
        if($nombre == "" || $apellidos == "" || $dni == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}", $apellidos)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El APELLIDO no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando DNI #
        if($this->verificarDatos("[0-9]{8}", $dni)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El DNI no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando DNI no se repita #
        $check_dni = $this->ejecutarConsulta("SELECT dni FROM clientes WHERE dni='$dni'");
        if($check_dni->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El DNI ingresado ya se encuentra registrado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando email #
        if($email != "") {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("SELECT email FROM clientes WHERE email='$email'");
                if($check_email->rowCount() > 0) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El EMAIL ingresado ya se encuentra registrado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Ha ingresado un correo electrónico no válido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        # Verificando teléfono #
        if($telefono != "") {
            if($this->verificarDatos("[0-9()+]{7,20}", $telefono)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El TELÉFONO no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        # Preparar datos para insertar #
        $cliente_datos_reg = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "apellidos",
                "campo_marcador" => ":Apellidos",
                "campo_valor" => $apellidos
            ],
            [
                "campo_nombre" => "dni",
                "campo_marcador" => ":Dni",
                "campo_valor" => $dni
            ],
            [
                "campo_nombre" => "email",
                "campo_marcador" => ":Email",
                "campo_valor" => $email
            ],
            [
                "campo_nombre" => "telefono",
                "campo_marcador" => ":Telefono",
                "campo_valor" => $telefono
            ],
            [
                "campo_nombre" => "direccion",
                "campo_marcador" => ":Direccion",
                "campo_valor" => $direccion
            ]
        ];

        $registrar_cliente = $this->guardarDatos("clientes", $cliente_datos_reg);

        if($registrar_cliente->rowCount() == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Cliente registrado",
                "texto" => "El cliente " . $nombre . " " . $apellidos . " se registró con éxito",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo registrar el cliente, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador listar cliente  ----------*/
    public function listarClienteControlador($pagina, $registros, $url, $busqueda) {
        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        // Consulta sin limitación de paginación - DataTables se encargará
        if(isset($busqueda) && $busqueda != "") {
            $consulta_datos = "SELECT * FROM clientes WHERE (nombre LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR dni LIKE '%$busqueda%') ORDER BY apellidos ASC";
        } else {
            $consulta_datos = "SELECT * FROM clientes ORDER BY id ASC";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = count($datos);

        $tabla .= '
        <div class="container-fluid pt-4 px-4">
            <div class="bg-light text-center rounded p-4 shadow">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <a href="'.APP_URL.'clientNew/" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Cliente
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="tablaClientes" class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-dark">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">NOMBRE</th>
                                <th scope="col" class="text-center">APELLIDOS</th>
                                <th scope="col" class="text-center">DNI</th>
                                <th scope="col" class="text-center">EMAIL</th>
                                <th scope="col" class="text-center">TELEFONO</th>
                                <th scope="col" class="text-center">DIRECCION</th>
                                <th scope="col" class="text-center">EDITAR</th>
                                <th scope="col" class="text-center">ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>
        ';

        if($total >= 1) {
            $contador = 1;
            foreach($datos as $rows) {
                $tabla .= '
                    <tr class="text-center">
                        <td>'.$contador.'</td>
                        <td>'.$rows['nombre'].'</td>
                        <td>'.$rows['apellidos'].'</td>
                        <td>'.$rows['dni'].'</td>
                        <td>'.$rows['email'].'</td>
                        <td>'.$rows['telefono'].'</td>
                        <td>'.$rows['direccion'].'</td>
                        <td>
                            <a href="'.APP_URL.'clientUpdate/'.$rows['id'].'/" class="btn btn-warning btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <form class="FormularioAjax d-inline" action="'.APP_URL.'app/ajax/clienteAjax.php" method="POST" autocomplete="off">
                                <input type="hidden" name="modulo_cliente" value="eliminar">
                                <input type="hidden" name="cliente_id" value="'.$rows['id'].'">
                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                ';
                $contador++;
            }
        } else {
            $tabla .= '
                <tr class="text-center">
                    <td colspan="9">
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-info-circle"></i> No hay registros en el sistema
                        </div>
                    </td>
                </tr>
            ';
        }

        $tabla .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        ';

        return $tabla;
    }

    /*----------  Controlador eliminar cliente  ----------*/
    public function eliminarClienteControlador() {

        $id = $this->limpiarCadena($_POST['cliente_id']);

        # Verificando cliente #
        $datos = $this->ejecutarConsulta("SELECT * FROM clientes WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el cliente en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
        } else {
            $datos = $datos->fetch();
        }

        $eliminarCliente = $this->eliminarRegistro("clientes", "id", $id);

        if($eliminarCliente->rowCount() == 1) {

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Cliente eliminado",
                "texto" => "El cliente ".$datos['nombre']." ".$datos['apellidos']." ha sido eliminado del sistema correctamente",
                "icono" => "success"
            ];

        } else {

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido eliminar el cliente ".$datos['nombre']." ".$datos['apellidos']." del sistema, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador actualizar cliente  ----------*/
    public function actualizarClienteControlador() {

        $id = $this->limpiarCadena($_POST['cliente_id']);

        # Verificando cliente #
        $datos = $this->ejecutarConsulta("SELECT * FROM clientes WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el cliente en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos #
        $nombre = $this->limpiarCadena($_POST['cliente_nombre']);
        $apellidos = $this->limpiarCadena($_POST['cliente_apellidos']);
        $dni = $this->limpiarCadena($_POST['cliente_dni']);
        $email = $this->limpiarCadena($_POST['cliente_email']);
        $telefono = $this->limpiarCadena($_POST['cliente_telefono']);
        $direccion = $this->limpiarCadena($_POST['cliente_direccion']);
   
        # Verificando campos obligatorios #
        if($nombre == "" || $apellidos == "" || $dni == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos que son obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,100}", $apellidos)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El APELLIDO no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Validar datos del campo telefono y dni #
        if($telefono != "") {
            if($this->verificarDatos("[0-9()+]{9,20}", $telefono)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El TELÉFONO no coincide con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
            }
        }       
                
        if($this->verificarDatos("[0-9]{8}", $dni)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El DNI no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando email #
        if($email != "" && $datos['email'] != $email) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("SELECT email FROM clientes WHERE email='$email'");
                if($check_email->rowCount() > 0) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                }
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Ha ingresado un correo electrónico no válido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
            }
        }

        # Verificando DNI no se repita #
        if($datos['dni'] != $dni) {
            $check_dni = $this->ejecutarConsulta("SELECT dni FROM clientes WHERE dni='$dni'");
            if($check_dni->rowCount() > 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El DNI ingresado ya se encuentra registrado, por favor elija otro",
                    "icono" => "error"
                ];
                return json_encode($alerta);
            }
        }

        $cliente_datos_up = [
            [
                "campo_nombre" => "nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "apellidos",
                "campo_marcador" => ":Apellidos",
                "campo_valor" => $apellidos
            ],
            [
                "campo_nombre" => "dni",
                "campo_marcador" => ":Dni",
                "campo_valor" => $dni
            ],
            [
                "campo_nombre" => "email",
                "campo_marcador" => ":Email",
                "campo_valor" => $email
            ],
            [
                "campo_nombre" => "telefono",
                "campo_marcador" => ":Telefono",
                "campo_valor" => $telefono
            ],
            [
                "campo_nombre" => "direccion",
                "campo_marcador" => ":Direccion",
                "campo_valor" => $direccion
            ]
        ];

        $condicion = [
            "condicion_campo" => "id",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if($this->actualizarDatos("clientes", $cliente_datos_up, $condicion)) {

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Cliente actualizado",
                "texto" => "Los datos del cliente ".$datos['nombre']." ".$datos['apellidos']." se actualizaron correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido actualizar los datos del cliente ".$datos['nombre']." ".$datos['apellidos'].", por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

}
?>