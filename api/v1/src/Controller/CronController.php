<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\CronModel;
use Fincatech\Controller\DocumentalController;
use Fincatech\Controller\EmpresaController;
use HappySoftware\Controller\Traits;
use HappySoftware\Controller\HelperController;

class CronController extends FrontController{

    public $CronModel;
    public $DocumentalController;
    public $MensajeController;
    public $EmpresaController;

    //  Tabla de requerimientos de empresa. Se utiliza para marcar un requerimiento a estado pendiente y moverlo al historial
    private $_tablaRequerimientoEmpresa = 'empresarequerimiento';
    //  Tabla de requerimientos de empleados. Se utiliza para marcar un requerimiento a estado pendiente y moverlo al historial
    private $_tablaRequerimientoEmpleado = 'empleadorequerimiento';

    //  Se utiliza para almacenar la tabla en formato html con la información de los requerimientos de empresa caducados
    private $_htmlRequerimientoEmpresa;
    //  Se utiliza para almacenar la tabla en formato html con la información de los requerimientos de empleado de empresa caducado
    private $_htmlRequerimientoEmpleado;

    //  Número de envíos máximo de e-mails de recordatorio de acceder a la plataforma para las empresas
    private $_limiteEnviosRecordatorios = 10;

    //  Texto recordatorio acceso para empresas
    private $_textoRecordatorio = '<p>&nbsp;</p><p style="font-size: 14px; line-height: 140%; font-family: Raleway,sans-serif;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">Hola @empresa, le recordamos que tiene pendiente de acceder a la plataforma según e-mail adjunto en el proceso del alta por parte del administrador.</span></p><p>&nbsp;</p>';

    public function __construct($params = null)
    {
        $this->InitModel('Cron', $params);
    }

    public function ControlCaducidadDocumentosAdministrador()
    {
        //  Recuperamos todos los administradores del sistema

        //  Por cada uno de ellos comprobamos los posibles documentos que tenga caducados

        //  Si tiene documentos caducados, enviamos la información al administrador correspondiente 
        //  para que lo subsane
    }

    /**
     * Comprueba los requerimientos caducados de empresa y envía por e-mail a los proveedores la información
     */
    public function ControlCaducidadDocumentosCAEEmpresas()
    {

        $empresas = $this->CronModel->ListEmpresas();

        //  Recorremos todas las empresas
        for($iEmpresa = 0; $iEmpresa < count($empresas); $iEmpresa++)
        {

            $empresas[$iEmpresa]['requerimientoscaducados'] = [];

            //  Datos de la empresa
            $idEmpresa = $empresas[$iEmpresa]['id'];

            //  Recuperamos los documentos que pueda tener caducados en materia de CAE
            $documentosCAEEmpresaCaducados = $this->CronModel->DocumentosEmpresaCAECaducados($idEmpresa);
            $empresas[$iEmpresa]['requerimientoscaducados']['empresa'] = $documentosCAEEmpresaCaducados;

            //  Recuperamos los documentos de empleados que puedan estar caducados
            $documentosEmpleadoEmpresaCaducados = $this->CronModel->DocumentosEmpleadoEmpresaCaducados($idEmpresa);
            $empresas[$iEmpresa]['requerimientoscaducados']['empleado'] = $documentosEmpleadoEmpresaCaducados;

            //  Comprobamos si la empresa tiene requerimientos caducados
            if( intval($documentosCAEEmpresaCaducados['total']) > 0 ||
                intval($documentosEmpleadoEmpresaCaducados['total']) > 0 ){

                //  Instanciamos el controller de documental para poder mover al histórico
                //  los requerimientos caducados
                $this->InitController('Documental');
                //  Marcamos los requerimientos de empresa como pendientes de subir
                $this->MarcarRequerimientosEmpresaPendiente($documentosCAEEmpresaCaducados);
                //  Marcamos los requerimientos de empleados como pendientes de subir
                $this->MarcarRequerimientosEmpleadosEmpresaPendiente($documentosEmpleadoEmpresaCaducados);                    
                //  Enviamos el e-mail al proveedor para que actualice los documentos
                $this->EnviarEmailProveedorDocumentosCaducados($empresas[$iEmpresa]);
            }

        }

        $datos = [];
        $datos['nombre'] = 'Requerimientos caducados CAE Empresa';
        $datos['resultado'] = 'Ejecutado';
        $datos['fechaejecucion'] = date('Y-m-d');

        $this->CronModel->Create('cron',$datos);
        //  Enviamos el resultado a Cristóbal y a desarrollo
        $this->SendEmail('info@fincatech.es', 'Fincatech','Ejecución Cron [Control caducidad CAE Proveedores]', '<p>Se ha ejecutado el cron de control de caducidad de documentos CAE para empresas',false);
        //$this->SendEmail('ororodeveloper@gmail.com', 'Desarrollo','Ejecución Cron [Control caducidad CAE Proveedores]', '<p>Se ha ejecutado el cron de control de caducidad de documentos CAE para empresas',false);
    }

    /**
     * Genera la tabla html de los requerimientos caducados de empresa
     * @param Array $requerimientos. Requerimientos caducados
     */
    private function GenerarTablaHTMLRequerimientosCaducadosEmpresa($requerimientos)
    {
        $tabla = '';
        if(count($requerimientos) > 0)
        {
        
            $tabla = '<p style="text-align: center;"><strong>REQUERIMIENTOS DE EMPRESA CADUCADOS</strong></p>';
            $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
            $tabla .= '<table style="width: 100%;">';
            $tabla .= '     <thead>
                                <tr>
                                    <th>Requerimiento</th>
                                    <th style="text-align:center;">Fecha caducidad</th>
                                </tr>
                            </thead>
                            <tbody>';

            for($y = 0; $y < count($requerimientos); $y++)
            {
                $tabla .= '<tr>
                                <td><p>'.$requerimientos[$y]['nombrerequerimiento'].'</p></td>
                                <td style="text-align:center;"><p style="text-align:center;">'.date('d-m-Y',strtotime($requerimientos[$y]['fechacaducidad'])).'</p></td>
                            </tr>';
            }

            $tabla .= '</tbody></table>';
        }
        $this->_htmlRequerimientoEmpresa = $tabla;
    }

    /**
     * Genera la tabla de requerimientos caducados de empleados de una empresa
     * @param Array $requerimientos Array con los requerimientos que están caducados
     */
    private function GenerarTablaHTMLRequerimientosCaducadosEmpleadoEmpresa($requerimientos)
    {

        $this->_htmlRequerimientoEmpleado = '';
    
        foreach($requerimientos as $requerimiento)
        {
            if(count($requerimiento['requerimientos']) > 0 )
            {
                //  Creamos la tabla con la información del requerimiento
                //  idempleado, empleado, emailempleado, 
                //  ['requerimiento'] => fechacaducidad, requerimiento
                $this->_htmlRequerimientoEmpleado = '';

       
                    $tabla = '<p style="text-align: center;"><strong>REQUERIMIENTOS DE EMPLEADO CADUCADOS</strong></p>';
                    $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
                    $tabla .= '<table style="width: 100%;">';
                    $tabla .= '     <thead>
                                        <tr>
                                            <th>Trabajador</th>
                                            <th>Requerimiento</th>
                                            <th style="text-align:center;">Fecha caducidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        
                    //  Iteramos sobre los requerimientos de cada empleado
                    for($y = 0; $y < count($requerimiento['requerimientos']); $y++)
                    {
                        $tabla .= '<tr>
                                        <td><p>'.$requerimiento['empleado'].'</p></td>
                                        <td><p>'.$requerimiento['requerimientos'][$y]['requerimiento'].'</p></td>
                                        <td style="text-align:center;"><p style="text-align:center;">'.date('d-m-Y',strtotime($requerimiento['requerimientos'][$y]['fechacaducidad'])).'</p></td>
                                    </tr>';
                    }
        
                    $tabla .= '</tbody></table>';
                    $this->_htmlRequerimientoEmpleado = $tabla;
            }
        }
    }

    /**
     * Envía el e-mail de los requerimientos caducados a los proveedores que se hayan podido detectar
     * @param Array $requerimientos Array con los requerimientos caducados
     */
    private function EnviarEmailProveedorDocumentosCaducados($requerimientos)
    {

        //  Reinicializamos las propiedades de tabla html
        $this->_htmlRequerimientoEmpleado = '';
        $this->_htmlRequerimientoEmpresa = '';

        //  Procesamos la tabla para los requerimientos de empresa caducados
        $this->GenerarTablaHTMLRequerimientosCaducadosEmpresa($requerimientos['requerimientoscaducados']['empresa']['requerimientos']);
        //  Procesamos la tabla para los requerimientos de empleados caducados
        $this->GenerarTablaHTMLRequerimientosCaducadosEmpleadoEmpresa($requerimientos['requerimientoscaducados']['empleado']['requerimientos']);

        $nombreEmpresa = $requerimientos['razonsocial'];
        $email = $requerimientos['email'];

        // $email = 'oscar.livin@gmail.com';

        $personaContacto = (trim($requerimientos['personacontacto']) == '' ? $nombreEmpresa : $requerimientos['personacontacto']);
        
        //  Cargamos la plantilla
        $templateEmail = $this->GetTemplateEmail('req_caducado_empresa');
        //  Parseamos los datos
        $body = str_replace('[@persona_contacto@]', $personaContacto, $templateEmail);
        $body = str_replace('[@tabla_datos_empresa@]', $this->_htmlRequerimientoEmpresa,$body);
        $body = str_replace('[@tabla_datos_empleado@]', $this->_htmlRequerimientoEmpleado,$body);
        $body = str_replace('[@tabla_datos_comunidades@]', '',$body);
        $body = str_replace('[@tabla_datos_pendientes@]', '',$body);

        //  Enviamos el e-mail al proveedor
        $this->SendEmail($email, $nombreEmpresa, 'Documentos CAE Caducados', $body, true);

    }

    /**
     * Marca los requerimientos caducados como pendientes de subir
     * @param Array $documentosEmpleadoEmpresaCaducados Requerimientos de empleado
     */
    private function MarcarRequerimientosEmpleadosEmpresaPendiente($documentosEmpleadoEmpresaCaducados)
    {
        //  Si tiene documentos caducados, enviamos e-mail al proveedor
        if(count($documentosEmpleadoEmpresaCaducados) > 0)
        {
            //  Marcamos los requerimientos de empleados de empresa como pendientes de subir y lo movemos al historial
            foreach($documentosEmpleadoEmpresaCaducados['requerimientos'] as $documentoCaducado)
            {
                for($iDocumento = 0; $iDocumento < count($documentoCaducado['requerimientos']); $iDocumento++)
                {
                    $idRequerimiento = $documentoCaducado['requerimientos'][$iDocumento]['id'];
                    //  Movemos el requerimiento al historial
                    $this->DocumentalController->moveRequerimientoToHistorial($idRequerimiento, 'empleadorequerimiento');                    
                    //  Actualizamos el estado para dejarlo en pendiente
                    $this->CronModel->MarcarRequerimientoPendiente($idRequerimiento, $this->_tablaRequerimientoEmpleado);
                }
            }
        }
    }

    /**
     * Marca los requerimientos caducados como pendientes de subir
     * @param Array $documentosCAEEmpresaCaducados Requerimientos de empresa caducados
     */    
    private function MarcarRequerimientosEmpresaPendiente($documentosCAEEmpresaCaducados)
    {
            //  Si tiene documentos caducados, enviamos e-mail al proveedor
            if(count($documentosCAEEmpresaCaducados['requerimientos']) > 0)
            {
                //  Marcamos los requerimientos de cae empresa como pendientes de subir y lo movemos al historial
                foreach($documentosCAEEmpresaCaducados['requerimientos'] as $documentoCaducado)
                {
                    $idRequerimiento = $documentoCaducado['id'];

                    //  Movemos el requerimiento al historial
                    $this->DocumentalController->moveRequerimientoToHistorial($idRequerimiento, 'empresarequerimiento');
                    //  Actualizamos el estado para dejarlo en pendiente
                    $this->CronModel->MarcarRequerimientoPendiente($idRequerimiento, $this->_tablaRequerimientoEmpresa);
                }
            }
    }

    private function RequerimientoCaducado($requerimiento)
    {
        $resultado = false;
        if($requerimiento['caduca'] == '1')
        {
            $fechaCaducidad = strtotime(date($requerimiento['']));
            $fechaActual = strtotime(date('d-M-Y'));
            if($fechaCaducidad <= $fechaActual)
            {
                $resultado = true;
            }
        }
        return $resultado;
    }


    //============================================================================================
    //                          NORMALIZACIÓN INFORMACIÓN MENSATEK
    //============================================================================================
    public function NormalizarInformesMensatek()
    {
        try{

            // Activar la visualización de errores
            ini_set('display_errors', 1);

            // Reportar todos los errores
            error_reporting(E_ALL);

            //  Establecemos el límite a 0 por si hay muchas peticiones
            set_time_limit(0);
            $emailDestinatarioCronCC = 'oscar.livin@gmail.com';
            $emailDestinatarioCron = 'info@fincatech.es';

            //  Instanciamos el controller
            $this->InitController('Mensaje');

            //  Recuperamos el listado de mensajes que no tienen filename de Mensatek para procesarlos       
            $listadoEmailsPendientesInforme = $this->CronModel->ListMensajesSinInforme();
            $output = '<strong>Total mensajes proceso</strong>: ' . count($listadoEmailsPendientesInforme) . '<br><br>' . PHP_EOL;

            //  Iteramos por cada uno de ellos para solicitar el informe a Mensatek
            foreach($listadoEmailsPendientesInforme as $emailPendiente)
            {
                //  Comprobamos si tiene id de mensaje
                $idMensaje = $emailPendiente['idmensaje'] ;               
                $output .= '- Mensaje con ID: ' . $emailPendiente['id'] . ' -- Fecha de envío: ' . date('d-m-Y', strtotime( $emailPendiente['created'] ) ) . ' -- MensatekID: ' . $idMensaje;
                //  Mensaje para el log del servidor
                $mensaje = '- Mensaje con ID: ' . $emailPendiente['id'] . ' -- Fecha de envío: ' . date('d-m-Y', strtotime( $emailPendiente['created'] ) ) . ' -- MensatekID: ' . $idMensaje;
                //  Nombre del fichero de certificación
                $ficheroCertificacion = $this->getPDFEmailCertificado($idMensaje);

                if(strpos($ficheroCertificacion, 'Res:') === false)
                {
                    // $output .= ' -- Fichero: ' . $ficheroCertificacion . '<br>' . PHP_EOL;
                    // $mensaje .= ' -- Fichero: ' . $ficheroCertificacion . '<br>' . PHP_EOL;
                    $ficheroGenerado = $this->MensajeController->saveFileEmailCertificado($idMensaje, $ficheroCertificacion);
                    //$this->MensajeController->saveFileEmailCertificado($idMensaje, $ficheroCertificacion);  
                    $output .= ' -- Fichero: ' . $ficheroGenerado . '<br>' . PHP_EOL;
                    $mensaje .= ' -- Fichero: ' . $ficheroGenerado . '<br>' . PHP_EOL;

                }else{
                    $output .= ' -- Fichero: No disponible y no descargado -- Respuesta Mensatek: '.$ficheroCertificacion.'<br>' . PHP_EOL;
                    $mensaje .= ' -- Fichero: No disponible y no descargado -- Respuesta Mensatek: '.$ficheroCertificacion.'<br>' . PHP_EOL;
                }
                $this->WriteToLog('cron_mensatek','NormalizacionInformesMensatek','['.date('d-m-Y').']' . PHP_EOL . $mensaje);
            }

            $body = 'Se ha ejecutado el Cron de descarga de informes pendientes de Mensatek<br><br>';
            $body .= $output;

            // $this->SendEmail($emailDestinatarioCron,'Fincatech','Cron Normalizacion Informes Mensatek', $body, false);
            $this->SendEmail($emailDestinatarioCronCC,'Fincatech','Cron Normalizacion Informes Mensatek', $body, false);           
            set_time_limit(180);
        }catch(\Exception $ex){
            //  Escribimos en el log de mensatek
            $this->WriteToLog('cron_mensatek','NormalizacionInformesMensatek','['.date('d-m-Y').']' . PHP_EOL . 'Error: ' . $ex->getMessage());
            set_time_limit(180);
        }

        return $output;
    }

    /**
     * Comprueba aquellas empresas que tienen la CAE completada pero que no se ha enviado e-mail a través de mensatek ni se ha recibido certificación
     */
    public function PendientesEmision()
    {
        try{
            //  Incrementamos el límite
            set_time_limit(3600);

            $result = 'No hay documentación cae pendiente de enviar';
            //  Instanciamos el controller de certificados digitales
            $this->InitController('Documental');
            //  Recuperamos los datos relativos a aquellas empresas + comunidad que no se ha enviado el e-mail
            $emailsPendientes = $this->DocumentalController->PendienteEnviarDocumentacionCAE();

            //  Iteramos sobre los datos recuperados para poder enviar los e-mails correspondientes
            if(!is_null($emailsPendientes))
            {
                $iTotal = 0;
                for($i = 0; $i < count($emailsPendientes); $i++)
                {
                    $idComunidad = $emailsPendientes[$i]['idcomunidad'];
                    $idEmpresa = $emailsPendientes[$i]['idempresa'];
                    $iTotal += $this->DocumentalController->comprobarDocumentacionComunidad($idComunidad, $idEmpresa);
                }
                $result = 'Se han procesado ' . $iTotal . ' registros';
            }
        }catch(\Exception $ex){
            die('Ha ocurrido la siguiente excepcion: ' . $ex->getMessage());
        }

        return HelperController::successResponse($result,200);        

    }

    //============================================================================================
    //                         RECORDATORIO DE REGISTRO DE EMPRESAS
    //============================================================================================
    /**
     * Envío de e-mails de recordatorio de acceso a la plataforma para empresas
     * @return string Mensaje de ejecución del cron con el resultado obtenido
     */
    public function EnvioRecordatorioAccesoEmpresas()
    {

        $numeroEnvios = 0;
        //  Recuperamos aquellas empresas que no han accedido nunca al sistema
        $empresasPendienteAcceso = $this->CronModel->ListEmpresasPendientesAcceso();

        if(count($empresasPendienteAcceso) > 0)
        {

            //  Inicializamos el controller de mensaje
            $this->Initcontroller('Mensaje');
            //  Por cada una de las empresas recuperamos el mensaje enviado para comprobar si ha alcanzado el límite de envíos
            //  En caso de haber alcanzado el límite de recordatorios, enviamos e-mail al administrador para avisarle de dicha circunstancia
            for($iEmpresa = 0; $iEmpresa < count($empresasPendienteAcceso); $iEmpresa++)
            {
                //  Recuperamos la empresa del array
                $empresa = $empresasPendienteAcceso[$iEmpresa];

                //  Recuperamos el mensaje enviado durante el registro de la empresa
                $emailRegistroId = $this->MensajeController->GetEmailRegistroIdByEmail( $empresa['emailempresa'] );

                //  Comprobamos si hay mensaje enviado
                if( $emailRegistroId > 0 )
                {
                    //  Comprobamos si el número de envío es inferior al máximo permitido
                    if( $numeroEnvios < $this->_limiteEnviosRecordatorios) 
                    {
                        $mensajeRecordatorio = str_replace('@empresa', $empresa['razonsocial'], $this->_textoRecordatorio);
                        //  Si aún no ha alcanzado el límite enviamos el recordatorio al usuario
                        $this->MensajeController->ResendMessage($emailRegistroId, true, 'Recordatorio Acceso Fincatech', $mensajeRecordatorio);
                        $numeroEnvios++;
                    }else{
                        //  Enviamos e-mail al administrador para avisar de dicha circunstancia
                        $this->MensajeController->SendEmailRecordatorioAccesoAdministrador();
                    }
                }
            }

        }else{

        }
        //
        $mensaje = $numeroEnvios;
        return $mensaje;
    }

    /**
     * Cron que se encarga de enviar el e-mail de alta en la plataforma a aquellas empresas dadas de alta que no está registrado en el sistema de e-mails
     */
    public function EnvioAltaPlataformaPendienteEmpresas()
    {
        $iEnvios = 0;
        $empresasEnvio = '';

        try{

            echo 'Cambiando el límite de ejecución en el servidor<br>';
            $timeLimit = ini_set('max_execution_time', '0');
            echo 'Resultado cambio tiempo límite: ' . ($timeLimit == true ? 'Ok' : 'No') . '<br>';
            echo 'Límite de ejecución: ' . ini_get('max_execution_time') . '<br>';
            
            $empresasSinEmailAlta = $this->CronModel->ListEmpresasSinEnvioEmailAlta();

            if(count($empresasSinEmailAlta) > 0)
            {
                //  Iniciamos el controller de empresas
                // $empresaController = new \Fincatech\Controller\EmpresaController();
                $this->InitController('Empresa');

                for($iEmpresa = 0; $iEmpresa < count($empresasSinEmailAlta); $iEmpresa++)
                {
                    $empresa = null;
                    $empresaProceso = $empresasSinEmailAlta[$iEmpresa];
                    //  Recuperamos la empresa
                    $empresa = $this->EmpresaController->Get($empresaProceso['id']);
                    //  Comprobamos que exista la empresa ya que puede haberse eliminado previamente
                    if(count($empresa['Empresa']) > 0)
                    {
                        $empresa = $empresa['Empresa'][0];
                        
                        //  Generamos una nueva password aleatoria
                        $password = HelperController::GenerateRandomPassword(8);
                        $data['password'] = md5($password);

                        $empresasEnvio .= '<strong>' . $empresa['razonsocial'] . '</strong><br>' . $empresa['email'] . '<br>Nueva Password: ' . $password . '<br><br>';

                        //  Guardamos en bbdd la nueva password
                        $this->EmpresaController->Update('Empresa', $data, $empresa['id']);

                        //  Recuperamos las comunidades en las que está asignada la empresa
                        $comunidadesEmpresa = $this->EmpresaController->ComunidadesAsignadas($empresa['id']);

                        //  Enviamos por cada 1 de ellas el e-mail de alta y asignación
                        if(count($comunidadesEmpresa) > 0)
                        {
                            foreach($comunidadesEmpresa as $comunidad)
                            {
                                //  Enviamos el e-mail de alta al proveedor por cada una de las comunidades
                                $this->EmpresaController->EnvioEmailAltaPlataforma($empresa, $comunidad['nombre'], null, $comunidad['administrador'], $password);
                            }
                        }else{
                            //  Enviamos el e-mail del alta sin los datos de comunidad ni de administrador
                            $this->EmpresaController->EnvioEmailAltaPlataforma($empresa, 'No asignada', null, 'Fincatech', $password);
                        }
                        $iEnvios++;
                    }
                }

            }

            //  Envío de e-mail con el resultado del cron al administrador del sistema y al dpto de desarrollo
            $info = 'Nº de envíos realizados: ' . $iEnvios . '<br>';
            $info .= str_pad("", 75, '-', STR_PAD_LEFT) . '<br>';
            $info .= 'Empresas a las que se le ha enviado el e-mail de alta<br>';
            $info .= str_pad("", 75, '-', STR_PAD_LEFT) . '<br><br>';
            $info .= $empresasEnvio;
            
            $this->SendEmail('desarrollo@fincatech.es, oscar.livin@gmail.com','Desarrollo', 'Envío E-mails pendientes Alta Plataforma Empresas', $info, false);


        }catch(\Exception $ex)
        {
            $this->SendEmail('desarrollo@fincatech.es','Desarrollo', '[Error] Envío E-mails pendientes Alta Plataforma Empresas', $ex->getMessage(), false);    
        }


    }

}