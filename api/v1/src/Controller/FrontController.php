<?php

namespace Fincatech\Controller;

use HappySoftware\Controller\ConfigController;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\ConfigTrait;
use HappySoftware\Controller\Traits\ExcelGen;
use HappySoftware\Controller\Traits\FilesTrait;
use HappySoftware\Controller\Traits\LogTrait;
use HappySoftware\Controller\Traits\MailTrait;
use HappySoftware\Controller\Traits\SmsTrait;
use HappySoftware\Controller\Traits\SecurityTrait;
use HappySoftware\Controller\Traits\UanatacaTrait;
use \HappySoftware\Model\Model;

use \PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;

class FrontController{

    use ConfigTrait, FilesTrait, LogTrait, MailTrait, SmsTrait, SecurityTrait;
    use UanatacaTrait;

    /**
     * Hace referencia al controller instanciado por la aplicación desde la llamada del api
     * @var Controller
     */
    public $context;
    protected $model; // protected
    public $helperModel;
    public $excel;
    /**
     * Variable donde se almacena el objeto post que es enviado en la petición al api
     * @var string
     */
    public $jsonPostData;

    public function __construct()
    {
        $this->InstantiateHelperModel();
        require_once ABSPATH .'src/Includes/database/mysqlcore.php';
        $this->excel = new ExcelGen();
        $this->checkFolders();
    }

    /**
     * Inicializa el objeto Controller
     */
    public function Init($controllerName, $params = null)
    {
        //  Instancia del trait de configuración
        //  TODO: Auto incluir todos los traits en función del controlador que se llama
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

        if(!class_exists($controllerInstance))
        {
            //  Incluimos el fichero
            include(ABSPATH.'src/Controller/'.$controllerName.'.php');
        }

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
        $instanceName = ConfigTrait::getHSNamespaceName() . 'Model\\' . $modelName;
        
        $this->$modelName = new $instanceName($params);        
    }

    private function InstantiateEntityObject()
    {
        include_once(ABSPATH.'src/Entity/SchemaEntity.php'); 
    }

    public function InstantiateHelperModel()
    {
        //  Instanciamos el helpermodel
        require_once(ABSPATH.'src/Model/Model.php');
        $this->model = new Model();
        $this->helperModel = new Model();
    } 

    protected function GetHelperModel()
    {
        return $this->helperModel;
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
        $data = $this->context->Create($entidadPrincipal, $jsonPostData);
        //  Validar la respuesta por si tiene error        
        if(isset($data['error']))
        {
            return HelperController::errorResponse($data, $data['error'], 200);
        }else{
            return HelperController::successResponse( $data );
        }
    }

    public function Delete($id)
    {
        return HelperController::successResponse($this->context->Delete($id));
    }

    public function Update($entidadPrincipal, $jsonPostData, $entidadId)
    {
          $data = $this->context->Update($entidadPrincipal, $jsonPostData, $entidadId);

          //  Validar la respuesta por si tiene error        
          if(isset($data['error']))
          {
              return HelperController::errorResponse($data, $data['error'], 200);
          }else{          
            return HelperController::successResponse( $data );
          }
    }

    /** Obtiene una entidad con todas sus relaciones */
    public function Get($id)
    {
        $data = $this->context->Get($id);
        //  Validar la respuesta por si tiene error        
        if(isset($data['error']))
        {
            return HelperController::errorResponse($data, $data['error'], 200);
        }else{
            return HelperController::successResponse( $data );
        }
    }

    /** Lista todos los registros para una entidad */
    public function List($params)
    {
        //  Validamos si hay usuario autenticado en el sistema
        if($this->getLoggedUserId() == '-1')
        {
            return HelperController::errorResponse('error','Acceso denegado',403);
        }else{
            //  Validar la respuesta por si tiene error
            return HelperController::successResponse( $this->context->List($params) );
        }
    }

    public function Search($jsonSearchData)
    {
        //  Denegamos el acceso a usuarios que no estén autenticados en el sistema
        if($this->getLoggedUserId() == '-1')
        {
            return HelperController::errorResponse('error','Acceso denegado',403);
        }

        //  Comprobamos si el método está definido para el controller
        if(!method_exists($this->context, 'Search'))
            return HelperController::errorResponse('error', 'Método no implementado', 200);
        
        //  Llamamos al método buscar del modelo
        $data = $this->context->Search($jsonSearchData);
        //  Validar la respuesta por si tiene error        
        if(isset($data['error']))
        {
            return HelperController::errorResponse($data, $data['error'], 200);
        }else{
            return HelperController::successResponse( $data );
        }
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

        $datos = (isset($_requestData['entidad']) ? json_decode($_requestData['entidad']) : null) ;
        $paginacion = (!isset($_requestData['paginacion']) ? false : $_requestData['paginacion']);
        $isEdit = !is_null($datos);
                
        $vistaRenderizado = ABSPATH.'src/Views/templates/';
        $htmlOutput = '';
        $rutaVista = $vistaRenderizado . $viewFolder . "/";

        ob_start();
            include_once($rutaVista . $view . ".php");
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

    public function ExecuteTest()
    {
        $this->InitController('Certificadodigital');
        $this->CertificadodigitalController->Test();
        return true;
    }

}