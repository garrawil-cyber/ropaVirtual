<?php
namespace app\controllers;
use app\models\mainModel;

class productController extends mainModel {

    /*----------  Controlador registrar producto  ----------*/
    public function registrarProductoControlador() {
        
        # Almacenando datos #
        $nombre = $this->limpiarCadena($_POST['producto_nombre']);
        $descripcion = $this->limpiarCadena($_POST['producto_descripcion']);
        $precio = $this->limpiarCadena($_POST['producto_precio']);
        $precio_oferta = $this->limpiarCadena($_POST['producto_precio_oferta']);
        $categoria_id = $this->limpiarCadena($_POST['producto_categoria_id']);
        $stock = $this->limpiarCadena($_POST['producto_stock']);
        $tallas = $this->limpiarCadena($_POST['producto_tallas']);
        $colores = $this->limpiarCadena($_POST['producto_colores']);
        $destacado = isset($_POST['producto_destacado']) ? 1 : 0;
        $activo = isset($_POST['producto_activo']) ? 1 : 0;

        # Verificando campos obligatorios #
        if($nombre == "" || $precio == "" || $categoria_id == "" || $stock == "") {
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
        if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ.,\-\s]{3,200}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando precio #
        if(!is_numeric($precio) || $precio < 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El PRECIO debe ser un número válido",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando precio oferta #
        if($precio_oferta != "" && (!is_numeric($precio_oferta) || $precio_oferta < 0)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El PRECIO DE OFERTA debe ser un número válido",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando categoría #
        if($this->verificarDatos("[0-9]{1,10}", $categoria_id)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La CATEGORÍA no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando que la categoría existe #
        $check_categoria = $this->ejecutarConsulta("SELECT id FROM categorias WHERE id='$categoria_id'");
        if($check_categoria->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La CATEGORÍA seleccionada no existe en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando stock #
        if($this->verificarDatos("[0-9]{1,10}", $stock)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El STOCK no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Directorio de imágenes #
        $img_dir = "../app/views/img/productos/";

        # Verificar si se seleccionó una imagen #
        if($_FILES['producto_imagen']['name'] != "" && $_FILES['producto_imagen']['size'] > 0) {

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
            if(mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/jpeg" && 
               mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/png" && 
               mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/webp") {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen que ha seleccionado es de un formato no permitido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando peso de imagen #
            if(($_FILES['producto_imagen']['size'] / 1024) > 5120) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen seleccionada supera el peso permitido (5MB)",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Extensión de imágenes #
            switch(mime_content_type($_FILES['producto_imagen']['tmp_name'])) {
                case 'image/jpeg':
                    $img_ext = ".jpg";
                    break;
                case 'image/png':
                    $img_ext = ".png";
                    break;
                case 'image/webp':
                    $img_ext = ".webp";
                    break;
            }

            # Nombre final de la imagen #
            $img_nombre = $this->renombrar_fotos($nombre);
            $imagen = $img_nombre . $img_ext;

            # Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES['producto_imagen']['tmp_name'], $img_dir . $imagen)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No podemos subir la imagen al sistema en este momento",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        } else {
            $imagen = "";
        }

        # Preparar datos para insertar #
        $producto_datos_reg = [
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
                "campo_nombre" => "precio",
                "campo_marcador" => ":Precio",
                "campo_valor" => $precio
            ],
            [
                "campo_nombre" => "precio_oferta",
                "campo_marcador" => ":PrecioOferta",
                "campo_valor" => $precio_oferta
            ],
            [
                "campo_nombre" => "categoria_id",
                "campo_marcador" => ":CategoriaId",
                "campo_valor" => $categoria_id
            ],
            [
                "campo_nombre" => "imagen",
                "campo_marcador" => ":Imagen",
                "campo_valor" => $imagen
            ],
            [
                "campo_nombre" => "stock",
                "campo_marcador" => ":Stock",
                "campo_valor" => $stock
            ],
            [
                "campo_nombre" => "tallas",
                "campo_marcador" => ":Tallas",
                "campo_valor" => $tallas
            ],
            [
                "campo_nombre" => "colores",
                "campo_marcador" => ":Colores",
                "campo_valor" => $colores
            ],
            [
                "campo_nombre" => "destacado",
                "campo_marcador" => ":Destacado",
                "campo_valor" => $destacado
            ],
            [
                "campo_nombre" => "activo",
                "campo_marcador" => ":Activo",
                "campo_valor" => $activo
            ]
        ];

        $registrar_producto = $this->guardarDatos("productos", $producto_datos_reg);

        if($registrar_producto->rowCount() == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Producto registrado",
                "texto" => "El producto " . $nombre . " se registró con éxito",
                "icono" => "success"
            ];
        } else {
            
            if(is_file($img_dir . $imagen)) {
                chmod($img_dir . $imagen, 0777);
                unlink($img_dir . $imagen);
            }

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo registrar el producto, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador listar producto  ----------*/
public function listarProductoControlador($pagina, $registros, $url, $busqueda) {
    $busqueda = $this->limpiarCadena($busqueda);
    $tabla = "";

    // Consulta sin limitación de paginación - DataTables se encargará
    if(isset($busqueda) && $busqueda != "") {
        $consulta_datos = "SELECT p.*, c.nombre as categoria_nombre 
                         FROM productos p 
                         INNER JOIN categorias c ON p.categoria_id = c.id 
                         WHERE (p.nombre LIKE '%$busqueda%' OR p.descripcion LIKE '%$busqueda%') 
                         ORDER BY p.id DESC";
    } else {
        $consulta_datos = "SELECT p.*, c.nombre as categoria_nombre 
                         FROM productos p 
                         INNER JOIN categorias c ON p.categoria_id = c.id 
                         ORDER BY p.id DESC";
    }

    $datos = $this->ejecutarConsulta($consulta_datos);
    $datos = $datos->fetchAll();

    $total = count($datos);

    $tabla .= '
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light text-center rounded p-4 shadow">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <a href="'.APP_URL.'productNew/" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Producto
                </a>
            </div>
            <div class="table-responsive">
                <table id="tablaProductos" class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-dark">
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">IMAGEN</th>
                            <th scope="col" class="text-center">NOMBRE</th>
                            <th scope="col" class="text-center">CATEGORÍA</th>
                            <th scope="col" class="text-center">PRECIO</th>
                            <th scope="col" class="text-center">PRECIO OFERTA</th>
                            <th scope="col" class="text-center">STOCK</th>
                            <th scope="col" class="text-center">DESTACADO</th>
                            <th scope="col" class="text-center">ESTADO</th>
                            <th scope="col" class="text-center">EDITAR</th>
                            <th scope="col" class="text-center">ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>
    ';

    if($total >= 1) {
        $contador = 1;
        foreach($datos as $rows) {

            # Imagen del producto #
            $rutaFisica = APP_PATH . "app/views/img/productos/" . $rows['imagen'];
            $urlImagen = APP_URL . "app/views/img/productos/";

            if(!empty($rows['imagen']) && is_file($rutaFisica)) {
                $imagen = '<img src="' . $urlImagen . $rows['imagen'] . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="' . htmlspecialchars($rows['nombre']) . '">';
            } else {
                $imagen = '<img src="' . $urlImagen . 'default.png" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="Sin imagen">';
            }

            # Estado destacado #
            $destacado = ($rows['destacado'] == 1) 
                ? '<span class="badge bg-warning">Destacado</span>' 
                : '<span class="badge bg-secondary">Normal</span>';

            # Estado activo #
            $activo = ($rows['activo'] == 1) 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>';

            # Precio oferta #
            $precio_oferta = ($rows['precio_oferta'] > 0) 
                ? 'S/ ' . number_format($rows['precio_oferta'], 2) 
                : '-';

            $tabla .= '
                <tr class="text-center">
                    <td>' . $contador . '</td>
                    <td>' . $imagen . '</td>
                    <td class="text-start">' . htmlspecialchars($rows['nombre']) . '</td>
                    <td>' . htmlspecialchars($rows['categoria_nombre']) . '</td>
                    <td>S/ ' . number_format($rows['precio'], 2) . '</td>
                    <td>' . $precio_oferta . '</td>
                    <td>' . $rows['stock'] . '</td>
                    <td>' . $destacado . '</td>
                    <td>' . $activo . '</td>
                    <td>
                        <a href="' . APP_URL . 'productUpdate/' . $rows['id'] . '/" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax d-inline" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off">
                            <input type="hidden" name="modulo_producto" value="eliminar">
                            <input type="hidden" name="producto_id" value="' . $rows['id'] . '">
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
                <td colspan="11">
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


    /*----------  Controlador eliminar producto  ----------*/
    public function eliminarProductoControlador() {

        $id = $this->limpiarCadena($_POST['producto_id']);

        # Verificando producto #
        $datos = $this->ejecutarConsulta("SELECT * FROM productos WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el producto en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
        } else {
            $datos = $datos->fetch();
        }

        # Eliminando imagen del producto #
        if(is_file("../app/views/img/productos/".$datos['imagen'])) {
            chmod("../app/views/img/productos/".$datos['imagen'], 0777);
            unlink("../app/views/img/productos/".$datos['imagen']);
        }

        $eliminarProducto = $this->eliminarRegistro("productos", "id", $id);

        if($eliminarProducto->rowCount() == 1) {

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Producto eliminado",
                "texto" => "El producto ".$datos['nombre']." ha sido eliminado del sistema correctamente",
                "icono" => "success"
            ];

        } else {

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido eliminar el producto ".$datos['nombre']." del sistema, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

    /*----------  Controlador actualizar producto  ----------*/
    public function actualizarProductoControlador() {

        $id = $this->limpiarCadena($_POST['producto_id']);

        # Verificando producto #
        $datos = $this->ejecutarConsulta("SELECT * FROM productos WHERE id='$id'");
        if($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el producto en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos #
        $nombre = $this->limpiarCadena($_POST['producto_nombre']);
        $descripcion = $this->limpiarCadena($_POST['producto_descripcion']);
        $precio = $this->limpiarCadena($_POST['producto_precio']);
        $precio_oferta = $this->limpiarCadena($_POST['producto_precio_oferta']);
        $categoria_id = $this->limpiarCadena($_POST['producto_categoria_id']);
        $stock = $this->limpiarCadena($_POST['producto_stock']);
        $tallas = $this->limpiarCadena($_POST['producto_tallas']);
        $colores = $this->limpiarCadena($_POST['producto_colores']);
        $destacado = isset($_POST['producto_destacado']) ? 1 : 0;
        $activo = isset($_POST['producto_activo']) ? 1 : 0;
   
        # Verificando campos obligatorios #
        if($nombre == "" || $precio == "" || $categoria_id == "" || $stock == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos que son obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando integridad de los datos #
        if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ.,\-\s]{3,200}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando precio #
        if(!is_numeric($precio) || $precio < 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El PRECIO debe ser un número válido",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando precio oferta #
        if($precio_oferta != "" && (!is_numeric($precio_oferta) || $precio_oferta < 0)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El PRECIO DE OFERTA debe ser un número válido",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando categoría #
        if($this->verificarDatos("[0-9]{1,10}", $categoria_id)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La CATEGORÍA no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando que la categoría existe #
        $check_categoria = $this->ejecutarConsulta("SELECT id FROM categorias WHERE id='$categoria_id'");
        if($check_categoria->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "La CATEGORÍA seleccionada no existe en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Verificando stock #
        if($this->verificarDatos("[0-9]{1,10}", $stock)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El STOCK no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
        }

        # Directorio de imágenes #
        $img_dir = "../app/views/img/productos/";

        # Verificar si se seleccionó una nueva imagen #
        if($_FILES['producto_imagen']['name'] != "" && $_FILES['producto_imagen']['size'] > 0) {

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
            if(mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/jpeg" && 
               mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/png" && 
               mime_content_type($_FILES['producto_imagen']['tmp_name']) != "image/webp") {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen que ha seleccionado es de un formato no permitido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando peso de imagen #
            if(($_FILES['producto_imagen']['size'] / 1024) > 5120) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen seleccionada supera el peso permitido (5MB)",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Eliminando imagen anterior #
            if(is_file($img_dir.$datos['imagen'])) {
                chmod($img_dir.$datos['imagen'], 0777);
                unlink($img_dir.$datos['imagen']);
            }

            # Extensión de imágenes #
            switch(mime_content_type($_FILES['producto_imagen']['tmp_name'])) {
                case 'image/jpeg':
                    $img_ext = ".jpg";
                    break;
                case 'image/png':
                    $img_ext = ".png";
                    break;
                case 'image/webp':
                    $img_ext = ".webp";
                    break;
            }

            # Nombre final de la imagen #
            $img_nombre = $this->renombrar_fotos($nombre);
            $imagen = $img_nombre . $img_ext;

            # Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES['producto_imagen']['tmp_name'], $img_dir . $imagen)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No podemos subir la imagen al sistema en este momento",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        } else {
            $imagen = $datos['imagen'];
        }

        $producto_datos_up = [
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
                "campo_nombre" => "precio",
                "campo_marcador" => ":Precio",
                "campo_valor" => $precio
            ],
            [
                "campo_nombre" => "precio_oferta",
                "campo_marcador" => ":PrecioOferta",
                "campo_valor" => $precio_oferta
            ],
            [
                "campo_nombre" => "categoria_id",
                "campo_marcador" => ":CategoriaId",
                "campo_valor" => $categoria_id
            ],
            [
                "campo_nombre" => "imagen",
                "campo_marcador" => ":Imagen",
                "campo_valor" => $imagen
            ],
            [
                "campo_nombre" => "stock",
                "campo_marcador" => ":Stock",
                "campo_valor" => $stock
            ],
            [
                "campo_nombre" => "tallas",
                "campo_marcador" => ":Tallas",
                "campo_valor" => $tallas
            ],
            [
                "campo_nombre" => "colores",
                "campo_marcador" => ":Colores",
                "campo_valor" => $colores
            ],
            [
                "campo_nombre" => "destacado",
                "campo_marcador" => ":Destacado",
                "campo_valor" => $destacado
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

        if($this->actualizarDatos("productos", $producto_datos_up, $condicion)) {

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Producto actualizado",
                "texto" => "Los datos del producto ".$datos['nombre']." se actualizaron correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido actualizar los datos del producto ".$datos['nombre'].", por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }

}
?>