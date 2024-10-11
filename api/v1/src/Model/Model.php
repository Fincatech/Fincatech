<?php

namespace HappySoftware\Model;

use Fincatech\Controller\FrontController;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits;
use HappySoftware\Controller\Traits\ConfigTrait;
use HappySoftware\Controller\Traits\FilesTrait;
use HappySoftware\Controller\Traits\SecurityTrait;

use HappySoftware\Database\DatabaseCore;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Model\Traits\DatabaseTrait;
use HappySoftware\Model\Traits\CrudTrait;
use HappySoftware\Model\Traits\EntityTrait;
use HappySoftware\Model\Traits\SchemaTrait;
use HappySoftware\Model\Traits\UtilsTrait;
use HappySoftware\Model\Traits\TableTrait;

use Fincatech\Model\UsuariosModel;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Model extends FrontController{
    
    use ConfigTrait;
    use CrudTrait, DatabaseTrait, EntityTrait, SchemaTrait, TableTrait, UtilsTrait;
    use SecurityTrait, FilesTrait;

    //  Operadores de la búsqueda
    /**
     * Búsqueda coincidente
     */
    const _OPERATOR_LIKE_ = 'like';
    /**
     * Búsqueda Igual
     */
    const _OPERATOR_EQUALS_ = 'eq';
    /**
     * Búsqueda mayor o igual que
     */
    const _OPERATOR_GREATERTHAN_ = 'gt';
    /**
     * Búsqueda menor o igual que
     */
    const _OPERATOR_LOWERTHAN_ = 'lt';
    /**
     * Búsqueda menor
     */
    const _OPERATOR_LOWER_ = 'g';
    /**
     * Búsqueda mayor
     */
    const _OPERATOR_GREATER_ = 'l';

    /** Namespace del proyecto enlazado */
    //private $nameSpace;

    /** Entidad del modelo */
    private $mainEntity;

    private $entities;

    //  Contiene los datos de la entidad en array
    private $entityData;

    //  Relaciones de la entidad. Array asociativo
    private $entityRelations;

    /**
     * @var boolean
     * Indica si la entidad tiene relaciones. Por defecto es false
     */
    private $haveEntityRelations = false;

    /**
     * @var Array
     * Schema de la entidad y sus relaciones
     */
    private $entitySchema;

    /**
     * @var string
     * Query que se va a ejecutar sobre el repositorio
     */
    public $queryToExecute;

    /** Entity Helper */
    protected $entityHelper;

    /** Repositorio conectado al MySQLCore 
     * @var DatabaseCore
    */
    private $repositorio;

    //  Campos para la búsqueda
    private $searchFields = [];
    public function SearchFields(){
        return $this->searchFields;
    }

    /**
     * @var boolean
     */
    private $pagination = false;
    private $pageResults;
    private $pageResultsLimit;

    public $usuarios;
 
    public function Test()
    {
        echo 'TestModel',PHP_EOL;
    }

    public function __construct()
    {

        global $databaseCore;
        
        if(class_exists('HappySoftware\Database\DatabaseCore') && empty($databaseCore)){
            $databaseCore = new \HappySoftware\Database\DatabaseCore();
        }    

        if(class_exists('HappySoftware\Entity\EntityHelper') && empty($this->entityHelper)){
            $this->entityHelper = new \HappySoftware\Entity\EntityHelper();
        }

        //  Inicializamos los posibles campos de búsqueda para el modelo
        $this->searchFields = [];
        $this->searchFields['searchfields'] = [];

        $this->repositorio = $databaseCore; 

        $this->InitModel();

    }
    
    public function getRepositorio()
    {
        return $this->repositorio;
    }

    /**
     * Inicializa el modelo
     */
    public function InitModel($entity = null, $params = null, $tablasSchema = null)
    {

        global $database;
        global $databaseCore;
        //  Inicializamos el core de la base de datos
        $this->repositorio = $databaseCore; 
        // $this->repositorio = new \HappySoftware\Database\DatabaseCore(); 

        $this->mainEntity = $entity;

        //  Establecemos los parámetros para la consulta
        if(!is_null($params))
        {
            //  TODO: Hay que validar que existan las propiedades para evitar excepciones no controladas
            //          Si no se han pasado, se debe coger de la configuración de la aplicación
            // Paginación
            if(isset($params['page']))
                $this->setPagination($params['page'], (isset($params['resultslimit']) ? $params['resultslimit']: $database['config']['pageresults']));
            
            //  Filtros de búsqueda

            //  Orden de la consulta

        }

        //  ¿Para qué se utiliza??¿?¿?¿
        $this->entities = $tablasSchema;

    }

    /** FIX: Rellena el modelo
     * @param string $modelName Nombre del modelo a rellenar
     * @param array $data. Datos que se van a procesar recuperados desde la entidad de bbdd
     */
    public function Fill($modelName, $entity = null)
    {
        
        $data = $this->entityData[ucfirst($modelName)];
        $modelInstanceName = ucfirst($modelName) . 'Model';

        if(is_array($data) && class_exists($modelInstanceName))
        {   

            $propiedadesModelo = get_class_vars( $modelInstanceName );
            for($iFill = 0; $iFill < count($data[0]); $iFill++)
            {
                $fieldName = $propiedadesModelo[$iFill];
                $setMethod = 'set'.strtoupper($fieldName);

                $fieldValue = $fieldName;
                //  Validamos que exista el campo en el data a procesar
                if(isset($data[$fieldName]))
                {
                    $this->$modelName->$setMethod( $fieldValue );
                }
                
            }

        }

    }

    public function setMainModel($name)
    {
        $modelName = __NAMESPACE__ . '\\'. $name;

        include_once(ABSPATH.'src/Model/'.$name.'.php');
        
        $this->$name = new $modelName();

    }

    public function setModel($value)
    {
        $this->setMainModel($value);
        return $this->$value;
    }

    //TODO
    public function setCriteria($criteria)
    {
        return $this;
    }

    /** Ejecuta una consulta sobre el repositorio previamente construida sin devolver resultado */
    public function queryRaw($sqlToExecute)
    {
        
        $resultData = $this->repositorio->queryRaw( $sqlToExecute );

        //  Validamos que haya datos
        if(!$resultData)
            return null;

    }

    /** Ejecuta una consulta sobre el repositorio previamente construida 
     * @param string $sqlToExecute. Query to execute
     * @param boolean $associateArrayToResults. (Optional) Defaults: True. Returns mysqliobject
     * @return Array|\mysqli_result Devuelve un array con los datos recuperados
    */
    public function query($sqlToExecute, $associateArrayToResults = true)
    {

        $resultData = $this->repositorio->queryRaw( $sqlToExecute );

        //  Validamos que haya datos
        //TOFIX: Controlar por si acaso en algún proceso da error ya que se ha modificado
        //  para que devuelva siempre un array aunque sea vacío
        // if(!$resultData)
        //     return null;

        if($associateArrayToResults){
            return $this->mapMysqliResultsToObject($resultData);
        }else{
            return $resultData;
        }
    }

    // TODO: Método de ejecución segura pasando por parámetro un array con los valores y campos

    /**
     * Ejecuta una consulta sobre el repositorio
     * @param boolean $relations Por defecto es true. Indica si se van a recuperar las entidades asociadas a la entidad principal
     */
    private function execute($relations = true, $includeSchema = false)
    {

            $resultData = $this->repositorio->queryRaw( $this->queryToExecute );

            $this->entityData[$this->mainEntity] = [];
            $this->entityData[$this->mainEntity] = $this->mapMysqliResultsToObject($resultData);

            //  Obtiene las relaciones de la entidad principal si las hay
            if($relations)
                $this->getDataRelations($includeSchema);

            if($includeSchema == true)
                $this->getSchemaEntity();


    }

    /** Devuelve el número total de registros de una entidad */
    private function executeCount($entity, $subquery = null)
    {
        //  Hay que comprobar si el count se hace sobre una query para poder delimitarla
        if(strpos($entity, 'select') !== false){
            $entity = '(' . $entity . ') as t1';
        }

        $query = 'select count(*) as total from ' . $entity;
        // $resultData = $this->repositorio->queryRaw ( "select count(*) as total from (" . $entity . ") tabla ");
        $resultData = $this->repositorio->queryRaw ( $query );
        $result = $this->mapMysqliResultsToObject($resultData);
        if(!$result)
        {
            return 0;
        }else{
            return $result[0]['total'];
        }
    }

    /**
     * Recupera un registro de la entidad actual por su id además de las relaciones establecidas para la entidad actual
     * @param int $id ID de la entidad que se va a recuperar
     * @return Array Array asociativo con los datos de la entidad en caso de tener datos
     */
    public function getById($id)
    {
        $this->queryToExecute = "select * from " . strtolower($this->mainEntity) . " where id = $id";

        $this->execute(true, true);
        return $this->entityData;
    }

    /**
     * Recupera una entidad y todas sus entidades relacionadas por el campo que se quiera consultar
     * @param string $field Campo que se va a consultar
     * @param string $value Valor con el que se va a recuperar la información
     */
    public function getByFields($queryFields, $entity)
    {

        $result = [];
        $values = '';

        $sql = "select " . $queryFields['getfields'] . " from $entity";

        if(isset($queryFields['fields']))
        {
            $sql .= " where ";
            foreach($queryFields['fields'] as $key => $campo)
            {
                $values .= $key . " " . $campo . " and ";
            }
            $sql .= substr($values, 0, strlen($values)-4);
        }

        $results = $this->query($sql);
        if(!is_null($results))
        {
            $result = $results;
        }

        return $result;
    }

    /**
     * Comprueba si existe un registro mediante el nombre del campo + su valor
     * @param string $fieldName. Nombre de la columna
     * @param string $fieldValue. Valor que se va a buscar
     * @return bool True: Existe, False: No existe
     */
    public function ExistsByFieldAndValue($fieldName, $fieldValue)
    {

        $sql = "select * from " . strtolower($this->mainEntity) . " where " . $fieldName . " = " . $fieldValue;
        $results = $this->query($sql);

        if(is_null($results)){
            return false;
        }

        return count($results) > 0;
    }

    public function getEntidad()
    {
        return $this->mainEntity;
    }
    
    public function setEntidad($nombre)
    {
        $this->mainEntity = $nombre;
        return $this;
    }

    /**
     * Agrega un campo al modelo de búsqueda antes de realizar la búsqueda
     * @param string $fieldName Nombre de la columna
     * @param string $searchOperator Tipo de búsqueda
     * @param string $fieldType Tipo de dato
     * @param string $fieldValue Valor que se va a buscar
     */
    public function AddFieldToSearch($fieldName, $searchOperator, $fieldType, $fieldValue)
    {
        //  Validamos que esté inicializada la propiedad de las columnas a incluir en la búsqueda
        if(!isset($this->searchFields['fields'])){
            $this->searchFields['fields'] = [];
        }

        $field = [];
        $field['field'] = $fieldName;
        $field['searchtype'] = $searchOperator;
        $field['search'] = $fieldValue;
        $field['type'] = $fieldType;

        //  Si no se ha agregado aún se añade, si no, no se hace nada
        $fieldNames = array_column($this->SearchFields()['fields'], 'field');
        if(!in_array($fieldName, $fieldNames)){
            $this->searchFields['fields'][] = $field;
        }else{
            $idx = array_search($fieldName, $fieldNames);
            //  Sobreescribimos el tipo y el valor
            $this->searchFields['fields'][$idx]['searchtype'] = $searchOperator;
            $this->searchFields['fields'][$idx]['search'] = $fieldValue;
            $this->searchFields['fields'][$idx]['type'] = $fieldType;
        }

        return $this;
    }

    /**
     * Recupera el último ID insertado para una entidad
     * @param string $entity. Nombre de la entidad que se va a consultar
     * @return int Último ID
     */
    public function GetNextId($entity)
    {
        return $this->repositorio->getLastID($entity);
    }

}