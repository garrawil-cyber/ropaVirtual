<?php
	
	namespace app\models;
	use \PDO;
	$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/ropaVirtual_mvc';

	require_once $rootPath ."/config/database.php";

	class mainModel{

		private $server=DB_HOST;
		private $db=DB_NAME;
		private $user=DB_USER;
		private $pass=DB_PASS;


		/*----------  Funcion conectar a BD  ----------*/
		protected function conectar(){
			$conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db,$this->user,$this->pass);
			$conexion->exec("SET CHARACTER SET utf8");
			return $conexion;
		}


		/*----------  Funcion ejecutar consultas  ----------*/
		protected function ejecutarConsulta($consulta){
			$sql=$this->conectar()->prepare($consulta);
			$sql->execute();
			return $sql;
		}


		/*----------  Funcion limpiar cadenas  ----------*/
		public function limpiarCadena($cadena){

			$palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

			$cadena=trim($cadena);
			$cadena=stripslashes($cadena);

			foreach($palabras as $palabra){
				$cadena=str_ireplace($palabra, "", $cadena);
			}

			$cadena=trim($cadena);
			$cadena=stripslashes($cadena);

			return $cadena;
		}


		/*---------- Funcion verificar datos (expresion regular) ----------*/
		protected function verificarDatos($filtro,$cadena){
			if(preg_match("/^".$filtro."$/", $cadena)){
				return false;
            }else{
                return true;
            }
		}


		/*----------  Funcion para ejecutar una consulta INSERT preparada  ----------*/
		protected function guardarDatos($tabla,$datos){

			$query="INSERT INTO $tabla (";

			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_nombre"];
				$C++;
			}
			
			$query.=") VALUES(";

			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_marcador"];
				$C++;
			}

			$query.=")";
			$sql=$this->conectar()->prepare($query);

			foreach ($datos as $clave){
				$sql->bindParam($clave["campo_marcador"],$clave["campo_valor"]);
			}

			$sql->execute();

			return $sql;
		}


		/*---------- Funcion seleccionar datos ----------*/
        public function seleccionarDatos($tipo,$tabla,$campo,$id){
			$tipo=$this->limpiarCadena($tipo);
			$tabla=$this->limpiarCadena($tabla);
			$campo=$this->limpiarCadena($campo);
			$id=$this->limpiarCadena($id);

            if($tipo=="Unico"){
                $sql=$this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo=:ID");
                $sql->bindParam(":ID",$id);
            }elseif($tipo=="Normal"){
                $sql=$this->conectar()->prepare("SELECT $campo FROM $tabla");
            }
            $sql->execute();

            return $sql;
		}


		/*----------  Funcion para ejecutar una consulta UPDATE preparada  ----------*/
		protected function actualizarDatos($tabla,$datos,$condicion){
			
			$query="UPDATE $tabla SET ";

			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
				$C++;
			}

			$query.=" WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"];

			$sql=$this->conectar()->prepare($query);

			foreach ($datos as $clave){
				$sql->bindParam($clave["campo_marcador"],$clave["campo_valor"]);
			}

			$sql->bindParam($condicion["condicion_marcador"],$condicion["condicion_valor"]);

			$sql->execute();

			return $sql;
		}


		/*---------- Funcion eliminar registro ----------*/
        protected function eliminarRegistro($tabla,$campo,$id){
            $sql=$this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
            $sql->bindParam(":id",$id);
            $sql->execute();
            
            return $sql;
        }


		/*---------- Paginador de tablas ----------*/
		protected function paginadorTablas($pagina,$numeroPaginas,$url,$botones){
	        $tabla='<nav aria-label="Navegación de páginas"><ul class="pagination justify-content-center">';

	        if($pagina<=1){
	            $tabla.='<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
	        }else{
	            $tabla.='
	            <li class="page-item"><a class="page-link" href="'.$url.($pagina-1).'/">Anterior</a></li>
	            <li class="page-item"><a class="page-link" href="'.$url.'1/">1</a></li>
	            ';
	            if($pagina>3){
	                $tabla.='<li class="page-item disabled"><span class="page-link">...</span></li>';
	            }
	        }


	        $ci=0;
	        for($i=$pagina; $i<=$numeroPaginas; $i++){

	            if($ci>=$botones){
	                break;
	            }

	            if($pagina==$i){
	                $tabla.='<li class="page-item active"><a class="page-link" href="'.$url.$i.'/">'.$i.'</a></li>';
	            }else{
	                $tabla.='<li class="page-item"><a class="page-link" href="'.$url.$i.'/">'.$i.'</a></li>';
	            }

	            $ci++;
	        }


	        if($pagina==$numeroPaginas){
	            $tabla.='<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
	        }else{
	            if($pagina<($numeroPaginas-2)){
	                $tabla.='<li class="page-item disabled"><span class="page-link">...</span></li>';
	            }
	            $tabla.='
	                <li class="page-item"><a class="page-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li>
	                <li class="page-item"><a class="page-link" href="'.$url.($pagina+1).'/">Siguiente</a></li>
	            ';
	        }

	        $tabla.='</ul></nav>';
	        return $tabla;
	    }

	function renombrar_fotos($nombre) {
    $nombre = str_replace(" ", "_", $nombre);
    $nombre = str_replace(["á","é","í","ó","ú"], ["a","e","i","o","u"], $nombre);
    $nombre = str_replace(["Á","É","Í","Ó","Ú"], ["A","E","I","O","U"], $nombre);
    $codigo = rand(0, 99);
    return $nombre . "_" . $codigo;
}
	    
	}