<?php

/*
  Controller: Certificados digitales
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Controller\FrontController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\RepresentantelegalController;
use Fincatech\Controller\SmsController;
use Fincatech\Controller\AdministradorController;
use Fincatech\Model\CertificadodigitalModel;

use Happysoftware\Controller\HelperController;
use Happysoftware\Controller\Traits;
use Happysoftware\Controller\Traits\MailTrait;
use HappySoftware\Database\DatabaseCore;
use PHPUnit\TextUI\Help;

class CertificadodigitalController extends FrontController{

    public $certificadodigitalModel;
    public $CertificadodigitalModel;
    public $UsuarioController, $ComunidadController, $AdministradorController, $FicheroscomunesController, $RepresentantelegalController;
    public $SmsController;

    public $_logMessage;
    //  Se utiliza para almacenar la información de los resultados de generación del certificado digital
    private $_certificateRequestReport;

    private function InitCertificateRequestReport()
    {
        $this->_certificateRequestReport = '';
    }
    private function SetCertificateRequestReport($value)
    {
        $this->_certificateRequestReport .= $value;
    }
    private function CertificateRequestReport()
    {
        return $this->_certificateRequestReport;
    }

    public function __construct($params = null)
    {
        parent::__construct(); 

        $this->InitModel('Certificadodigital', $params);
        //  Inicializamos el controller de Mensajes
        $this->InitController('Mensaje');

    }

    /**
     * Create Certificado digital
     */
    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->CertificadodigitalModel->Create($entidadPrincipal, $datos);
    }

    /**
     * Update
     */
    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->CertificadodigitalModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->CertificadodigitalModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->CertificadodigitalModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->CertificadodigitalModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->CertificadodigitalModel->List($params);
    }

    //  Envío de SMS
    public function SendSMS($phoneNumber, $sender, $message, $fileName = null, $fileBase64 = null, $storageFileId = null)
    {
        $smsContract = false;

        $this->InitController('Administrador');
        $administrador = $this->AdministradorController->Get($sender);
        $cifAdministrador = $administrador['Usuario'][0]['cif'];
        $telefonoAdministrador = $administrador['Usuario'][0]['telefono'];
        $telefonoAdministrador = str_replace(' ','',$telefonoAdministrador);
        $telefonoAdministrador = str_replace('+','',$telefonoAdministrador);
        $telefonoAdministrador = str_replace('-','',$telefonoAdministrador);
        $nombreAdministrador = $administrador['Usuario'][0]['nombre'];

        //  Enviamos el mensaje
        if(is_null($fileName) && is_null($fileBase64) && is_null($storageFileId))
        {
            $result = $this->SendSMSCertificated($phoneNumber, $message, $nombreAdministrador, $cifAdministrador, $telefonoAdministrador);
        }else{
            //  Es un sms con contrato adjunto por lo que debemos almacenar
            //  primero el fichero en el almacén
            $smsContract = true;
            $this->InitController('Ficheroscomunes');
            $fichero = $this->FicheroscomunesController->Get($storageFileId);
            $ficheroNombreStorage = $fichero['FicherosComunes'][0]['nombrestorage'];
            $result = $this->SendSMSContractCertified($telefonoAdministrador, $message, $ficheroNombreStorage, $nombreAdministrador, $cifAdministrador, $telefonoAdministrador);
        }

        //  Esperamos a la respuesta
        if($result !== true)
        {
            return $result;
        }else{
            //  Creamos el registro en base de datos
            $this->InitController('Sms');
            $data = [];
            $data['idusuario'] = $sender;
            $data['phone'] = $phoneNumber;
            $data['message'] = DatabaseCore::PrepareDBString($message);
            $data['message'] = str_replace('&',' ', $data['message']);
            //  Si es un sms con contrato adjunto, se almacena el fichero en el almacén
            if($smsContract){
                $data['storagefileid'] = $storageFileId;
            }

            $this->SmsController->Create('sms', $data);
        }

        //  Devolvemos la respuesta al front controller
        return 'ok';

    }

    /** 
     * Contrato firmado por SMS
     */ 
    public function SendSMSContrato( $phoneNumber, $sender, $message, $ficheroBase64, $ficheroNombre)
    {
        //  Subimos el fichero al almacén y recuperamos el ID
            $storageFileId = $this->uploadFile($ficheroNombre, $ficheroBase64);
  
        //  Enviamos el sms para firmar
        return $this->SendSMS($phoneNumber, $sender, $message, $ficheroNombre, $ficheroBase64, $storageFileId );
    }

    /**
     * Envío e-mail certificado
     */
    public function SendEmailCertificadoAdministrador($subject, $destinatario, $administradorId, $mensaje, $nombreDestinatario = null)
    {

        $destinatario = DatabaseCore::PrepareDBString($destinatario);
        $destinatario = trim($destinatario);

        //  Comprobamos si el usuario está en la blacklist
        $blackList = $this->EmailBlackList();
        $emailNotSents = array();

        //  Dividimos los destinatarios en un array para poder procesarlo más rápido
        $destinatariosArray = explode(';',$destinatario);

        if(count($blackList) > 0)
        {

            //  Recorremos la lista de los destinatarios
            for($iDestinatario = 0; $iDestinatario < count($destinatariosArray); $iDestinatario++)
            {
                $destinatarioValidate = $destinatariosArray[$iDestinatario];
                //  Comprobamos si está en al lista
                if(in_array($destinatarioValidate, array_column($blackList,'email')))
                {
                    //  Se añade el e-mail a destinatarios no enviados
                    array_push($emailNotSents, $destinatarioValidate);
                }
            }

            //  Si hay destinatarios no válidos devolvemos un mensaje de error antes de enviar
            if(count($emailNotSents) > 0)
                return HelperController::errorResponse('error','Los siguientes e-mails no son válidos o no existen: <br><br>' . implode(', ',$emailNotSents), 200);
        }

        $usuarioId = $this->getLoggedUserId(); // Usuario en sesión
        $mensaje = DatabaseCore::PrepareDBString($mensaje);
        $subject = DatabaseCore::PrepareDBString($subject);

        //  Realizamos el intento de envío
        $resultadoEnvio = $this->SendEmailCertificado($subject, $destinatariosArray, $mensaje, $usuarioId, $nombreDestinatario);

        //  Evaluamos el resultado del envío
        if($resultadoEnvio == 'ok')
        {
            return HelperController::successResponse('ok');
        }else{
            return HelperController::errorResponse('error','El e-mail no se ha podido enviar. Inténtelo pasado unos minutos, si el problema persiste, contacte con nosotros',200);
        }

    }

    public function getRepositorio()
    {
        return parent::GetHelperModel()->getRepositorio();
    }

    /**
     * TODO: Cambiar método de solicitud
     */
    public function CreateRequestCertificates($administradorId, $solicitudesId, $documentoFrontalBase64, $documentoFrontalNombre, $documentoTraseroBase64, $documentoTraseroNombre)
    {
        //  Validamos que el usuario que solicita el certificado sea el mismo que el que está autenticado en el sistema
        if($administradorId != $this->getLoggedUserId())
            return 'Ud. no tiene permiso para realizar esta operación';

        //  Subimos el dni del administrador y generamos el fichero para enviarlo a uanataca
        $documentoFrontalId = $this->uploadFile($documentoFrontalNombre, $documentoFrontalBase64);
        $documentoTraseroId = $this->uploadFile($documentoTraseroNombre, $documentoTraseroBase64);

        //  TODO: Almacenamos de manera temporal el dni del administrador
        $this->InitController('Administrador');
        $this->AdministradorController->SaveDocumentAdministrador($administradorId, $documentoFrontalId, $documentoTraseroId);

        //  Empezamos a crear solicitudes en el sistema para ser procesadas
        foreach($solicitudesId as $solicitud)
        {
            //  Actualizamos la petición marcándola como solicitadocertificado = 1
            $this->CertificadodigitalModel->AdministradorSolicitaCertificado($solicitud);
        }
        
        return true;

    }

    private function UploadRequerimientosAprobadosCertificadoDigitalComunidad($documentosSubidos, $requestKey)
    {
        for($xDoc = 0; $xDoc < count($documentosSubidos); $xDoc++)
        {
            $documento = $documentosSubidos[$xDoc];
            if($documento['idestado'] === '6' || ($documento['idrequerimiento'] == '57' || $documento['nombre'] == 'Declaración responsable y solicitud del certificado digital'))
            {
                //  Nombre del requerimiento
                $nombreRequerimiento = $documento['requerimiento'];
                $requerimientoFicheroId = $documento['idficherorequerimiento'];

                //  Ruta física del fichero
                $documentoPath = $documento['ubicacionficherorequerimiento'] . $documento['storageficherorequerimiento'];

                //  Enviamos a Uanataca el fichero para la solicitud
                $uanatacaDocumentId = $this->UploadDocumentToCertificateRequest($requestKey, $documentoPath, 'extra_document', $nombreRequerimiento);

                if(isset($uanatacaDocumentId['message']['pk']))
                {
                    $this->CertificadodigitalModel->UpdateUanatacaIdFile($requerimientoFicheroId, $uanatacaDocumentId['message']['pk']);
                    $this->SetCertificateRequestReport('<li><strong>ID Uanataca</strong>: ' . $uanatacaDocumentId['message']['pk'] . ' - <strong>Requerimiento</strong>: ' . $nombreRequerimiento . '</li>');                        
                }else{
                    //  Si ha habido error lo reportamos en el informe y cancelamos la solicitud
                    $this->_logMessage .= '- Ha ocurrido un error al realizar el request a Uanataca' . PHP_EOL;
                    $this->CancelRequestCertificate($requestKey);
                    return HelperController::errorResponse('error','No se ha podido adjuntar la declaración responsable',200);
                }

            }
        }
    }

    /**
     * Uploads legal documents of representante legal
     * @param string $requestKey PK Uanataca Certificate Request
     * @param int $docFrontalId ID image frontal document
     * @param int $docAnversoId ID reverse image document
     * @return bool Result of upload
     */
    private function UploadLegalDocumentRepresentante($requestKey, $docFrontalId, $docAnversoId)
    {
        $resultUpload = true;
        $documentoFrontal = $this->GetFileInfoById($docFrontalId);
        $documentoAnverso = $this->GetFileInfoById($docAnversoId);
        $documentoFrontalFile = $documentoFrontal['ubicacion'] . $documentoFrontal['nombrestorage'];
        $documentoAnversoFile = $documentoAnverso['ubicacion'] . $documentoAnverso['nombrestorage'];
        //  DNI parte frontal
        $uanatacaDocumentId = $this->UploadDocumentToCertificateRequest($requestKey, $documentoFrontalFile, 'document_front', 'DNI Frontal');
        if(isset($uanatacaDocumentId['message']['pk']))
        {
            $this->CertificadodigitalModel->UpdateUanatacaIdFile($docFrontalId, $uanatacaDocumentId['message']['pk']);
        }else{
            //  Registramos el error
            $resultUpload = false;
        }

        //  DNI parte trasera
        $uanatacaDocumentId = $this->UploadDocumentToCertificateRequest($requestKey, $documentoAnversoFile, 'document_rear', 'DNI Anverso');
        if(isset($uanatacaDocumentId['message']['pk']))
        {
            $this->CertificadodigitalModel->UpdateUanatacaIdFile($docAnversoId, $uanatacaDocumentId['message']['pk']);
        }else{
            //  Registramos el error
            $resultUpload = false;
        }        
        return $resultUpload;
    }

    /**
     * Solicitud de certificado digital a uanataca
     */
    public function RequestCertificateToUanataca($comunidadId)
    {
        //  Inicializamos el informe de resultado de la solicitud;
        $this->InitCertificateRequestReport();

        //  Log
        $this->_logMessage = '+ Inicio proceso solicitud certificado digital. Comunidad Id: ' . $comunidadId . PHP_EOL;

        //  Instanciamos el controlador del Administrador
        $this->InitController('Administrador');
        //  Instanciamos el controlador de Comunidad
        $this->InitController('Comunidad');
        //  Instanciamos el controlador del Representante Legal
        $this->InitController('Representantelegal');

        //  Recuperamos los documentos que hay subidos y aprobados por el técnico de certificado digital
        $documentosSubidos = $this->ComunidadController->getDocumentacionCertificadoDigitalByComunidadId($comunidadId);

        //  Validamos que tenga los documentos de requerimiento solicitados subidos y en estado aprobado
        if(count($documentosSubidos) > 0)
        {
            $documentosSubidos = $documentosSubidos['documentacioncertificado'];
        }else{
            return HelperController::errorResponse('error','La comunidad no tiene los requerimientos de certificado digital subidos', 200);
        }

        //  Recuperamos los datos de la comunidad 
        $comunidad = $this->ComunidadController->Get($comunidadId);

        //  Debemos comprobar que la comunidad existe y está activa en el sistema
        if(count($comunidad['Comunidad']) > 0)
        {
            $comunidad = $comunidad['Comunidad'][0];
        }else{
            return HelperController::errorResponse('error','La comunidad no existe', 200);
        }

        if($comunidad['estado'] != 'A'){
            return HelperController::errorResponse('error','La comunidad no está activada aún por lo que el certificado no puede emitirse', 200);
        }

        //  Recuperamos el ID del administrador según la comunidad
        $administradorId = $comunidad['usuarioId'];

        //  Recuperamos los datos del administrador
        $administrador = $this->AdministradorController->Get($administradorId);
        //  Validamos que el administrador esté dado de alta en el sistema
        if(count($administrador['Usuario']) > 0)
        {
            $administrador = $administrador['Usuario'][0];
            //  Email del administrador para enviar e-mail tras solicitud
            // $emailAdministrador = $administrador['email'];
        }else{
            return HelperController::errorResponse('error','El administrador asociado no se ha encontrado en el sistema',200);
        }

        //  Recuperamos la solicitud del certificado
        $solicitudCertificado = $this->CertificadodigitalModel->SolicitudCertificado($comunidadId);

        //  Validamos que tenga solicitud asociada la comunidad
        if(count($solicitudCertificado) > 0)
        {
            $solicitudCertificado = $solicitudCertificado[0];
        }else{
            return HelperController::errorResponse('error','La comunidad no tiene solicitud pendiente de certificado digital', 200);
        }

        //  Recuperamos el representante legal en base a la solicitud del certificdo digital
        $representanteLegal = $this->RepresentantelegalController->Get($solicitudCertificado['idrepresentante']);
        //  Validamos que tenga representante legal asociado y esté en el sistema
        if(count($representanteLegal) > 0)
        {
            $representanteLegal = $representanteLegal['Representantelegal'][0];
            $representanteLegalEmail = $representanteLegal['email'];
            $representanteLegalNombreApellidos = $representanteLegal['nombre'] . ' ' . $representanteLegal['apellido'] . ' ' . $representanteLegal['apellido2'];
        }else{
            return HelperController::errorResponse('error','El representante legal asociado no existe en el sistema', 200);
        }

        //  Log
        $this->_logMessage .= 'Inicio comunicación con Uanataca para crear una solicitud de certificado';
        
        $certificateResult = $this->UanatacaCreateCertificateRequest($representanteLegal, $comunidad, $documentosSubidos);
        $this->_logMessage .= '+ Resultado solicitud Uanataca:' . PHP_EOL;
        //  Escritura en log de la respuesta de solicitud de Uanataca
        $this->_logMessage .= json_encode($certificateResult) . PHP_EOL;
        
        $requestKey = '';
        if(isset($certificateResult['message']['pk']))
        {
            //  Recuperamos el ID de Uanataca
            $requestKey = $certificateResult['message']['pk'];

            //  Informe de resultado
            $this->SetCertificateRequestReport('<p><strong>ID Solicitud Certificado digital Uanataca</strong>: ' . $requestKey . '</p><br><br>');

            $this->_logMessage .= '- PK Solicitud devuelta: ' . $requestKey . PHP_EOL;

            //  Adjuntamos las imágenes del dni del representante legal
            if( !$this->UploadLegalDocumentRepresentante($requestKey,$representanteLegal['imagenfrontal'], $representanteLegal['imagentrasera']) )
            {
                $this->CancelRequestCertificate($requestKey);
                return HelperController::errorResponse('error','No se ha podido adjuntar el DNI/NIF a la solicitud', 200);
            }

            //  Subimos el resto de documentos que ya están aprobados recuperándolos previamente
            $this->SetCertificateRequestReport('<p>+ Documentos aportados:</p><p>&nbsp;</p><br>');
            $this->SetCertificateRequestReport('<ul>' . PHP_EOL . PHP_EOL);

            $this->UploadRequerimientosAprobadosCertificadoDigitalComunidad($documentosSubidos, $requestKey);
            // for($xDoc = 0; $xDoc < count($documentosSubidos); $xDoc++)
            // {
            //     $documento = $documentosSubidos[$xDoc];
            //     if($documento['idestado'] === '6' || ($documento['idrequerimiento'] == '57' || $documento['nombre'] == 'Declaración responsable y solicitud del certificado digital'))
            //     {
            //         //  Nombre del requerimiento
            //         $nombreRequerimiento = $documento['requerimiento'];
            //         $requerimientoFicheroId = $documento['idficherorequerimiento'];

            //         //  Ruta física del fichero
            //         $documentoPath = $documento['ubicacionficherorequerimiento'] . $documento['storageficherorequerimiento'];

            //         //  Enviamos a Uanataca el fichero para la solicitud
            //         $uanatacaDocumentId = $this->UploadDocumentToCertificateRequest($requestKey, $documentoPath, 'extra_document', $nombreRequerimiento);

            //         if(isset($uanatacaDocumentId['message']['pk']))
            //         {
            //             $this->CertificadodigitalModel->UpdateUanatacaIdFile($requerimientoFicheroId, $uanatacaDocumentId['message']['pk']);
            //             $this->SetCertificateRequestReport('<li><strong>ID Uanataca</strong>: ' . $uanatacaDocumentId['message']['pk'] . ' - <strong>Requerimiento</strong>: ' . $nombreRequerimiento . '</li>');                        
            //         }else{
            //             //  Si ha habido error lo reportamos en el informe y cancelamos la solicitud
            //             $this->_logMessage .= '- Ha ocurrido un error al realizar el request a Uanataca' . PHP_EOL;
            //             $this->CancelRequestCertificate($requestKey);
            //             return HelperController::errorResponse('error','No se ha podido adjuntar la declaración responsable',200);
            //         }

            //     }
            // }

            $this->SetCertificateRequestReport('</ul>' . PHP_EOL . PHP_EOL);

            //  Lanzamos el request aproval sobre la solicitud inicial una vez adjuntados todos los ficheros
            $requestAprovalResult = $this->RequestApproval($requestKey);
            if(isset($requestAprovalResult['message']['secrets']['puk']))
            {
                //  Una vez recuperado el id de la solicitud lo seteamos en la base de datos
                $this->CertificadodigitalModel->SetUanatacaIdSolicitudCertificadoComunidad($comunidadId, $requestKey);
                //  Actualizamos la solicitud indicando que ya ha sido solicitado a uanataca

            }else{
                //  Registramos el error
                $this->CancelRequestCertificate($requestKey);
                return HelperController::errorResponse('error','No se ha podido realizar la aprobación de la solicitud',200);
            }

            $this->WriteToLog('certificado_digital', 'RequestCertificateToUanataca', $this->_logMessage);
            $certificateResult = true;
            
        }else{
            //  Este error lo enviamos al técnico de certificado digital, al admin y a desarrollo
            //  para que se pueda comprobar por todas las partes
            return HelperController::errorResponse('error','No se ha podido crear la solicitud del certificado digital en Uanataca',200);
        }

        return HelperController::successResponse($requestKey,200);
            
    }

    /**
     * Solicita la aprobación de documentos aportados para la solicitud individual de certificado digital para una comunidad
     * @user_rol Administrador de fincas
     */
    public function SolicitarCertificadoIndividual($idComunidad, $idRepresentanteLegal)
    {

        $resultado = $this->RequestByComunidadId($idComunidad, $idRepresentanteLegal);
        return $resultado;

    }

    /** Solicitud de certificado digital para una comunidad por parte de un administrador de fincas */
    private function RequestByComunidadId($idComunidad, $idRepresentanteLegal)
    {
        $mensajeError = '';

        //  Validamos que el certificado no haya sido emitido todavía
        if($this->ValidateCertificateIsApproved($idComunidad))
            return 'El certificado ya ha sido emitido.<br><br>Revise la bandeja de entrada de su correo electrónico y la carpeta de correo no deseado.<br><br>Si no tiene el e-mail con la información del certificado por favor contacte con info@fincatech.es.';

        //  Validamos que la emisión no haya sido ya generada y aprobada
        if($this->ValidateCertificateAlreadyRequested($idComunidad))
            return 'La solicitud de certificado digital para esta comunidad ya ha sido creada y está siendo gestionada por un Operador de Registro Autorizado.<br>Si la solicitud resulta aprobada se le enviará un e-mail con las instrucciones para descargar su certificado digital.';

        //  Comprobamos si tiene todos los documentos subidos a la plataforma
        $ficherosSubidos = $this->ValidateRequerimientosCertificadoByComunidadId($idComunidad);

        //  Mensaje de error
        if(!$ficherosSubidos){
            $mensajeError = 'La solicitud no puede ser generada porque tiene requerimientos de certificado digital pendientes de subir.';
        }else{
            $this->InitController('Comunidad');
            $requerimientosComunidad = $this->ComunidadController->getDocumentacionCertificadoDigitalByComunidadId($idComunidad);
            $requerimientosComunidad = $requerimientosComunidad['documentacioncertificado'];

            //  Recuperamos los requerimientos de certificado digital para la comunidad
            $requerimientosId = array();
            for($x = 0 ; $x < count($requerimientosComunidad); $x++)
            {
                array_push($requerimientosId, $requerimientosComunidad[$x]['idrelacion']);
            }
            //  Creamos la solicitud en la base de datos
            $this->CertificadodigitalModel
                ->SetIdComunidad($idComunidad)
                ->SetIdRepresentanteLegal($idRepresentanteLegal)
                ->SetRequerimientosIdS(implode(',', $requerimientosId));
            
            $certificadoId = $this->CertificadodigitalModel->Insert();

            // Enviamos un e-mail a los técnicos de revisión documental de Certificados Digitales
            $this->SendEmailCertificadoComunidadSolicitado($idComunidad, $idRepresentanteLegal);
        }

        return ($mensajeError != '' ? $mensajeError : true);

    }

    public function GetCertificateRequestByComunityId($idComunidad)
    {
        return $this->CertificadodigitalModel->GetCertificateRequestByComunityId($idComunidad);
    }

    /**
     * Recupera las comunidades para las que se ha solicitado el certificado y están pendientes de validación
     */
    public function ComunidadesPendienteValidacion()
    {
        $data = [];
        $data['comunidad'] = $this->CertificadodigitalModel->GetComunidadesCertificado(false, false);
        return $data;
    }

    /**
     * Recupera las comunidades que tienen aprobada la documentación pero aún no se ha solicitado el certificado 
     */
    public function ComunidadesPendientesSolicitud($useLoggedUserId = false, $solicitadoUanataca = false, $getAll = false)
    {
        //  Si no está autenticado le denegamos el acceso al listado
        if($this->getLoggedUserId() === -1)
        {
            return 'error';
        }

        $data = [];
        $data['comunidad'] = $this->CertificadodigitalModel->GetComunidadesCertificado(true, $solicitadoUanataca, $useLoggedUserId, $getAll);
        return $data;
    }

    /**
     * Recupera las comunidades que han solicitado el certificado digital y tienen además la documentación aprobada
     */
    public function ComunidadesCertificadoSolicitado()
    {
        $data = [];
        $data['comunidad'] = $this->CertificadodigitalModel->GetComunidadesCertificado(true, true);
        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////
    ///                             VALIDACIONES
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Valida que tenga subidos al menos 5 documentos de certificado digital
     */
    private function ValidateRequerimientosCertificadoByComunidadId($idComunidad)
    {
        $iFicherosSubidos = 0;
        //  Tenemos que recuperar los requerimientos para la comunidad
        //  y usuario que solicita el certificado digital
        //  Instanciamos el controller de comunidad
        $this->InitController('Comunidad');
        $requerimientosCertificado = $this->ComunidadController->getDocumentacionCertificadoDigitalByComunidadId($idComunidad);

        //  Comprobamos si los 4 primeros que son obligatorios están subidos
        $requerimientos = $requerimientosCertificado['documentacioncertificado'];

        for($xReq = 0; $xReq <= 5; $xReq++)
        {
            //  No tiene subido el fichero
            if($requerimientos[$xReq]['idficherorequerimiento'] != '')
                $iFicherosSubidos++;
        }

        //  Se comprueba contra 5 que son los documentos obligatorios para realizar la solicitud
        return ($iFicherosSubidos >= 5);

    }

    /** Valida si una comunidad ya ha solicitado el certificado digital */
    private function ValidateCertificateAlreadyRequested($idComunidad)
    {
        $resultado = $this->GetCertificateRequestByComunityId($idComunidad);
        return (count($resultado) > 0);
    }

    /** Valida si una comunidad tiene el certificado aprobado */
    private function ValidateCertificateIsApproved($idComunidad)
    {
        $validationResult = false;

        $resultado = $this->GetCertificateRequestByComunityId($idComunidad);

        //  Comprobamos si el certificado está aprobado por el técnico
        if(count($resultado) > 0)
            $validationResult = boolval($resultado[0]['aprobado']);

        return $validationResult;
    }

    /**
     * Valida que la documentación de una comunidad haya sido aprobada
     */
    public function ValidarDocumentacionAprobadaComunidad($idComunidad)
    {

        $iRequerimientosObligatorios = 0;   //  Variable para controlar los requerimientos obligatorios aprobados
        $iRequerimientosNoObligatorios = 0;
        $validacionCorrecta = false;

        //  Inicializamos el controller de comunidad para recuperar sus requerimientos de certificado digital
        $this->InitController('Comunidad');
        
        //  Recuperamos los requerimientos de la comunidad en materia de certificado digital
        $requerimientosComunidad = $this->ComunidadController->getDocumentacionCertificadoDigitalByComunidadId($idComunidad);
        $requerimientosComunidad = $requerimientosComunidad['documentacioncertificado'];
        
        //  Verificamos que todos los documentos tengan estado aprobado
        foreach($requerimientosComunidad as $requerimiento)
        {
            if($requerimiento['orden'] > 4)
                break;

            if($requerimiento['idestado'] == 6) // El estado 6 es el de aprobado
                $iRequerimientosObligatorios++;
        }

        //  Comprobación de documentos opcionales
        //  Al menos 1 debe estar subido y aprobado por el técnico de certificado digital
        if($requerimientosComunidad[4]['idestado'] == 6)
            $iRequerimientosNoObligatorios++;

        if($requerimientosComunidad[5]['idestado'] == 6)
            $iRequerimientosNoObligatorios++;

        //  Si se han aprobado los 4 obligatorios y además 1 o más de los opcionales,
        //  la validación se da por correcta
        if($iRequerimientosObligatorios == 4 && $iRequerimientosNoObligatorios >= 1){
            $validacionCorrecta = true;
            //  Marcamos la solicitud como aprobada
            $this->CertificadodigitalModel->AprobarSolicitudCertificado($idComunidad);
            //  Enviamos el e-mail al administrador avisando que su solicitud está siendo procesada
            $this->SendEmailToAdministradorSolicitudCertificadoAprobada($idComunidad);
            //  Creamos y solicitamos el certificado a Uanataca
            $this->RequestCertificateToUanataca($idComunidad);
        }

        return ($validacionCorrecta ? 'ok' : 'ko');

    }

    /////////////////////////////////////////////////////////////////////////////
    ///                       E-MAILS DE NOTIFICACIÓN
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Envía un e-mail a todos los técnicos de revisión de certificado digital avisando que se ha solicitado un nuevo certificado para una comunidad con los datos de comunidad, fecha y representante legal
     * @param int $idComunidad ID comunidad
     * @param int $idRepresentanteLegal ID Representante legal
     */
    private function SendEmailCertificadoComunidadSolicitado($idComunidad, $idRepresentanteLegal)
    {
        $this->InitController('Representantelegal');
        $this->InitController('Usuario');

        //  Recuperamos la comunidad para obtener sus datos
        $comunidad = $this->ComunidadController->Get($idComunidad);
        $comunidad = $comunidad['Comunidad'][0];
        $nombreComunidad = $comunidad['nombre'];
        $codigoComunidad = $comunidad['codigo'];
        $fechaSolicitud = HelperController::DateNow();
        $representanteLegal = $this->RepresentantelegalController->Get($idRepresentanteLegal);
        $representanteLegal = $representanteLegal['Representantelegal'][0];

        //  Nombre del representante legal
        $representanteLegalNombre = $representanteLegal['nombre'] . ' ' . $representanteLegal['apellido'] . ' ' . $representanteLegal['apellido2'];

        $body = $this->GetTemplateEmail('certificadodigital/comunidad_solicitud_certificado');

        $body = str_replace('[@codigo_comunidad@]', $codigoComunidad, $body);
        $body = str_replace('[@comunidad@]', $nombreComunidad, $body);
        $body = str_replace('[@administrador@]', $this->getLoggedUserName(), $body);
        $body = str_replace('[@representante_legal@]', $representanteLegalNombre, $body);
        $body = str_replace('[@fecha@]', $fechaSolicitud, $body);
        
        //  Recuperamos todos los posibles técnicos de rao que haya para enviar el e-mail
        //  avisando que un administrador ha solicitado el certificado digital para una comunidad
        $tecnicosRao = $this->UsuarioController->ListTecnicosRAO();
        $tecnicosRao = $tecnicosRao['Usuario'];
        for($xTecnico = 0; $xTecnico < count($tecnicosRao); $xTecnico++)
        {
            $emailTecnico = $tecnicosRao[$xTecnico]['email'];
            //DEBUG:
            //$emailTecnico = 'info@fincatech.es';
            $this->SendEmail($emailTecnico, 'Fincatech','Nueva solicitud certificado digital comunidad', $body, true);
        }        
        //  Enviamos el e-mail también al sudo del sistema
        //DEBUG:
        $adminEmail = ADMINMAIL;
        // $adminEmail = 'oscar.livin@gmail.com';
        $this->SendEmail($adminEmail, 'Fincatech','Nueva solicitud certificado digital comunidad', $body, true);
    }

    /**
     * Envía un e-mail al Administrador de la comunidad avisando que la documentación ha sido aprobada y se va a proceder a solicitar el certificado digital a la entidad certificadora correspondiente
     * @param int $idComunidad ID de la comunidad
     */
    private function SendEmailToAdministradorSolicitudCertificadoAprobada($idComunidad)
    {
        $fechaSolicitud = HelperController::DateNow();
        $datosSolicitud = $this->CertificadodigitalModel->InfoSolicitud($idComunidad);
        if(count($datosSolicitud))
        {
            $datosSolicitud = $datosSolicitud[0];
            $emailAdministrador = $datosSolicitud['emailadministrador'];
            $body = $this->GetTemplateEmail('certificadodigital/solicitud_certificado_aprobado');
            $body = str_replace('[@codigo_comunidad@]', $datosSolicitud['codigocomunidad'], $body);
            $body = str_replace('[@comunidad@]', $datosSolicitud['comunidad'], $body);
            $body = str_replace('[@administrador@]', $datosSolicitud['administrador'], $body);
            $body = str_replace('[@fecha@]', $fechaSolicitud, $body);  

            //DEBUG: 
            // $emailAdministrador = 'oscar.livin@gmail.com';    

            //  Enviamos el e-mail al administrador
            $this->SendEmail( $emailAdministrador, 'Fincatech','Solicitud certificado digital comunidad ['.$datosSolicitud['comunidad'].'] aprobada', $body, true);

        }

    }

    /** TODO: Eliminar tras el debug correspondiente */
    public function Test()
    {
        $this->SendEmailToAdministradorSolicitudCertificadoAprobada(143);
        return true;
    }
}