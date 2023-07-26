<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EmpresaModel;
use HappySoftware\Controller\HelperController;

class EmpresaController extends FrontController{

    private $empresaModel;
    public $EmpresaModel;

    public function __construct($params = null)
    {
        $this->InitModel('Empresa', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {

            $envioEmailRegistro = false;
            $nombreComunidad = '';

        //  Comprobamos si es una empresa que se ha dado de alta desde el CAE ya que de ser así habrá que enviar un e-mail
            if(isset($datos['fromcae']))
            {
                $envioEmailRegistro = true;
                unset($datos['fromcae']);
            }

        //  Comprobamos si viene informado el nombre de la comunidad
            if(isset($datos['comunidad']))
            {
                $nombreComunidad = $datos['comunidad'];
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
                    $administrador = $this->EmpresaModel->GetNombreAdministrador($this->getLoggedUserId());

                //  Template email
                    $templateEmail = $this->GetTemplateEmailAltaProveedor();

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
            // die($idEmpresa);
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

    public function GetTemplateEmailAltaProveedor(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/alta_empresa.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

}