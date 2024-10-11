<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel

use Exception;
use Fincatech\Model\DocumentalModel;
use Fincatech\Model\ComunidadModel;
use Fincatech\Controller\FrontController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\EmpresaController;

use Fincatech\Controller\RequerimientoController;

use Happysoftware\Controller\HelperController;
use Happysoftware\Controller\Traits;
use PHPUnit\TextUI\Help;

class DocumentalController extends FrontController{

    public $AutorizadoController, $EmpresaController, $HistoricoController, $MensajeController, $RequerimientoController, $UsuarioController;
    public $ComunidadModel, $DocumentalModel;
    public $CertificadoRequerimientoModel;

    public function __construct($params = null)
    {
        parent::__construct();
        $this->InitModel('Documental', $params);
        $this->InitModel('CertificadoRequerimiento', $params);

        //  Inicializamos el controller de requerimientos
        $this->InitController('Requerimiento', $params);

    }

    public function Insert($data)
    {

    }

    /** Genera en bbdd el requerimiento de cámara de seguridad para una comunidad */
    public function createRequerimientoCamara($idcomunidad, $estado)
    {

        $datos = [];

        $datos['titulo'] = 'Registro de actividades de tratamiento cámaras de seguridad';
        $datos['observaciones'] = '';
        $datos['idcomunidad'] = $idcomunidad;
        $datos['idrequerimiento'] = '-1';

        $this->InitModel('Comunidad');

        //  Guardamos que tiene cámara de seguridad la comunidad
            $datosUpdate = [];
            $datosUpdate['camarasseguridad'] = $estado;
            $this->ComunidadModel->Update(strtolower($this->ComunidadModel->getEntidad()), $datosUpdate, $idcomunidad);

        //  Comprobamos si la comunidad ya tiene este documento subido
        if($this->DocumentalModel->compruebaRequerimientoCamara($idcomunidad) == false && $estado == '1')
        {
            return HelperController::successResponse($this->DocumentalModel->createRequerimientoRGPD('camarasseguridad', '-1', $datos));
        }else{
            return HelperController::successResponse('ok');
        }

        
    }

    /** Sube un requerimiento al tipo que corresponda */
    public function uploadRequerimiento($destino, $datos)
    {

            $relaciones = $this->DocumentalModel->getRelacionesTabla();

            $tablaDestino = $relaciones[ $datos['entidad'] ]['tabla'];
            $campoDestino = $relaciones[ $datos['entidad'] ]['campo'];

            $idrequerimiento = $datos['idrequerimiento'];
            $tablaDestino = $relaciones[ $datos['entidad'] ]['tabla'];
            $idkey = $relaciones[ $datos['entidad'] ]['campo'];
            $id = $datos[ $relaciones[ $datos['entidad'] ]['campo'] ];

        //  FIXME: Se hardcodea si es un fichero de operatoria cuyo id es 1
        //          Hay que modificar todo el gestor documental para hacerlo más operativo
            if($idrequerimiento == 1)
            {
                $id .= " and idcomunidad=". $datos['idcomunidad'];
            }

        //  Subimos el fichero a la plataforma
            $idFichero = $this->uploadFile($datos['fichero']['nombre'], $datos['fichero']['base64']);

            if($this->DocumentalModel->existeRequerimiento($idrequerimiento, $tablaDestino, $idkey, $id))
            {
                $resultado = $this->ProcessUpdateRequerimiento($idrequerimiento, $datos['idrelacionrequerimiento'], $idFichero, $tablaDestino);
            }else{
                $resultado = $this->ProcessRequerimiento($destino, $idFichero, $datos);
            }

            //  Si es un requerimiento de documentación básica comprobamos si están los 3 para enviarlos
            if($tablaDestino === 'comunidadrequerimiento')
            {
                $this->comprobarDocumentacionComunidad($datos['idcomunidad']);
            }

            return HelperController::successResponse($resultado, 200);

    }

    /**
     * Procesa la actualización del requerimiento
     */
    private function ProcessUpdateRequerimiento($idRequerimiento, $idRelacionRequerimiento, $idFichero, $tablaDestino)
    {
        $resultado = null;
        $tieneFechaCaducidad = false;
        $requerimiento = $this->RequerimientoController->Get($idRequerimiento);
        $tieneFechaCaducidad = $requerimiento['Requerimiento'][0]['caduca'];
        $fechaCaducidad = $tieneFechaCaducidad ? '9999-12-31' : null;
        return $this->DocumentalModel->updateRequerimiento( $idRelacionRequerimiento, $idFichero, $tablaDestino, $fechaCaducidad );
    }


    /**
     * Procesa el requerimiento que está subiendo el usuario
    */
    private function ProcessRequerimiento($destinoRequerimiento, $idFichero, $datos)
    {

        $resultado = null;
        $tieneFechaCaducidad = false;

        //  Hay que recuperar la información del requerimiento para comprobar
        //  si tiene fecha caducidad, y si no la tiene poner 99-99-9999
        $requerimiento = $this->RequerimientoController->Get($datos['idrequerimiento']);
        $tieneFechaCaducidad = $requerimiento['Requerimiento'][0]['caduca'];

        switch($destinoRequerimiento){
            case 'certificado':
                $this->CertificadoRequerimientoModel
                    ->SetIdUsuario($this->getLoggedUserId() )
                    ->SetIdComunidad( $datos['idcomunidad'] )
                    ->SetIdRequerimiento( $datos['idrequerimiento'] )
                    ->SetFechaSubida( date('Y-m-d H:i:s') )
                    ->SetFechaCaducidad( ($tieneFechaCaducidad ? '9999-12-31' : '') )
                    ->SetIdFichero( $idFichero )
                    ->SetIdEstado( 3 ) // Por defecto se queda en pendiente de verificación
                    ->SetEstado( '' )
                    ->SetObservaciones( '' )
                    ->SetUserCreate( $this->getLoggedUserId() );
                $resultado = $this->CertificadoRequerimientoModel->Insert();
                break;
            default:
                $resultado = $this->DocumentalModel->createRequerimiento( $idFichero, $datos );
                break;
        }

        return $resultado;
        
    }

    /**
     * Comprueba si la documentación CAE para una comunidad está subida, y, de ser así, la envía por 
     * correo electrónico certificado a través de Mensatek
     * @param int $idComunidad ID de la comunidad a comprobar
     * @param int $idEmpresa (optional). ID De la empresa a validar. Defaults: Null
     * @param bool $_enviarEmail (optional). Boolean que indica si se debe enviar el e-mail certificado. Defaults: True
     * @return int Número de envíos realizados
     */
    public function comprobarDocumentacionComunidad($idComunidad, $idEmpresa = null, $_enviarEmail = true)
    {
        try{
        
            $bodyEmail = 'Estimado Contratista,<br><br>

            En virtud de lo establecido en el <strong>Real decreto 171/2004</strong> de 30 de enero por el que se desarrolla el artículo 24 de la ley 31/1995 del 8 de Noviembre de prevención de riesgos laborales y su reforma, la ley 54/2003 en materia de coordinación de actividades empresariales, cuando en un mismo centro de trabajo se desarrollen actividades trabajadores/as de 2 o más empresas, éstas deberán cooperar en la aplicación de la normativa de prevención de riesgos laborales.<br><br>

            Es por ello que ponemos a su disposición como documentos adjuntos a esta notificación la evaluación de riesgos, la planificación de las actividad preventiva y medidas de emergencia de la comunidad de propietarios del asunto, con objeto de que pueda llevar a cabo la coordinación de actividades empresariales con la/las empresas que acceden a dicha comunidad de propietarios, las cuales puede conocer y acceder a su respectiva información en materia de prevención de riesgos laborales accediendo a la plataforma <a href="https://app.fincatech.es" target="_blank">app.fincatech.es</a>  , en la pestaña de EMPRESAS CONCURRENTES . En el supuesto que dicha información no esté disponible, deberá contactar con dichas empresas para que le aporte la documentación necesaria en esta materia.  Deberá tener en cuenta la información proporcionada por las empresas concurrentes con objeto poder incorporar la misma a su plan de prevención y establecer las medidas adecuadas para prevenir dichos riesgos, así como informar a sus trabajadores de los riesgos del centro de trabajo proporcionados por el titular y de los riesgos derivados de la concurrencia.<br><br>

            Por otro lado, le recordamos que deberá informar sobre los riesgos específicos que puede generar su actividad en el centro de trabajo y que pueden afectar a las empresas concurrentes.<br><br> 

            En el caso de que su actividad genere algún peligro para otras empresas concurrentes, deberá comunicar dicho peligro a las mismas, así como, en el supuesto de producirse algún accidente de trabajo o situación de peligro deberá comunicarlo de manera fehaciente tanto a la propia Comunidad como a las empresas concurrentes y trasladar dicha situación a los empleados que accedan a la comunidad de propietarios.<br><br>

            En todo caso, se compromete, bajo su responsabilidad, a cumplir con lo determinado por la Ley 31/1995, de 8 de noviembre, de Prevención de Riesgos Laborales quedando terminantemente prohibido que acceda a la Comunidad trabajador alguno que no reúna los requisitos establecidos en dicha normativa, así como, cualquier otra normativa aplicable y que rige la contratación de trabajadores por cuenta ajena.<br><br>
            
            Por otro lado, una vez accedido a la plataforma, dispone en la parte superior de un videotutorial que le guiará sobre los pasos a realizar.<br><br>
            
            El acceso a la plataforma se realiza con el mismo email donde ha recibido este correo y su clave de acceso ha sido remitida previamente a ese mismo email. En el supuesto de no disponer o haber olvidado la clave, en la misma zona de iniciar sesión, tiene la opción de recuperar la contraseña.';

            $enviarEmail = true;
            $this->InitController('comunidad');
            $ComunidadController = new \Fincatech\Controller\ComunidadController();

            $documentosComunidad = $ComunidadController->GetDocumentacionComunidad($idComunidad);

            //  Recorremos el array que devuelve para ver si alguno de ellos está vacío lo que indica que aún no están todos
            for($iDoc = 0; $iDoc < count($documentosComunidad); $iDoc++)
            {
                if($documentosComunidad[$iDoc]['idrelacion'] == '' || $documentosComunidad[$iDoc]['idrelacion'] == 'null' || is_null($documentosComunidad[$iDoc]['idrelacion'])){
                    $enviarEmail = false;
                    break;
                }
            }

            //  Si no tiene todos los documentos nos salimos
                if(!$enviarEmail)
                    return;

            //  Si se ha de enviar el e-mail preparamos la información para enviarla a los e-mails correspondientes
                $ficherosAdjuntos = [];
                for($iDoc = 0; $iDoc < count($documentosComunidad); $iDoc++)
                {
                    $fichero = [];
                    $fichero['nombre'] = $documentosComunidad[$iDoc]['storageficherorequerimiento'];
                    $fichero['ubicacion'] = $documentosComunidad[$iDoc]['ubicacionficherorequerimiento'] . $documentosComunidad[$iDoc]['storageficherorequerimiento'];
                    $ficherosAdjuntos[] = $fichero;
                }

            //  Recuperamos todos los proveedores que tenga asignados la comunidad y recuperamos los e-mail de cada uno de ellos
                $destinatarios = [];

                if(!is_null($idEmpresa))
                {
                    //  Recuperamos la información de la empresa 
                    $this->InitController('Empresa');
                    $empresas = $this->EmpresaController->Get($idEmpresa)['Empresa'];
                    $destinatario = [];

                    //ORR: Se cambia el nombre de la persona de contacto por el nombre de la empresa
                    //Fecha: 20/02/2023
                    
                    // $destinatario['nombre'] = substr($empresas[0]['personacontacto'], 0, 20);
                    $destinatario['nombre'] = substr($empresas[0]['razonsocial'], 0, 20);
                    if(strlen($destinatario['nombre']) < 4)
                    {
                        $destinatario['nombre'] = str_pad($destinatario['nombre'],5, '_',STR_PAD_RIGHT);
                    }
                    $destinatario['email'] = $empresas[0]['email'];
                    $destinatarios[] = $destinatario;
                }else{
                    $empresas = $ComunidadController->ComunidadModel->GetEmpresasByComunidadId($idComunidad);
                }

                if(count($empresas) === 0)
                {
                    $enviarEmail = false;
                }else{

                    for($iEmpresa = 0; $iEmpresa < count($empresas); $iEmpresa++)
                    {
                        $destinatario = [];
                        $destinatario['nombre'] = substr($empresas[$iEmpresa]['razonsocial'], 0, 20);
                        if(strlen($destinatario['nombre']) < 4)
                        {
                            $destinatario['nombre'] = str_pad($empresas[$iEmpresa]['razonsocial'], 5, '_',STR_PAD_RIGHT);
                        }                    
                        $destinatario['email'] = $empresas[$iEmpresa]['email'];
                        $destinatarios[] = $destinatario;
                    }

                }

            //  Recuperamos el nombre de la comunidad
                $comunidad = $ComunidadController->Get($idComunidad, false);

            //  Comprobamos que tenga acceso a la comunidad
                if(isset($comunidad['error']))
                    return 0;

                $nombreComunidad = $comunidad['Comunidad'][0]['nombre'];

                if($enviarEmail)
                {
                    //  Componemos el cuerpo del mensaje
                    $body = 'Estimado Contratista,  ' .$nombreComunidad.' dispone de todos los documentos obligatorios en materia de CAE disponibles y que son enviados en este e-mail certificado.';
                    if($_enviarEmail)
                    {
                        $this->procesarEmailCertificado($idComunidad,$empresas, $this->SendEmailCertificadoAdjuntos('Documentos CAE C.P. ' .$nombreComunidad, $destinatarios, $bodyEmail, $ficherosAdjuntos));
                        return count($destinatarios);
                    }else{
                        return count($destinatarios);
                    }
                }
            }catch(\Exception $ex)
            {
                die('Excepción: ' . $ex->getMessage());
            }
    }

    private function procesarEmailCertificado($idComunidad, $empresas, $respuestaXML)
    {

        /* Ejemplo de respuesta
          <?xml version="1.0"?>
            <result>
                <Res>10</Res>
                <Error>Falta par&#xE1;metro obligatorio</Error>
                <Destinatarios>
                <item0>
                <Nombre>Pedro P&#xE9;rez</Nombre>
                <Email>destinatario@eldominio.com</Email>
                <IDMENSAJE>108366478</IDMENSAJE>
                <Variable_1>Variable para personalizar asunto y mensaje por destinatario</Variable_1>
                </item0>
                </Destinatarios>
                <Cred>12000</Cred>
                <Enviados>2</Enviados>
                <NoEnviados>0</NoEnviados>
                <Duplicados>0</Duplicados>
                <CreditosUsados>18</CreditosUsados>
            </result>
         */
        // if(strpos($respuestaXML, 'Res:') >= 0)
        // {
        //     // print_r($empresas);
        //     $this->WriteToLog('emailcertificado', 'DocumentalController -> procesarEmailCertificado', $respuestaXML . ' - Error');            
        //     // echo 'idcomunidad: ' . $idComunidad;
        //     return;

        // }


        //  Ha ocurrido algún tipo de error así que salimos por seguridad pero habrá que determinar
        if($respuestaXML == '-1' || strpos($respuestaXML, 'xml') < 0)
        {
            $dEmpresas = implode(',', $empresas);
            $this->WriteToLog('emailcertificado', 'DocumentalController -> procesarEmailCertificado', $respuestaXML . ' - Error de autenticación');            
            //            echo 'respuesta: ' . $respuestaXML;
            return;
        }

        $xml = simplexml_load_string($respuestaXML);
        $objJsonDocument = json_encode($xml);
        $arrOutput = json_decode($objJsonDocument, TRUE);
        
        //  Logueamos la respuesta de mensatek
        $this->WriteToLog('emailcertificado', 'DocumentalController -> procesarEmailCertificado', $respuestaXML);

        libxml_use_internal_errors(true);

        if ($xml === false) {
            // oh no
            $errors = libxml_get_errors();
            // do something with them
            print_r($errors);
            // really you'll want to loop over them and handle them as necessary for your needs
            return;
        }

        if(!is_array($arrOutput))
        {
            return;
        }

        //  Si la respuesta ha sido satisfactoria, procesamos
        if(intval($arrOutput['Res']) >= 1)
        {

            $this->InitController('Mensaje');

        //  Hay que recorrer cada destinatario para obtener el ID de mensaje y el fichero en formato PDF para almacenarlo en el sistema
            for($x = 0; $x < count($arrOutput['Destinatarios']); $x++)
            {

                    $nombre = $arrOutput['Destinatarios']['item'.$x]['NOMBRE'];
                    $email =  $arrOutput['Destinatarios']['item'.$x]['EMAIL'];
                    $idMensaje =  $arrOutput['Destinatarios']['item'.$x]['IDMENSAJE'];
                
                //  Recuperamos el ID de la empresa desde el array de empresas
                    $idEmpresa = -1;
                    for($y = 0; $y < count($empresas); $y++)
                    {
                        if(strtolower($empresas[$y]['email']) == strtolower($email))
                        {
                            $idEmpresa = $empresas[$y]['id'];
                        }
                    }   

                    if(floatval($idMensaje) > 0)
                    {
                        //  Guardamos el ID del mensaje enviado proporcionado por el proveedor Mensatek
                        $this->MensajeController->SaveCertificado($idMensaje, $idComunidad, $idEmpresa);
                    }


            }

        }

    }

    /** Sube un requerimiento de RGPD a la tabla que corresponda */
    public function uploadRequerimientoRGPD($destino, $datos)
    {

            $idFichero = 'null';

        //  Subimos el fichero a la plataforma
            if(isset( $datos['fichero']['nombre'] ))
            {
                $idFichero = $this->uploadFile($datos['fichero']['nombre'], $datos['fichero']['base64']);
            }


        //  TODO: Validar que no exista el requeririmiento en bbdd
        //  para eso analizamos el cuerpo de la petición para ver si viene informado el ID del requerimiento previamente subido

        //  Destino, idFichero, datos
            return HelperController::successResponse($this->DocumentalModel->createRequerimientoRGPD( $destino, $idFichero, $datos) );
    }

    public function getRepositorio()
    {
        return parent::GetHelperModel()->getRepositorio();
    }

    /** Actualiza el estado de un requerimiento */
    // public function actualizarEstadoRequerimiento($datos)
    // {
    //     $this->DocumentalModel->uploadRequerimiento($datos);
    // }

    /** Devuelve un listado de requerimientos según el tipo y la comunidad */
    public function ListRequerimientoRGPD($destino, $idComunidad)
    {
        return HelperController::successResponse( $this->DocumentalModel->ListRequerimientoRGPD($destino, $idComunidad) );
    }

    /** Comprueba la operatoria entre empresa y comunidad */
    public function CheckOperatoriaEmpresaComunidad($idEmpresa, $idComunidad)
    {
        return HelperController::successResponse( $this->DocumentalModel->CheckOperatoriaEmpresaComunidad( $idEmpresa, $idComunidad) );
    }  

    /** Comprueba si un administrador tiene subido el contrato con la comunidad */
    public function CheckContratoAdministradorComunidad($idComunidad, $idAdministrador)
    {
        return HelperController::successResponse( $this->DocumentalModel->CheckContratoAdministradorComunidad( $idComunidad, $idAdministrador) );
    }

    public function DeleteRequerimiento($tiporequerimiento, $id)
    {
        return HelperController::successResponse($this->DocumentalModel->DeleteRequerimiento(strtolower($tiporequerimiento), $id));
    }

    /** Refleja la descarga de un fichero en el sistema */
    public function ReflejarDescargaFichero($datos)
    {
        if(isset($datos['idcomunidad']))
            $this->DocumentalModel->setIdComunidad($datos['idcomunidad']);

        if(isset($datos['idempresa'])){
            $this->DocumentalModel->setIdEmpresa($datos['idempresa']);
        }else{
            $this->DocumentalModel->setIdEmpresa( null );
        }

        if(isset($datos['idusuario']))
            $this->DocumentalModel->setIdUsuario($datos['idusuario']);

        if(isset($datos['idfichero']))
            $this->DocumentalModel->setIdFichero($datos['idfichero']);

       return HelperController::successResponse( $this->DocumentalModel->SaveDescargaFichero() );

    }

    public function ListadoRequerimientos($datos)
    {
        //  Devolver en el data la información de los requerimientos

        //  Dentro del data hay que meter un nuevo valor que sea:
        //      DescargaPrevia  : Indica si es un tipo de requerimiento que tiene descarga previa
        //      SujetoRevisión  : Indica si es un tipo de requerimiento que tiene revisión por parte de un técnico
        //      Caducidad       : Indica si es un tipo de requerimiento que es susceptible de caducidad
        //  

        $descargaPrevia = false;
        $sujetoRevision = false;
        $caducidad = false;

        $entidadDestino = $datos['entidaddestino'];
        $idComunidad = $datos['idcomunidad'];
        $idEmpresa = $datos['idempresa'];
        $idEmpleado = $datos['idempleado'];

        //  Comprobamos si es de CAE para poder establecer que está sujeto a revisión por parte de un técnico
        if(intval($datos['tiporequerimiento']) >= 4 && intval($datos['tiporequerimiento']) <= 7)
        {
            $sujetoRevision = true;
            //  Comprobamos si el usuario es de tipo Autóno o empresa
            if($this->DocumentalModel->GetTipoContratista($idEmpresa) == 1)
            {
                //  Empleado
                $datos['tiporequerimiento'] = ($datos['tiporequerimiento'] == 4 ? 4 : $datos['tiporequerimiento']);
            }else{
                //  Autónomo
                $datos['tiporequerimiento'] = ($datos['tiporequerimiento'] == 4 ? 7 : $datos['tiporequerimiento']);
            }
        }

        // var_dump($datos);
        // die();
            $data = [];
            $filter = [];
            
            $filter['filterfield'] = 'idrequerimientotipo';
            $filter['filtervalue'] = $datos['tiporequerimiento'];
            
        //  Primero recuperamos los requerimientos según el tipo de Requerimiento
        //  Estos requerimientos no deben ser los asociados a una entidad sino los generales pero acotados por ID del tipo
            $data = $this->DocumentalModel->List($filter, false);

        //  Una vez recuperados, debemos recuperar aquellos requerimientos que sí vayan asociados a la entidad: Comunidad, Empresa, DPD, ...
            $fieldToFilter = null;
            $fieldToFilterValue = null;

            switch($datos['entidaddestino'])
            {
                case 'empresa':
                    $fieldToFilter = 'idempresa';
                    $fieldToFilterValue = $idEmpresa;
                    break;
                case 'comunidad':
                    $fieldToFilter = 'idcomunidad';
                    $fieldToFilterValue = $idComunidad;
                    break;   
                case 'empleado':
                    $fieldToFilter = 'idempleado';
                    $fieldToFilterValue = $idEmpleado;
                    break;   
                case 'certificado':
                    $fieldToFilter = 'idcomunidad';
                    $fieldToFilterValue = $idComunidad;
                    break;                                                            
            }

            // TODO: Requerimientos adicionales
            if(!is_null($fieldToFilter) && !is_null($fieldToFilterValue))
            {

                $filter['filterfield'] = $fieldToFilter;
                $filter['filtervalue'] = $fieldToFilterValue;

                $requerimientosAdicionales = [];
                $requerimientosAdicionales = $this->DocumentalModel->List($filter, false);
                if(count($requerimientosAdicionales) > 0)
                {
                    //TODO:   Mergeamos los 2 arrays
                    // $data['Requerimiento'] = array_merge($requerimientosAdicionales['Requerimiento'], $data['Requerimiento']);                    
                }

            }
            
            //  Entidad destino puede tener los siguientes valores que es de donde se van a recuperar los documentos asociados
            //  salvo que sea información que entonces no se recupera ninguna asociación existente
            
            //      ENTIDAD_GENERAL: 'informacion', //  Documentos de sólo descarga
            //      ENTIDAD_EMPRESA: 'empresa',
            //      ENTIDAD_COMUNIDAD: 'comunidad',
            //      ENTIDAD_EMPLEADO: 'empleado',
            $this->InitController('Historico');

            for($x = 0; $x < count($data['Requerimiento']); $x++)
            {

                $idRequerimiento = $data['Requerimiento'][$x]['id'];
                $data['Requerimiento'][$x]['documentoasociado'] = [];
                $data['Requerimiento'][$x]['documentoasociado']['id'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['idcomunidad'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['idrequerimiento'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['idempresa'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['idestado'] = '1';
                $data['Requerimiento'][$x]['documentoasociado']['idfichero'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['fechacaducidad'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['created'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['updated'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['usercreate'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['nombre'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['nombrestorage'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['ubicacion'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['estado'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['fechasubida'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['fechaactualizacion'] = null;
                $data['Requerimiento'][$x]['documentoasociado']['usuariocreacion'] = null;
                

                $documentosRequerimiento = $this->DocumentalModel->ListRequerimientosByEntity($entidadDestino, $idRequerimiento, $fieldToFilter, $fieldToFilterValue);
                if(count($documentosRequerimiento) > 0)
                {
                    $data['Requerimiento'][$x]['documentoasociado'] = $documentosRequerimiento[0]; 
                    $data['Requerimiento'][$x]['historico'] = $this->HistoricoController->TieneHistorico($documentosRequerimiento[0]['id'], $entidadDestino.'requerimiento');
                }else{
                    $data['Requerimiento'][$x]['historico'] = false;
                }

            }

        //  Una vez recuperados los requerimientos hay que comprobar si es para:
        //      Empleado
        //      Empresa
        //      Comunidad
        //      RGPD Comunidad
        //
        //  El data contiene la información del requerimiento
        //  Por cada uno de ellos hay que recuperar el fichero subido
        //  Ej: $data['requerimiento'][$x]['ficherosubido'] = recuperar info si la hay
        //  Fichero Subido: ID, Nombre original del fichero, Donde está almacenado, Fecha de caducidad si la tiene,
        //                  Rechazo y observaciones si lo tiene, ID de la relación, ID del fichero subido, Fecha de subida
        //                  Fecha de descarga, Quién lo descargó
        return HelperController::successResponse( $data['Requerimiento'] );
    }

    /** Recupera el listado de documentos pendientes de verificación */
    public function ListDocumentosPendientesVerificacion()
    {
        $listadoDocumentos = [];
        //  Recuperamos los documentos de empresa
        $listadoDocumentos['Requerimiento'] = $this->DocumentalModel->GetDocumentosPendientesVerificacion(4);

        return HelperController::successResponse($listadoDocumentos);

    }
    
    /** Cambia el estado a un requerimiento */
    public function cambiarEstadoRequerimiento($idRequerimiento, $entidadDestino, $estado, $fechaCaducidad, $observaciones)
    {
        $this->DocumentalModel->cambiarEstadoRequerimiento($idRequerimiento, $entidadDestino, $estado, $fechaCaducidad, $observaciones);
        return HelperController::successResponse('ok',200);
        
    }

    /** Devuelve el listado de información de requerimientos de comunidad descargados por una empresa contratista */
    public function ListadoDocumentosComunidadDescargadosEmpresa($idEmpresa, $idComunidad)
    {
        $data = [];
        $idUsuario = $this->getLoggedUserId();
        $data['infodescargas'] = $this->DocumentalModel->GetListadoDocumentosComunidadDescargadosEmpresa($idUsuario, $idEmpresa, $idComunidad);
        return HelperController::successResponse( $data );
    }

    public function GetTipoContratista($idEmpresa)
    {
        return $this->DocumentalModel->GetTipoContratista($idEmpresa);
    }

    /** Devuelve el número total de requerimientos de RGPD para una comunidad */
    public function GetTotalRequerimientosRGPDComunidad( $idAdministrador, $idComunidad, $tieneCamaraSeguridad = false)
    {
        return $this->DocumentalModel->GetTotalRequerimientosRGPD( $idAdministrador, $idComunidad, $tieneCamaraSeguridad );  
    }

    /** Devuelve el total de Requerimientos Pendientes en materia de RGPD para una comunidad */
    public function GetTotalRequerimientosPendientesRGPDComunidad($idComunidad, $idAdministrador, $_camarasSeguridad = false){
        return $this->DocumentalModel->GetRequerimientosPendientesRGPDComunidad($idComunidad, $idAdministrador, $_camarasSeguridad);
    }

    /** Recupera el listado de requerimientos pendientes para un administrador
     * @param string $tipo Admite: cae, rgpd
     */
    public function GetRequerimientosPendientes($tipo)
    {

        $requerimientosPendientes = [];
        $listadoComunidades = [];
        $comunidadesAutorizado = [];
        $requerimientosPendientes['pendientes'] = [];
        $usuarioAutorizado = false;
        
        $this->InitController('Usuario');

        $usuarioId = $this->getLoggedUserId();
        $adminId = $this->UsuarioController->IsAuthorizedUserByAdmin($usuarioId);

        //  Si es un usuario autorizado cogemos el id de su supervisor
        if($adminId !== false)
        {
            $usuarioId = $adminId;
            $usuarioAutorizado = true;
            $this->InitController('Autorizado');
            $comunidadesAutorizado = $this->AutorizadoController->ComunidadesAsignadas($this->getLoggedUserId());
        }


        switch ($tipo)
        {
            case 'cae':
                $requerimientosPendientes['pendientes'] = $this->DocumentalModel->GetRequerimientosPendientesCAE( $usuarioId );
                break;
            case 'cae_empresa':
                $requerimientosPendientes['pendientes'] = $this->DocumentalModel->GetRequerimientosPendientesCAEEmpresa( $usuarioId );
                break;
            case 'rgpd':
                $requerimientosPendientes['pendientes'] = $this->DocumentalModel->GetRequerimientosPendientesRGPD( $usuarioId );
                break;
        }
         //$data['pendientes']
        //  Si es un usuario autorizado tenemos que acotar las comunidades a las que tenga asignadas el usuario autorizado
        if(count($comunidadesAutorizado) && count($requerimientosPendientes['pendientes']) && $usuarioAutorizado)
        {
            for($i =  0; $i<count($comunidadesAutorizado); $i++)
            {
                $idComunidad = $comunidadesAutorizado[$i]['idcomunidad'];
                $filtro = $this->DocumentalModel->filterResults($requerimientosPendientes, 'pendientes', 'idcomunidad', $idComunidad);
                if( !empty($filtro['pendientes']) ){
                    for($z = 0 ; $z < count($filtro['pendientes']); $z++)
                    {
                        $listadoComunidades[] = $filtro['pendientes'][$z];
                    }
                }
            }
            $requerimientosPendientes['pendientes'] = $listadoComunidades;
        }

        return HelperController::successResponse($requerimientosPendientes);
        
    }

    public function ComprobarRequerimientosEmpresaCAE($idEmpresa)
    {

    }

    public function GetEmailCertificadoEmpresaComunidad($idEmpresa, $idComunidad)
    {
        $emailCertificado = $this->DocumentalModel->GetEmailCertificadoEmpresaComunidad($idEmpresa, $idComunidad);  
 
        if(count($emailCertificado) > 0)
        {
            return $emailCertificado[0]['filename'];
        }else{
            return null;
        }
    }

    /**
     * Traslada un requerimiento al historial
     * @param int $idRequerimiento ID del requerimiento
     * @param string $tablaDestino Nombre de la tabla destino
     */
    public function moveRequerimientoToHistorial($idRequerimiento, $tablaDestino)
    {
        $this->DocumentalModel->moveRequerimientoToHistorial($idRequerimiento, $tablaDestino);
    }

    /** Recupera todas las comunidades que tienen requerimientos pendientes en materia de CAE */
    public function GetRequerimientosPendientesCAEGeneral($tipo = 'comunidades')
    {
        $data = [];
        $data['requerimiento'] = $this->DocumentalModel->GetRequerimientosPendientesCAEGeneral($tipo);
        return $data;
    }

    /**
     * Devuelve la asociación entre comunidad y empresa que tenga la cae sin enviar
     */
    public function PendienteEnviarDocumentacionCAE()
    {
        return $this->DocumentalModel->EmailsPendientesCAE(); 
    }

}