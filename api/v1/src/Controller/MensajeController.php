<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\Mensaje;
use HappySoftware\Controller\HelperController;

class MensajeController extends FrontController{

    private $nombreModel;
    public $MensajeModel;

    public function __construct($params = null)
    {
        $this->InitModel('Mensaje', $params);
    }
    
    public function _Save()
    {
        return $this->MensajeModel->_Save();
    }

    // public function Create($entidadPrincipal, $datos)
    // {
    //     //  Llamamos al método de crear
    //     return $this->MensajeModel->Create($entidadPrincipal, $datos);
    // }
    public function Insert($email, $subject, $body, $mensajeCertificadoId = null, $emailRecipient = null){
        $this->MensajeModel->setRecipient($email);
        $this->MensajeModel->setSubject($subject);
        $this->MensajeModel->setBody($body);+
        $this->MensajeModel->setMensajeCertificadoId($mensajeCertificadoId);
        $this->MensajeModel->setDestinatarioNombre($emailRecipient);
        $this->MensajeModel->_Save();
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->MensajeModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->MensajeModel->getSchema();
    }

    public function Delete($id)
    {
        return null;
    }

    public function Get($id)
    {
        return $this->MensajeModel->Get($id);
    }

    public function List($params = null, $useLoggedUserId = false)
    {
        if(isset( $params['search']['value'] ))
        {
            $params['searchfields'] =[];
            $params['searchvalue'] = $params['search']['value'];
            $params['searchfields'][0]['field'] = "subject";
            $params['searchfields'][0]['operator'] = "%";
            $params['searchfields'][1]['field'] = "email";
            $params['searchfields'][1]['condition'] = "or";
            $params['searchfields'][1]['operator'] = "%";                                  
        }

        //  Comprobamos si viene orden
        if(isset($params['order']))
        {
            $orderType = $params['order'][0]['dir'];
            switch($params['order'][0]['column'])
            {
                case 0: // Asunto
                    $params['orderby'] = 'subject';
                    break;
                case 1: // Email
                    $params['orderby'] = 'email';
                    break;
                case 2: //  Fecha envío
                    $params['orderby'] = 'created';
                    break;
            }
            $params['order'] = $orderType;
        }

        $resultado = $this->MensajeModel->List($params, $useLoggedUserId);
        for($x = 0; $x < count($resultado['Mensaje']); $x++)
        {
            unset($resultado['Mensaje'][$x]['body']);
        }
        return $resultado;
    }

    /** Reenvía un mensaje previamente enviado */
    public function ResendMessage($idMensaje, $increment = false, $customSubject = '', $additionalBody = '')
    {

        //  Recuperamos el mensaje y reenviamos el mismo contenido
        $mensaje = $this->Get($idMensaje);

        if(!is_null($mensaje))
        {
            $body = html_entity_decode($mensaje['Mensaje'][0]['body'], ENT_QUOTES);
            
            //  Por alguna razón, cuando decodifica las urls suele meterle una barra de más por lo que lo saneamos con un replace
            $body = str_replace('https:/app','https://app', $body);
            $body = str_replace('http:/www', 'http://www', $body);
            
            $to = $mensaje['Mensaje'][0]['email'];

            //  Si tiene texto adicional, lo inyectamos justo antes del e-mail y 
            if($additionalBody != '')
                $body = $additionalBody . $body;

            //  Si el asunto es diferente, lo establecemos
            if($customSubject != '')
            {
                $subject = $customSubject;
            }else{
                $subject = html_entity_decode($mensaje['Mensaje'][0]['subject'], ENT_QUOTES);
            }

            $this->SendEmail($to, '', $subject, $body, true);

            //  Comprobamos si hay que incrementar el número de envío
            if($increment)
                $this->IncrementSendNumber( $idMensaje );

            return HelperController::successResponse('ok', 200);
        }else{
            return HelperController::errorResponse('error', 'Error reenviando mensaje',200);
        }

    }

    public function GetEmailRegistroIdByEmail($email)
    {
        return $this->MensajeModel->GetEmailRegistroByEmail($email);
    }

    public function SaveCertificado($idMensajeCertificado, $idComunidad, $idEmpresa)
    {
        $this->MensajeModel->SaveCertificado($idMensajeCertificado, $idComunidad, $idEmpresa);
    }

    /** Comprueba si un mensaje devuelto desde el Api de Mensatek es un sms certificado */
    public function IsSMSCertificated($idMensaje)
    {
        //  Buscamos en la tabla de sms certificados

        //  Si existe devolvemos true
    }

    /** Comprueba si un mensaje devuelto desde el Api de Mensatek es un e-mail certificado */
    public function IsEmailCertificated($idMensaje)
    {
        //  Buscamos en la tabla de emails certificados

        //  Si Existe devolvemos true
    }

    public function saveFileEmailCertificado($idMensaje, $fileContent)
    {
        global $appSettings;

        try{

        //  Generamos un nombre de fichero basado en timestamp
            $fileGenerated = $this->saveZipFile($fileContent);
            
        //  ORR: 27/06/2023 -> Se añade esta validación ya que mensatek devuelve a veces ficheros con peso de 1Kb 
        //                     y en ciertas casuísticas se muestra un fichero corrupto
        //  Validamos que el fichero recibido tenga un peso superior a 10Kb
            $pathFileGenerated = ROOT_DIR . $appSettings['storage']['certificados'] . '/' . $fileGenerated;
            $fileSize = filesize($pathFileGenerated);
            $fileSize = round($fileSize / 1024, 2);

            if($fileSize >= 3)
            {
                //  Guardamos el registro en la base de datos
                $this->MensajeModel->updateCertificado($idMensaje, $fileGenerated);
                return $fileGenerated;
            }else{
                //  Eliminamos el fichero del sistema
                unlink($pathFileGenerated);
            }
        }catch(\Exception $ex)
        {
            die('Error (saveFileEmailCertificado): ' . $ex->getMessage() );
        }
    }

    public function GetEmailsCertificadosAdministrador(){
        $data['Mensaje'] = $this->MensajeModel->EmailsCertificadosAdministrador();
        return $data;
    }

    /**
     * Envío de e-mail avisando a administrador que la empresa X no ha accedido nunca desde su registro.
     */
    public function SendEmailRecordatorioAccesoAdministrador()
    {

    }

    /**
     * Envía un email recordatorio de acceso a la plataforma para empresas que no han entrado nunca desde su registro
     */
    public function SendEmailRecordatorioAccesoEmpresa()
    {

    }

    /**
     * Actualiza el número de envíos del mismo e-mail
     */
    public function IncrementSendNumber($idMensaje)
    {
        $this->MensajeModel->UpdateSendNumber($idMensaje);
    }

}