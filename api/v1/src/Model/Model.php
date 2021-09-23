<?php

namespace HappySoftware\Model;

use Fincatech\Controller\FrontController;
use HappySoftware\Controller\HelperController;
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
    
    use ConfigTrait, CrudTrait, DatabaseTrait, EntityTrait, SchemaTrait, TableTrait, UtilsTrait;
    use SecurityTrait, FilesTrait;

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
    private $queryToExecute;

    /** Entity Helper */
    private $entityHelper;

    /** Repositorio conectado al MySQLCore */
    private $repositorio;

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
        $this->entityHelper = new \HappySoftware\Entity\EntityHelper();
        
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

        //  Inicializamos el core de la base de datos
        $this->repositorio = new \HappySoftware\Database\DatabaseCore();
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

    /**
    * Ejecuta una consulta sobre el repositorio previamente construida
    */
    public function query($sqlToExecute)
    {
        $resultData = $this->repositorio->queryRaw( $sqlToExecute );

        //  Validamos que haya datos
        if(!$resultData)
            return null;

        return $this->mapMysqliResultsToObject($resultData);
    }

    // TODO: Método de ejecución segura pasando por parámetro un array con los valores y campos

    /**
     * Ejecuta una consulta sobre el repositorio
     * @param boolean $relations Por defecto es true. Indica si se van a recuperar las entidades asociadas a la entidad principal
     */
    private function execute($relations = true, $includeSchema = false)
    {
        try{

            $resultData = $this->repositorio->queryRaw( $this->queryToExecute );
            // error_log("Query: " . $this->queryToExecute,0);
// die($this->queryToExecute);
            $this->entityData[$this->mainEntity] = [];
        //   die('2');  
            $this->entityData[$this->mainEntity] = $this->mapMysqliResultsToObject($resultData);
            //  Obtiene las relaciones de la entidad principal si las hay
            if($relations)
                $this->getDataRelations($includeSchema);

            if($includeSchema == true)
                $this->getSchemaEntity();

        }catch(\Exception $ex)
        {
            die($ex->getMessage());
        }
    }

    /** Devuelve el número total de registros de una entidad */
    private function executeCount($entity)
    {
        $resultData = $this->repositorio->queryRaw ( "select count(*) as total from (" . $entity . ") tabla ");
        $result = $this->mapMysqliResultsToObject($resultData);
        return $result[0]['total'];
    }

    /**
     * Recupera un registro de la entidad actual por su id además de las relaciones establecidas para la entidad actual
     */
    public function getById($id)
    {
        $this->queryToExecute = "select * from $this->mainEntity where id = $id";
        $this->execute(true, true);
        return $this->entityData;
    }

    /**
     * Recupera una entidad y todas sus entidades relacionadas por el campo que se quiera consultar
     * @param string $field Campo que se va a consultar
     * @param string $value Valor con el que se va a recuperar la información
     */
    public function getOneByField($field, $value)
    {
        //  Aquí comprobamos la definición de la entidad para ver el campo y el tipo que tiene definido
        //  para poder establecer el value en la consulta
        $this->queryToExecute = "select * from $this->mainEntity where $field = $value";        
        $this->execute();
        return $this->entityData;        
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

}