<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EmpresaModel;
use HappySoftware\Controller\HelperController;
use Fincatech\Controller\ComunidadController;
use HappySoftware\Database\DatabaseCore;
use PHPUnit\TextUI\Help;

class EmpresaController extends FrontController{

    private $empresaModel;
    public $EmpresaModel;
    protected $DocumentalController;
    protected $ComunidadController;

    public function __construct($params = null)
    {
        $this->InitModel('empresa', $params);
    //    $this->EmpresaModel = new EmpresaModel($params);
    }

    public function Create($entidadPrincipal, $datos)
    {

            $envioEmailRegistro = false;
            $nombreComunidad = '';
            $usuarioId = $this->getLoggedUserId();
            $administrador = '';

        //  Comprobamos si es una empresa que se ha dado de alta desde el CAE ya que de ser así habrá que enviar un e-mail
            if(isset($datos['fromcae']))
            {
                $envioEmailRegistro = true;
                unset($datos['fromcae']);
            }

        //  Comprobamos si viene informado el nombre de la comunidad
            if(isset($datos['comunidad']))
            {
                //  Nombre de la comunidad
                $nombreComunidad = $datos['comunidad']['nombre'];
                //  ID de la comunidad
                $idComunidad = $datos['comunidad']['id'];

                $this->InitController('Comunidad');

                //  Comprobamos si la comunidad pertenece a un usuario autorizado
                $usuarioAutorizado = $this->ComunidadController->GetUsuarioAutorizado($idComunidad);

                if($usuarioAutorizado !== false)
                {
                    //  Si pertenece a un usuario autorizado hay que recuperar el usuario por su ID
                    $usuarioId = $usuarioAutorizado['id'];
                    $administrador = $usuarioAutorizado['nombre'];
                }

                unset($datos['comunidad']);

            }

        //  Llamamos al método de crear
            $result = $this->EmpresaModel->Create('Empresa', $datos);

            if($envioEmailRegistro)
            {
                
                //  Recuperamos el password generado en el modelo
                    $password = $this->EmpresaModel->getPasswordGenerated();

                //  Recuperamos el nombre de la empresa
                    $nombreEmpresa = $datos['razonsocial'];
                    $emailEmpresa = $datos['email'];

                //  Recuperamos el nombre del administrador
                    $administrador = ($administrador == '' ? $this->EmpresaModel->GetNombreAdministrador( $usuarioId ) : $administrador);

                //  Template email
                    $templateEmail = $this->GetTemplateEmailAltaProveedor();

                //  Acceso a la plataforma
                    // $url = $this->GetURL();
                    $templateEmail = str_replace('[@enlaceacceso]', $this->GetURL(), $templateEmail);

                //  Parseamos los datos
                    $templateEmail = str_replace('[@administrador@]', $administrador, $templateEmail);
                    $templateEmail = str_replace('[@comunidad@]', $nombreComunidad, $templateEmail);
                    $templateEmail = str_replace('[@email@]', $emailEmpresa, $templateEmail);
                    $templateEmail = str_replace('[@password@]', $password, $templateEmail);
                    $templateEmail = str_replace('[@empresa@]', $nombreEmpresa, $templateEmail);

                //  Enviamos un e-mail a la empresa nueva que se ha creado para que entre en el sistema
                $this->SendEmail($emailEmpresa, $nombreEmpresa, 'Fincatech - Alta en la plataforma', $templateEmail);

            }

        return $result;

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->EmpresaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->EmpresaModel->getSchema();
    }

    public function Delete($id)
    {
        //  Eliminamos toda la información relacionada con la empresa
        
        $this->EmpresaModel->DeleteRelatedData($id);
        return $this->EmpresaModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->EmpresaModel->Get($id);
    }

    public function Search($searchData)
    {
        $results = $this->EmpresaModel->Search($searchData);
        return $results;
    }

    public function List($params = null)
    {
        if(isset( $params['search']['value'] ))
        {
            $params['searchfields'] =[];
            $params['searchvalue'] = $params['search']['value'];
            $params['searchfields'][0]['field'] = "razonsocial";
            $params['searchfields'][0]['operator'] = "%";
            $params['searchfields'][1]['field'] = "email";
            $params['searchfields'][1]['condition'] = "or";
            $params['searchfields'][1]['operator'] = "%";                                  
        }        
       return $this->EmpresaModel->List($params);
    }

    public function ValidacionDocumentacionCAECompleta($idEmpresa)
    {
        $resultado = true;
        $documentacion = $this->EmpresaModel->GetDocumentacionCAE($idEmpresa);
        if(!is_array($documentacion))
        {
            return false;
        }
        for($iDocCAE = 0; $iDocCAE < count($documentacion); $iDocCAE++)
        {
            if($documentacion[$iDocCAE]['idrelacion'] === 'null' || $documentacion[$iDocCAE]['idrelacion'] === null ||
            trim($documentacion[$iDocCAE]['idrelacion'] === '') || empty($documentacion[$iDocCAE]['idrelacion']))
            {
                $resultado = false;
                break;
            }
        }
        return $resultado;
    }

    public function DocumentacionCAE($idEmpresa)
    {
        return $this->EmpresaModel->GetDocumentacionCAE($idEmpresa);
    }

    /** Recupera las comunidades que tiene asignadas un contratista */
    public function GetComunidades($idEmpresa)
    {

        //---------------------------
        //  Validación de seguridad
        //---------------------------

        //  Si no es un usuario de tipo SUDO, se comprueba si es un usuario de tipo contratista
            // if(!$this->isSudo())
            //     $idEmpresa = ($this->isContratista() ? $this->getLoggedUserId() : -1);

        //FIXME: Revisar seguridad de usuario autenticado

        if($this->isContratista())
        {
            $idEmpresa = $this->getLoggedUserId();
        }

        $data = [];
        $data['Comunidades'] = $this->EmpresaModel->GetComunidades($idEmpresa);
        $data['total'] = count($data);
        return HelperController::successResponse( $data );
        
    }

    /** Devuelve los empleados de una empresa */
    public function GetEmpleados($idEmpresa)
    {
        //  Instanciamos el controller de empleados
        $empleados = $this->EmpresaModel->GetEmpleados($idEmpresa);

        //  Recuperamos todos los empleados
        return $empleados;
    }

    /** Recupera el template de alta de proveedor */
    public function GetTemplateEmailAltaProveedor(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/alta_empresa.html';
        $htmlOutput = file_get_contents($vistaRenderizado);
        return $htmlOutput;
        // ob_start();
        //     include($vistaRenderizado);
        //     $htmlOutput = ob_get_contents();
        // ob_end_clean();
        // return $htmlOutput;
    }

    /**
     * Comunidades que tiene asignadas una empresa
     * @param int $idEmpresa ID de la empresa para la que se va a recuperar las comunidades que tenga asignadas
     */
    public function ComunidadesAsignadas($idEmpresa)
    {
        return $this->EmpresaModel->ComunidadesAsignadas($idEmpresa);
    }

    public function GetComunidadesAsignadas($idEmpresa)
    {
        $data = [];
        $data['Comunidades'] = $this->EmpresaModel->ComunidadesAsignadas($idEmpresa);
        return HelperController::successResponse($data);
    }

    /**
     * Reasigna las comunidades de un proveedor a otro
     */
    public function ReasignarComunidades($idEmpresa, $idEmpresaDestino)
    {
        //  Recuperamos las comunidades que tiene asignadas actualmente el proveedor antiguo
        $comunidades = $this->EmpresaModel->ComunidadesAsignadas($idEmpresa);
        $this->EmpresaModel->ReasignarComunidades($idEmpresa, $idEmpresaDestino);
        $idsComunidades = '';

        $this->EmpresaModel->SetId($idEmpresaDestino);

        //  Recuperamos las comunidades para el nuevo proveedor
        //$comunidadesNuevas = $this->EmpresaModel->ComunidadesAsignadas($idEmpresaDestino);
        //  Si tiene comunidades, comprobamos si tiene la documentación CAE para poder enviarla al proveedor
        if(count($comunidades) > 0)
        {
            $this->InitController('Documental');
            for($iComunidad = 0; $iComunidad < count($comunidades); $iComunidad++)
            {
                $comunidad = $comunidades[$iComunidad];
                $this->DocumentalController->comprobarDocumentacionComunidad($comunidad['idcomunidad'], $idEmpresaDestino);
                $idsComunidades .= $comunidad['idcomunidad'] . ", ";
            }
        }
        
        //  Enviar e-mail de nueva asignación (mirar método y controller)
        //  Enviamos un e-mail a la empresa nueva que se ha creado para que entre en el sistema
        //  Template email
        //$templateEmail = $this->GetTemplateEmailAltaProveedor();
        // $this->SendEmail($emailEmpresa, $nombreEmpresa, 'Fincatech - Alta en la plataforma', $templateEmail);
        
        //  Registramos el cambio en el log para posibles comprobaciones o fallos
        $this->WriteToLog('empresa','ReasignarComunidades', 'ID empresa Origen: '.$idEmpresa.' | ID empresa Destino: ' . $idEmpresaDestino . ' | IDs Comunidades Asignadas: ' . $idsComunidades);
        return HelperController::successResponse('ok');
    }

    /**
     * Devuelve aquellas empresas que están registradas pero no han accedido nunca
     */
    public function EmpresasRegistradasSinAcceso()
    {
        $empresas = $this->EmpresaModel->EmpresasRegistradasSinAcceso();
        $data = [];
        $data['empresas'] = $empresas;
        if(count($data['empresas']) >  0)
        {
            for($i = 0; $i < count($data['empresas']); $i++)
            {
                //  Actuaciones realizadas
                $this->EmpresaModel->SetIdEmpresa($data['empresas'][$i]['id']);
                $data['empresas'][$i]['actuaciones'] =  $this->EmpresaModel->Actuaciones();
                // $data['empresas'][$i]['estadoprotocolo'] =  '1';
                //  Fecha última actuación realizada

            }
        }

        return HelperController::successResponse($data);
    }

    /**
     * Listado de actuaciones realizadas en el seguimiento de una empresa
     */
    public function ListadoActuaciones($empresaId)
    {
        //  Devuelve las actuaciones realizadas en el seguimiento de una empresa por su ID
        $data = [];
        $this->EmpresaModel->SetIdEmpresa(DatabaseCore::PrepareDBString($empresaId));
        $emailsAlta = $this->EmpresaModel->EmailAlta();
        $actuaciones =  $this->EmpresaModel->Actuaciones();
        if(!is_null($emailsAlta))
        {
            $actuaciones = array_merge($actuaciones, $emailsAlta);
        }
        $data['actuaciones'] = $actuaciones;
        return $data;
    }

    /**
     * Crea una actuación para una empresa
     */
    public function CreateActuacion($empresaId, $data)
    {

        $fecha = DatabaseCore::PrepareDBString($data['fecha']);
        $tipo = DatabaseCore::PrepareDBString($data['tipo']);
        $observaciones = DatabaseCore::PrepareDBString($data['observaciones']);

        $this->EmpresaModel->SetUserCreate($this->getLoggedUserId());
        $this->EmpresaModel->SetIdEmpresa($empresaId)->SetTipo($tipo)->SetObservaciones($observaciones)->SetFecha($fecha);

        $this->EmpresaModel->CreateActuacion();

        return HelperController::successResponse('ok');
    }

    /**
     * Elimina una actuación asociada a una empresa
     */
    public function DeleteActuacion($empresaId, $actuacionId)
    {
        $empresaId = DatabaseCore::PrepareDBString($empresaId);
        $actuacionId = DatabaseCore::PrepareDBString($actuacionId);

        $this->EmpresaModel->SetIdEmpresa($empresaId)->SetId($actuacionId);
        
        $this->EmpresaModel->DeleteActuacion();
        return HelperController::successResponse('ok');
    }

    public function FinalizarSeguimiento($empresaId)
    {
        $empresaId = DatabaseCore::PrepareDBString($empresaId);
        $this->EmpresaModel->SetIdEmpresa($empresaId);
        $this->EmpresaModel->FinishFollow();
        return HelperController::successResponse('ok');
    }

    /**
     * Envía un e-mail de alta en fincatech a una empresa
     */
    public function EnvioEmailAltaPlataforma($empresa, $nombreComunidad, $idAdministrador, $administrador = null, $password = null)
    {
        if(is_null($password)){
            $password = $this->EmpresaModel->getPasswordGenerated();
        }
        
        //  Recuperamos el nombre de la empresa
            $nombreEmpresa = $empresa['razonsocial'];
            $emailEmpresa = $empresa['email'];

        //  Recuperamos el nombre del administrador
            $administrador = (is_null($administrador) ? $this->EmpresaModel->GetNombreAdministrador( $idAdministrador ) : $administrador);
        
        //  Template email
            $templateEmail = $this->GetTemplateEmailAltaProveedor();

        //  Acceso a la plataforma
            $templateEmail = str_replace('[@enlaceacceso]', $this->GetURL(), $templateEmail);

        //  Parseamos los datos
            $templateEmail = str_replace('[@administrador@]', $administrador, $templateEmail);
            $templateEmail = str_replace('[@comunidad@]', $nombreComunidad, $templateEmail);
            $templateEmail = str_replace('[@email@]', strtolower($emailEmpresa), $templateEmail);
            $templateEmail = str_replace('[@password@]', $password, $templateEmail);
            $templateEmail = str_replace('[@empresa@]', $nombreEmpresa, $templateEmail);

        //  Enviamos un e-mail a la empresa nueva que se ha creado para que entre en el sistema
            $this->SendEmail($emailEmpresa, $nombreEmpresa, 'Fincatech - Alta en la plataforma', $templateEmail);

    }


    /**
     * Comprueba si el e-mail de la empresa está incluido en la blacklist
     * @param string $emailEmpresa. Email de la empresa que se va a validar
     * @return bool True | False Indicando si está o no incluido en la blacklist
     */
    public function EmailInBlacklist( $emailEmpresa )
    {
        $this->EmpresaModel->SetEmail($emailEmpresa);
        //  Comprobamos si el e-mail está en la lista negra
        return intval($this->EmpresaModel->EmailBlackList()) > 0;
    }

}