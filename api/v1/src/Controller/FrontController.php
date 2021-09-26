<?php

namespace Fincatech\Controller;

use HappySoftware\Controller\ConfigController;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\ConfigTrait;
use HappySoftware\Controller\Traits\FilesTrait;

use HappySoftware\Model\Model;

class FrontController{

    use ConfigTrait, FilesTrait;

    /**
     * Hace referencia al controller instanciado por la aplicación desde la llamada del api
     * @var Controller
     */
    public $context;
    protected $model;
    public $helperModel;

    /**
     * Variable donde se almacena el objeto post que es enviado en la petición al api
     * @var string
     */
    public $jsonPostData;

    public function __construct()
    {
        require_once ABSPATH .'src/Includes/database/mysqlcore.php';
    }

    /**
     * Inicializa el objeto Controller
     */
    public function Init($controllerName, $params = null)
    {
        //  Instancia del trait de configuración
        //  TODO: Auto incluir todos los traits en función del controlador que se llama
        $this->InstantiateHelperModel();
        $this->InstantiateController($controllerName, $params);
        $this->InstantiateEntityObject();

    }

    /**
     * Instancia un controller por su nombre dentro de la variable context del Front Controller
     */
    public function InitController($controllerName, $params = null)
    {
        $controllerName = ucfirst($controllerName).'Controller';
        $controllerInstance = __NAMESPACE__ . '\\'. $controllerName;

        //  Incluimos el fichero
        include(ABSPATH.'src/Controller/'.$controllerName.'.php');

        //  Instanciamos el controller
        $this->$controllerName = new $controllerInstance($params);
    }

    /**
     * Instancia un controller por su nombre dentro de la variable context del Front Controller
     */
    private function InstantiateController($controllerName, $params = null)
    {
        $controllerName = ucfirst($controllerName).'Controller';
        $controllerInstance = __NAMESPACE__ . '\\'. $controllerName;

        //  Incluimos el fichero
        include_once(ABSPATH.'src/Controller/'.$controllerName.'.php');

        //  Instanciamos el controller
        $this->context = new $controllerInstance($params);
    }

    /**
     * Inicializa el modelo asociado al controller
     */
    public function InitModel($modelName, $params = null)
    {
        //  Instanciamos el modelo principal y el modelo del controller
        $modelName = ucfirst($modelName).'Model';
        include_once(ABSPATH.'src/Model/'.$modelName.'.php');

        //$this->model->setMainModel( $modelName );
        $instanceName = ConfigTrait::getNamespaceName() . 'Model\\' . $modelName;
        
        $this->$modelName = new $instanceName($params);        
    }

    private function InstantiateEntityObject()
    {
        include_once(ABSPATH.'src/Entity/SchemaEntity.php'); 
    }

    public function InstantiateHelperModel()
    {

        //  Instanciamos el helpermodel
        include_once(ABSPATH.'src/Model/Model.php');
        $this->model = new Model();

    }

    public function GetController($controllerName)
    {
        $controllerName = ucfirst($controllerName).'Controller';
        $controllerInstance = __NAMESPACE__ . '\\'. $controllerName;

        //  Incluimos el fichero
        include_once(ABSPATH.'src/Controller/'.$controllerName.'.php');

        //  Instanciamos el controller y lo devolvemos
        return new $controllerInstance();      
    }

    public function Create($entidadPrincipal, $jsonPostData)
    {
        //  Llamamos al método crear del modelo
        return HelperController::successResponse( $this->context->Create($entidadPrincipal, $jsonPostData) );
    }

    public function Delete($id)
    {
        return HelperController::successResponse($this->context->Delete($id));
    }

    public function Update($entidadPrincipal, $jsonPostData, $entidadId)
    {
          //  Comprobamos si viene informado el ID de la entidad, si viene informado quiere decir que es un update
        return HelperController::successResponse( $this->context->Update($entidadPrincipal, $jsonPostData, $entidadId) );
    }

    /** Obtiene una entidad con todas sus relaciones */
    public function Get($id)
    {
        //  Validar la respuesta por si tiene error
        return HelperController::successResponse( $this->context->Get($id) );
    }

    /** Lista todos los registros para una entidad */
    public function List($params)
    {
        //  Validar la respuesta por si tiene error
        return HelperController::successResponse( $this->context->List($params) );
    }

    public function GetTable($params)
    {
        
    }

    public function GetSchemaAction()
    {
        //  Validar la respuesta por si tiene error
        return HelperController::successResponse( $this->context->getSchemaEntity() );
    }

   /**
     */
    public function renderView($_requestData)
    {

        $viewFolder = $_requestData['viewfolder'];
        $view = $_requestData['view'];
        $datos = (isset($_requestData['entidad']) ? isset($_requestData['entidad']): null) ;
        $paginacion = (!isset($_requestData['paginacion']) ? false : $_requestData['paginacion']);
        
        $vistaRenderizado = ABSPATH.'src/Views/templates/';
        $htmlOutput = '';

        ob_start();
            include_once($vistaRenderizado . $viewFolder . "/" . $view . ".php");
            $htmlOutput = ob_get_contents();
        ob_end_clean();

        //  Si tiene paginación la incluimos
        if($paginacion)
        {
            // ob_start();
            //     include_once($vistaRenderizado . "comunes/paginacion.php");
            //     $htmlOutput .= ob_get_contents();
            // ob_end_clean();
        }

        return $htmlOutput;

    }

}