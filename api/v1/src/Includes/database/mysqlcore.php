<?php 

namespace HappySoftware\Database;

use \HappySoftware\Entity\Schema;

/*
*************************************************************************
=========================================================================
						Clase de base de datos
=========================================================================

	- Autor: Oscar Rodriguez (oscar@happysoftware.es)
	- Version: 1.0.
	- Metodos:
		
		+ public function conectarBBDD()
		+ public function comprobarConexionBBDD()
		+ public function liberarConexionBBDD()
		+ public function estaConectado()
		+ public function cambiarConfiguracion($serverURLNew, $serverPortNew, $userNew, $passwordNew)
		+ public function ejecutarSQL($sentenciaSQL)
		+ public function sqlAddLeftJoin($tabla, $campoCondicionLeftJoin, $campoCondicionTabla, $sinonimo, $sinonimoLeftJoin)
		+ public function sqlSelectLeftJoin($tabla, $campos, $condicion = null, $orden = null, $tipoOrden = null, $limit = 10, $valueLeftJoin = null)
		+ public function sqlSelect($tabla, $campos, $condicion = null, $orden = null, $tipoOrden = null, $limit = 10, $sqlLeftJoin = false, $tablaLeftJoin = null, $condicionLeftJoin = null)
		+ public function insert($tabla, $campos, $value)
		+ public function delete($tabla, $id, $historico = false, $field_id_optional = null)
		+ public function getValue($tabla, $campo, $valorCampoCondicion = "", $campoCondicion = null)
		+ public function truncateTable($tabla)
		+ public function update($tabla, $id, $camposUpdate, $campoCondicionUpdate = null, $valorCampoCondicionUpdate = null)
		+ public function selectCount($tabla, $campocondicion = null, $tipocondicion = null, $valorcondicion = null)
		+ public function selectSUM($tabla, $campo,$valorcampo = "", $campoCondicion = null)
		+ public function getLastID($tabla)
		+ public function estadoHistorico($tabla, $id)
		+ public function ExisteRegistro($tabla, $condicion)
		+ 
		
=========================================================================
*************************************************************************
*/

class DatabaseCore {
	
	public $objetoBBDD;
	public $bResultado;
	public $conectado;
	public $tabla;

//***************************	
//  PROPIEDADES DE CONEXION	
//***************************

	public $usuario;
	public $password;
	public $urlServidor;
	public $esquemaBBDD;
	public $puertoMySQL;
	public $cErrores;
	public $enlaceBBDD;
	public $filaDatos;
	public $defTablaBBDD;
	public $mysqli;
	public $debug;
	public $codificacion = 'utf8mb4';

	public $queryBuilded;

	// Propiedad que se utiliza para saber si se deben crear las tablas en base a la entidad no encontrada en el schema
	private $createMissingTables;

	public static function test()
	{
		die('Class working as expect. HappySoftware :-)');
	}

	public function __construct()
	{
		global $database;

		require_once('config.php');
		//include_once('../../Entity/SchemaEntity.php');

		$this->debug = false;
		
		$this->conectado = false;
		
		$this->usuario = $database['user'];
		$this->urlServidor = $database['host'];
		$this->puertoMySQL = $database['port'];
		$this->usuario = $database['user'];
		$this->password = $database['password'];
		$this->esquemaBBDD = $database['schema'];
		$this->createMissingTables = $database['config']['createmissingtables'];

		if($this->comprobarConexionBBDD()){
			if($this->conectarBBDD()){
				$this->conectado = true;
			}else{
				$this->conectado = false;
				throw new \Exception("Error de conexión a la base de datos");
			}
			
		}else{
			throw new \Exception("Error de conexión a la base de datos");
		}

	}
	
// 	Método que conecta con la base de datos.
	public function conectarBBDD()
	{
		
		$conexionCorrecta = false;
		
		$this->enlaceBBDD = new \mysqli($this->urlServidor, $this->usuario,$this->password, $this->esquemaBBDD);
		
		if (!$this->enlaceBBDD)
		{
		  //$this->cErrores->registrarError('Error de base de datos: ' . mysql_error(), 'MySqlCore.comprobarConexionBBDD()');
		  $conexionCorrecta = false;
		}else{
			$conexionCorrecta = true;
			$this->enlaceBBDD->query("SET NAMES '" . $this->codificacion . "'");
		}
		
		return $conexionCorrecta;
		
	}
	
	/** Funcion que comprueba que se pueda conectar al servidor de base de datos. 
	 * 
	 * */ 	
	public function comprobarConexionBBDD()
	{
		
		$conexionCorrecta = false;
		
		$this->enlaceBBDD = new \mysqli($this->urlServidor, $this->usuario,$this->password, $this->esquemaBBDD);
		
		if (!$this->enlaceBBDD)
		{
		  //$this->cErrores->registrarError('Error de base de datos: ' . mysql_error(), 'MySqlCore.comprobarConexionBBDD()');
		  $conexionCorrecta = false;
		}else{
			$conexionCorrecta = true;
		}
		$this->liberarConexionBBDD();
		return $conexionCorrecta;
		
	}
	
//	Funcion que libera la conexion a base de datos.	
	public function liberarConexionBBDD()
	{
		mysqli_close($this->enlaceBBDD);
	}

//	Metodo que comprueba si la conexion esta activa.	
	public function estaConectado()
	{
		return $this->conectado;
	}
	
//	Metodo que cambia la configuracion de la base de datos.	
	public function cambiarConfiguracion($serverURLNew, $serverPortNew, $userNew, $passwordNew)
	{
		
		$this->serverURL = $serverURLNew;
		$this->serverPort = $serverPortNew;
		$this->user = $userNew;
		$this->password = $passwordNew;
		
	}

	/**
	 * Añade el límite para la consulta en base al número de página y número de registros que se desea recuperar
	 * @param int $page Número de página de resultados
	 * @param int $limit Número de registros a recuperar
	 */
	public function addPagination($page, $limit)
	{
		$limit = " LIMIT " . ($page - 1) * $limit . ", " . ($limit * $page);
		return $limit;
	}

	public function setOrderBy()
	{

	}

	/** Recupera la información de una tabla devolviendo la información de la columna, valor por defecto, tipo de dato, longitud máxima, tipo real, key y extra 
	 * @param string $tableName. Nombre de la tabla para la cuál se va a recuperar el schema
	 * @return Schema Información de la tabla
	*/
	public function getSchemaInfo($tableName)
	{

        $schema['definitions'] = [];//new \HappySoftware\Entity\Schema();
        $schema['primarykey'] = null;
		//$schema['schema']['table'] = $tableName;

        $schemaTable = "
            select 
                column_name columna, 
                column_default valordefecto, 
                data_type tipodato, 
                character_maximum_length longitudmaxima, 
                column_type, 
                column_key, 
                extra
            from 
                INFORMATION_SCHEMA.COLUMNS 
            where 
                table_schema = '" . $this->esquemaBBDD. "' 
                and table_name = '". $tableName . "'";

		$schemaResults = $this->ejecutarSQL($schemaTable);


		if(mysqli_num_rows($schemaResults) == 0)
		{
			$this->liberarConexionBBDD();
			return null;
		}else{

            $schema = [];

            while($schemaRow = mysqli_fetch_assoc($schemaResults))
            {

				$schemaDescription = new \HappySoftware\Entity\Schema();

                //$schemaDescription->fieldName = $schemaRow['columna'];
                $schemaDescription->fieldType = $schemaRow['tipodato'];
                $schemaDescription->maxLength = $schemaRow['longitudmaxima'];
                $schemaDescription->extraLengthDataTypeInfo = $schemaRow['column_type'];
				//$schemaDescription->table = $tableName;

				// Primary Key
                if($schemaRow['column_key'] == 'PRI')
                {
                    $schemaDescription->primarykey = true;
					$schema['primarykey'] = $schemaDescription->fieldName;
                }

				$schema['definitions'][$schemaRow['columna']] = $schemaDescription;
				
            }

			return $schema;

		}

	}

	/**
	 * Ejecuta una consulta sobre la base de datos y devuelve el cursor con la información
	 * @param string $sentenciaSQL. SQL a ejecutar
	 * @param boolean $debug. (Opcional) Por defecto es false. True pinta la consulta y no devuelve datos
	 */
	public function queryRaw($sentenciaSQL, $debug = false)
	{
		
		//	Comprobamos que haya conexion a la base de datos, si no devolvemos false
			if(!$this->estaConectado()){
				if(!$this->conectarBBDD()){
					return false;
				}
			}	

			$datos = mysqli_query($this->enlaceBBDD, $sentenciaSQL  );
			if($debug == true)
			{
				echo($sentenciaSQL) . '<br>';
			}
			
			return $datos;
		
	}

//	Metodo que ejecuta una sentencia SQL y devuelve un cursor con los datos.	
	public function ejecutarSQL($sentenciaSQL, $debug = false)
	{
		
		//	Comprobamos que haya conexion a la base de datos, si no devolvemos false
			if(!$this->estaConectado()){
				if(!$this->conectarBBDD()){
					return false;
				}
			}	

		//$datos = mysqli_query($this->enlaceBBDD, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $sentenciaSQL  );
			$datos = mysqli_query($this->enlaceBBDD, $sentenciaSQL  );
			if($debug == true)
			{
				echo($sentenciaSQL) . '<br>';
			}
			
			return $datos;
		
	}

//	TODO: Metodo que agrega un left join a la consulta con la tabla y condicion pasada	
	public function sqlAddLeftJoin($tabla, $campoCondicionLeftJoin, $campoCondicionTabla, $sinonimo, $sinonimoLeftJoin)
	{
		
		$value = "LEFT JOIN " . $tabla . ' ' . $sinonimoLeftJoin . ' ON ' . $sinonimoLeftJoin . "." . $campoCondicionLeftJoin . '=' . $sinonimo . "." . $campoCondicionTabla . ' ' ;
		return $value;
		
	}

//	Metodo que ejecuta una sentencia SQL con left Join
	public function sqlSelectLeftJoin($tabla, $campos, $condicion = null, $orden = null, $tipoOrden = null, $limit = 10, $valueLeftJoin = null)
	{
		// 	Comprobamos que haya conexion a la base de datos, si no devolvemos false
			if(!$this->estaConectado()){
				if(!$this->conectarBBDD()){
					return false;
				}
			}
				$constructorSQL = "SELECT " . $campos . ' ' ;
			
		// 		Asignamos la tabla de la que vamos a recoger los resultados
				$constructorSQL .= " FROM " . $this->esquemaBBDD . '.' . $tabla;
				
		//		Comprobamos si tiene Left Join en la consulta
				if($valueLeftJoin == null){
					return false;
				}
				
				$constructorSQL .= " " . $valueLeftJoin;
				
		// 		Establecemos la condici�n de la sentencia
				if(!$condicion==null){
					$constructorSQL .= " WHERE " . $condicion;
				}
				
				// Establecemos el orden
				if(!$orden == null){
					$constructorSQL .= ' ORDER BY ' . $orden;
					if($tipoOrden != null){
						$constructorSQL .= ' ' . $tipoOrden;
					}else{
						$constructorSQL .= ' ASC ';
					}
				}
				
				$datos = mysqli_query( $this->enlaceBBDD, $constructorSQL  );

				return $datos;		
	}
	
//	Método que ejecuta una sentencia SQL y devuelve un cursor con datos.	

	public function sqlSelect($tabla, $campos, $condicion = null, $orden = null, $tipoOrden = null, $limit = 10, $sqlLeftJoin = false, $tablaLeftJoin = null, $condicionLeftJoin = null)
	{
		// 		Comprobamos que haya conexión a la base de datos, si no devolvemos false
		if(!$this->estaConectado()){
			if(!$this->conectarBBDD()){
				return false;
			}
		}
		//mysqli_query($this->enlaceBBDD, "set names 'utf8'");
		$this->enlaceBBDD->set_charset("utf8");
			$constructorSQL = "SELECT " . $campos . ' ' ;
		
		// 	Asignamos la tabla de la que vamos a recoger los resultados
			$constructorSQL .= " FROM " . $this->esquemaBBDD . '.' . $tabla;
			
		//	Comprobamos si tiene Left Join en la consulta
			if($sqlLeftJoin && $tablaLeftJoin != null && $condicionLeftJoin != null){
				
				// $constructorSQL .= $this->sqlAddLeftJoin($tablaLeftJoin, $condicionLeftJoin);
				
			}
			
			// Establecemos la condici�n de la sentencia
			if(!$condicion==null){
				$constructorSQL .= " WHERE " . $condicion;
			}
			
			// Establecemos el orden
			if(!$orden == null){
				$constructorSQL .= ' ORDER BY ' . $orden;
				if($tipoOrden != null){
					$constructorSQL .= ' ' . $tipoOrden;
				}else{
					$constructorSQL .= ' ASC ';
				}
			}
			
			if($this->debug)
			{
				die($constructorSQL);
			}
			
			$datos = mysqli_query($this->enlaceBBDD, $constructorSQL  );

			return $datos;
		
	}
	
//	Método que lanza un insert sobre la base de datos.	
	public function insert($tabla, $campos, $value, $debug = false)
	{
		if(!$this->estaConectado()){
		//			Probamos a conectar a la base de datos			
			if(!$this->conectarBBDD()){
				return false;
			}
		}
		try{
			
		//	TODO: Antes de nada hay que validar que el registro no exista ya en base de datos
			$constructorSQL = "INSERT INTO " . $this->esquemaBBDD . '.' . $tabla . " (";
			
			$constructorSQL .= $campos;
					
			$constructorSQL .= ') VALUES (' . $value . ')';
		//	die($constructorSQL);
		
			if($debug)
			{
				echo 'error:' . $constructorSQL . '<br>';
				return false;
			}

			$this->enlaceBBDD->query("SET NAMES '" . $this->codificacion . "'");

			if($this->enlaceBBDD->query($constructorSQL)){
				return true;
			} else {
				return false;
			}
			
		}catch(\Exception $ex){
			return $ex->getMessage();
		}

	}

	/**
	 * Metodo que elimina un registro de base de datos o bien lo deja en historico.
	 * @param string $tabla Nombre de la tabla sobre la que se va a eliminar el registro
	 * @param int $id ID -> Primary Key de la tabla
	 * @param string $tipoBorrado. Indica si es eliminación (F)Física (L) lógica 
	 * @param boolean $historico True: Se marca como estado (H)istórico
	 * @param string $fieldNamePrimaryKey (Opcional). Por defecto es null. Nombre de la columna que se va a utilizar para eliminar el registro
	 * @return boolean. True: Operación correcta | False: Error en la operación
	 */	
	//	Devuelve boolean. True: Eliminación correcta | False: No se ha eliminado
	public function delete($tabla, $id, $tipoBorrado = DELETE_FISICO, $historico = false, $fieldNamePrimaryKey = null)
	{
		
		if($historico == true)
		{
			//return $this->update($tabla, $id, ' estado = 0 ');
			return $this->update($tabla, $id, "estado='" . ESTADO_HISTORIAL . "'");
		}else
		{

			$queryDelete = "DELETE FROM " . $tabla . " WHERE ";
			
			if($fieldNamePrimaryKey != null)
			{
				$queryDelete .= $fieldNamePrimaryKey . " = " . $id;
			
			}else{
				$queryDelete .= " id=" . $id;
			}

			if($this->enlaceBBDD->query($queryDelete)){
				return true;
			}else{
				return false;
			}
		}
		
	}

//	Metodo que elimina un registro de base de datos o bien lo deja en historico.
//	Devuelve boolean. True: Eliminación correcta | False: No se ha eliminado
	public function deleteSingle($tabla, $id, $historico = false, $field_id_optional = null, $debug = false)
	{
		
		if($historico == true)
		{
			return $this->update($tabla, $id, "estado='" . ESTADO_HISTORIAL . "'");
		}else
		{

			$queryDelete = "DELETE FROM " . $tabla . " WHERE ";
			
			if($field_id_optional != null)
			{
				$queryDelete .= $field_id_optional . " = " . $id;
			
			}else{
				$queryDelete .= " id=" . $id;
			}

			if($this->enlaceBBDD->query($queryDelete)){
				return true;
			}else{
				return false;
			}

		}
		
	}

	/** Metodo que devuelve el valor de un campo dado para una tabla
	 * @param String $tabla Tabla de la que se va a recoger el valor
	 * @param String $campo Campo que se desea consultar de la tabla
	 * @param String $valorCampoCondicion Valor que se va a utilizar para el =
	 * @param String $campoCondicion Campo que se va a comprobar para la sentencia
	 */
	public function getValue($tabla, $campo, $valorCampoCondicion = "", $campoCondicion = null)
	{
		// 		Comprobamos que haya conexion a la base de datos, si no devolvemos false
		if(!$this->estaConectado()){
			if(!$this->conectarBBDD()){
				return false;
			}
		}

		$sqlValue = "SELECT " . $campo . " FROM " . $tabla . " WHERE " ;
		
		if($campoCondicion != null){
			$sqlValue .= $campoCondicion . " = " . $valorCampoCondicion;
		}else
		{
			$sqlValue .= $campo . " = " . $valorCampoCondicion;
		}
		// echo $sqlValue;	
		$datos = $this->enlaceBBDD->query($sqlValue);

		if($datos){
			
			$valor = mysqli_fetch_assoc($datos);
			if(trim($valor[$campo]) == '')
			{
				return -1;
			} else {
				return $valor[$campo];
			}
		}else{

			return -1;//null;
		}
		
	}
	
//	Metodo que vacia todos los datos de una base de datos.	
	public function truncateTable($tabla)
	{
		if(!$this->estaConectado()){
		//			Probamos a conectar a la base de datos			
			if(!$this->conectarBBDD()){
				return false;
			}
		}
		$truncateSQL = "TRUNCATE " . $this->esquemaBBDD . '.' . $tabla;
		if($this->enlaceBBDD->query($truncateSQL)){
			echo ' Tabla truncada';
			return true;
		} else {
			echo ' Tabla no truncada ';
			return false;
		}
	}
	
	/**
	 * 
	 */
	public function updateFromObject($nombreEntidad, $datosActual, $datosUpdate)
	{
		//	Comprobamos el estado de la conexión a la base de datos

		//	Construimos el update en base a las propiedades de la entidad
		//	Para ello, recuperamos el schema de la entidad para poder ir mapeando los datos nuevos con los antiguos

		//	Lanzamos el update sobre la base de datos y devolvemos el estado de la actualización

		//
	}

//	Metodo que lanza un update sobre la base de datos.	
	public function update($tabla, $idregistro, $camposUpdate, $campoCondicionUpdate = null, $valorCampoCondicionUpdate = null)
	{
		// 	Comprobamos que haya conexión a la base de datos, si no devolvemos false
				if(!$this->estaConectado()){
					if(!$this->conectarBBDD()){
						return false;
					}
				}
				
				$sql = "UPDATE " . $tabla . " SET ";
				$sql .= $camposUpdate;
				
				if($campoCondicionUpdate == null)
				{

					$sql .= " WHERE id=" . $idregistro;

				}else{
					$sql .= " WHERE " . $campoCondicionUpdate . " = " . $valorCampoCondicionUpdate;
				}
				//echo 'error: <br>' . $sql;	
				//die($sql);

				$this->enlaceBBDD->query("SET NAMES '" . $this->codificacion . "'");
				if($this->enlaceBBDD->query($sql))
				{
					return true;
				} else {
					return false;
				}
	}

//	Método que obtiene el número de registros de la tabla pasada por parámetro.	
	public function selectCount($tabla, $campocondicion = null, $tipocondicion = null, $valorcondicion = null)
	{
		
		$sql = "SELECT count(*) as TOTAL FROM " . $tabla;
		$total = 0;

		if($campocondicion !== null && $tipocondicion !== null && $valorcondicion !== null)
		{
			$sql .= " WHERE " . $campocondicion . " ". $tipocondicion . " " . $valorcondicion;
		}

		if($result = $this->enlaceBBDD->query($sql)){
			
			$row = mysqli_fetch_assoc($result);
			$total = $row['TOTAL'];
			
		}
		//	echo 'error: ' . $sql;
		return $total;
		
	}

//=======================================================	
// 	Funcion que comprueba si existe un registro en bbdd
//=======================================================
	public function ExisteRegistro($tabla, $condicion)
	{
		
		$sql = "SELECT COUNT(*) as TOTAL FROM " . $tabla;
		$total = 0;
		
		$sql .= " WHERE " . $condicion;
		
		if($result = $this->enlaceBBDD->query($sql)){
			
			$row = mysqli_fetch_assoc($result);
			$total = $row['TOTAL'];
			
		}
		// echo 'query: ' . $sql;
		//echo $sql . ' Total: ' . $total;
		if($total  == 0)
		{
			return false;
		} else {
			return true;
		}
		
	}
	
//	Método que devuelve un campo sumado	
	public function selectSUM($tabla, $campoCalculo, $fieldConditionName = null, $conditionOperator = null,  $fieldConditionValue = null)
	{

		$totalSUM = 0;

		$sql = "SELECT SUM(" . $campoCalculo . ") as total FROM " . $tabla . " ";
		
		if(!is_null($fieldConditionName) && !is_null($conditionOperator) && !is_null($fieldConditionValue))
		{
			$sql .= " WHERE " . $fieldConditionName . $conditionOperator . $fieldConditionValue;
		}

		if($result = $this->enlaceBBDD->query($sql))
		{
			$row = mysqli_fetch_assoc($result);
			$totalSUM = $row['total'];
		}
		
		return $totalSUM;

	}
	
//	Metodo que obtiene el ultimo ID de una tabla para el valor auto_increment de dicha tabla	
	public function getLastID($tabla)
	{
		// 		Comprobamos que haya conexi�n a la base de datos, si no devolvemos false
		if(!$this->estaConectado()){
			if(!$this->conectarBBDD()){
				return false;
			}
		}		
		$sql = "SHOW TABLE STATUS FROM " . $this->esquemaBBDD . " LIKE '" . $tabla . "'";
// die($sql);
		$datos = $this->enlaceBBDD->query($sql);

		if($datos){
			$valor = mysqli_fetch_assoc($datos);
			return $valor['Auto_increment'];
		}else{
			return 1;
		}		
	}
	
	public function estadoHistorico($tabla, $id)
	{
		$estadoHistorico = false;
		if(!$this->estaConectado()){
			if(!$this->conectarBBDD()){
				return false;
			}
		}		
		$sql = "SELECT estado FROM " . $tabla . " WHERE ID=" . $id;
		
		$datos = $this->enlaceBBDD->query($sql);

		if($datos){
			$valor = mysqli_fetch_assoc($datos);
			if($valor['estado'] == '0'){
				return true; // Esta en el historico
			}else{
				return false; // No esta en el historico
			}
		}else{
			return 1;//null;
		}			
	}
	
	public static function PrepareDBString($value)
	{

		$saneado = str_replace("<script>", "", $value);
		$saneado = str_replace("</script>", "", $saneado);
		$saneado = str_replace("<?php",'', $saneado);
		$saneado = str_replace("?>", "", $saneado);
		$saneado = str_replace("/*", "", $saneado);
		$saneado = str_replace("*/", "", $saneado);
		$saneado = str_replace("//", "/", $saneado);
		$saneado = str_replace("'",  "`", $saneado);
		$saneado = str_replace("alert('",  "", $saneado);
		$saneado = str_replace("javascript:","", $saneado);
		$saneado = str_replace("drop database", "", $saneado);
		$saneado = str_replace("drop table", "", $saneado);
		$saneado = str_replace("base64", "", $saneado);
		$saneado = str_replace("eval('", "", $saneado);
		$saneado = str_replace("''","``", $saneado);		
				
		return $saneado;

	}
	
    public static function ConvertStringToDoubleMySQL($value)
    {
        return str_replace(",", ".", $value);
    }

	public function ArregloFecha($value)
	{
			// 	Hay que formatear la fecha de dd/mm/aaaa a aaaa-mm-dd
				$value = str_replace("/", "-", $value);
				$arrayFecha = explode("-",$value);
					$valorOut = $arrayFecha[2] . '/' . $arrayFecha[1] . '/' . $arrayFecha[0];
				return $valorOut;

				if($value != '')
				{
					// 	Hay que formatear la fecha de dd/mm/aaaa a aaaa-mm-dd
						$arrayFecha = explode("-",$value);
						if(count($arrayFecha) >= 2)
						{
							$valorOut = $arrayFecha[2] . '/' . $arrayFecha[1] . '/' . $arrayFecha[0];
							return $valorOut;
						} else {
							return '';
						}
				} else {
					return '';
				}		
	}
	
	public function getCreateMissingTables()
	{
		return $this->createMissingTables;
	}

}

?>