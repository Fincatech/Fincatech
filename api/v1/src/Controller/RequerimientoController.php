<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RequerimientoModel;
use HappySoftware\Controller\HelperController;

class RequerimientoController extends FrontController{
    public $RequerimientoModel;
    private $requerimientoModel;
    private $bodyEmail = [];

    private $requerimientoData = [];

    private $empresasEmpleadosNoAsignados;
    private $requerimientosPendientesEmpresa;
    private $requerimientosEmpleadoCaducados;
    private $requerimientosEmpresaCaducados;

    //  Almacena el HTML de la tabla de requerimientos caducados de empresa
        private $tablaRequerimientosCaducadosEmpresa;
    //  Almacena el HTML de la tabla de requerimientos caducados de empleado
        private $tablaRequerimientosCaducadosEmpleado;
    //  Almacena el HTML de la tabla de empresas que están asignadas a comunidad pero aún no han asignado empleados
        private $tablaEmpresaEmpleadosSinAsignar;
    //  Almacena el HTML de la tabla de requerimientos pendientes de subir en materia de CAE
        private $tablaRequerimientosPendientesSubir;


    /// GETTER
    private function getRequerimientoData()
    {
        return $this->requerimientoData;
    }
    private function getEmpresasEmpleadosNoAsignados()
    {
        return $this->empresasEmpleadosNoAsignados;
    }
    private function getRequerimientosPendientesEmpresa()
    {
        return $this->requerimientosPendientesEmpresa;
    }
    private function getRequerimientosEmpleadoCaducados()
    {
        return $this->requerimientosEmpleadoCaducados;
    }
    private function getRequerimientosEmpresaCaducados()
    {
        return $this->requerimientosEmpresaCaducados;
    }        

    private function getBodyEmail()
    {
        return $this->bodyEmail;
    }
    

    /// SETTER
    private function setBodyEmail($value)
    {
        $this->bodyEmail = $value;
    }

    private function setRequerimientoData($value)
    {
        $this->requerimientoData = $value;
    }

    private function setEmpresasEmpleadosNoAsignados($value)
    {
        $this->empresasEmpleadosNoAsignados = $value;
    }
    private function setRequerimientosPendientesEmpresa($value)
    {
        $this->requerimientosPendientesEmpresa = $value;
    }
    private function setRequerimientosEmpleadoCaducados($value)
    {
        $this->requerimientosEmpleadoCaducados = $value;
    }
    private function setRequerimientosEmpresaCaducados($value)
    {
        $this->requerimientosEmpresaCaducados = $value;
    }        

    /// LOGIC
    public function __construct($params = null)
    {
        $this->InitModel('Requerimiento', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->RequerimientoModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RequerimientoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RequerimientoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RequerimientoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->RequerimientoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->RequerimientoModel->List($params);
    }

    //  Recuperar requerimientos relativos a comunidad
    public function ListRequerimientosComunidad($comunidadId)
    {
        return $this->RequerimientoModel->ListRequerimientosComunidad($comunidadId);
    }

    public function ListRequerimientoByIdTipo($idTipo)
    {
        return $this->RequerimientoModel->ListRequerimientoByIdTipo($idTipo) ;
    }

    /** Devuelve los requerimientos adjuntados para una empresa */
    public function GetRequerimientosEmpresa($idEmpresa)
    {
        $data = [];
        $data['documentacioncae'] = $this->RequerimientoModel->GetRequerimientosEmpresa($idEmpresa);
        return HelperController::successResponse( $data );
    }

    /** Comprueba que todos los requerimientos que han podido caducar  */
    public function ComprobarRequerimientosCaducados()
    {

        //  Comprobar si hay alguna empresa que no tenga asignados empleados a comunidades
            $data = $this->RequerimientoModel->GetEmpresasSinEmpleadoAsignadoAComunidad();
            $this->setEmpresasEmpleadosNoAsignados(is_null($data) ? array() : $data);

        //  Comprobar si tiene documentos pendientes de subir en materia de cae
            $data = $this->RequerimientoModel->GetRequerimientosEmpresaPendientesSubir();
            $this->setRequerimientosPendientesEmpresa(is_null($data) ? array() : $data);

        //  Hay que comprobar los requerimientos de empleados
            $data = $this->RequerimientoModel->GetRequerimientosCaducadosEmpleado();
            $this->setRequerimientosEmpleadoCaducados(is_null($data) ? array() : $data);

        //  Hay que comprobar los requerimientos de empresa
            $data = $this->RequerimientoModel->GetRequerimientosCaducadosEmpresa();
            $this->setRequerimientosEmpresaCaducados(is_null($data) ? array() : $data);
            
        //  Consolidamos la información en una matriz para posteriormente construir las tablas correspondientes
            $this->ConsolidarInformacionRequerimientos();

        //  Construimos la tabla renderizada para cada uno de los datos por cada una de las empresas
            $dataRequerimiento = $this->getRequerimientoData();
            echo(json_encode($dataRequerimiento));
            die();
            for($x = 0; $x < count($dataRequerimiento); $x++)
            {

                    $this->ConstruirTablasRequerimiento($dataRequerimiento[$x]);
                    // $personaContacto = $dataRequerimiento[$x]['personacontacto'];
                    $personaContacto = $dataRequerimiento[$x]['empresa'];
                    $email = $dataRequerimiento[$x]['email'];
                    $empresa = $dataRequerimiento[$x]['empresa'];

                //  Si hay algún requerimiento que procesar, enviamos el e-mail
                    $email = 'oscar.livin@gmail.com';
                    $enviado = $this->EnviarEmailRequerimientos($email, $empresa, $personaContacto);

                //  Hacemos una pausa de 3 segundos para evitar que el envío lo catalogue como spam
                    // if($enviado)
                    //     sleep(3);
            }

        //  Respuesta de la comprobación
            $data = [];
            $data['empleados'] = count($this->requerimientosEmpleadoCaducados);
            $data['empresas'] = count($this->requerimientosEmpresaCaducados);
            $data['requerimientos_pendientes'] = count($this->requerimientosPendientesEmpresa);
            $data['requerimientos_caducados'] = count($this->requerimientosEmpresaCaducados);
            $data['empresassinempleadosasignados'] = count($this->empresasEmpleadosNoAsignados);

            return HelperController::successResponse($data, 200);

    }

    /** Consolida la información recuperada en un mismo array */
    private function ConsolidarInformacionRequerimientos()
    {
        //$empleadosNoAsignados, $requerimientosPendientesSubir, $requerimientosEmpleadoCaducados, $requerimientosCaducados
        $datos = $this->RequerimientoModel->ConsolidarInformacionRequerimientos($this->getEmpresasEmpleadosNoAsignados(), $this->getRequerimientosPendientesEmpresa(), $this->getRequerimientosEmpleadoCaducados(), $this->getRequerimientosEmpresaCaducados());
        $this->setRequerimientoData($datos);
    }

    /** Construye las tablas en HTML con la información correspondiente */
    private function ConstruirTablasRequerimiento($_dataRequerimiento)
    {
        $this->ConstruirTablaEmpresaSinEmpleadosAsignados($_dataRequerimiento);
        $this->ConstruirTablaRequerimientosPendientesSubir($_dataRequerimiento);
        $this->ConstruirTablaRequerimientosCaducadosEmpleados($_dataRequerimiento);
        $this->ConstruirTablaRequerimientosCaducadosEmpresa($_dataRequerimiento);
    }

    /** Construye la tabla HTML para los requerimientos pendientes de subir */
    private function ConstruirTablaRequerimientosPendientesSubir($_dataRequerimiento)
    {

        $this->tablaRequerimientosPendientesSubir = '';

        if(count($_dataRequerimiento['pendientesubir']) > 0)
        {

            $tabla = '<p style="text-align:center;"><strong>PENDIENTES DE SUBIR</strong></p>';
            $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
            $tabla .= '<table>';
            $tabla .= '     <thead>
                                <tr><th>Requerimiento</th></tr>
                            </thead>
                            <tbody>';

            for($y = 0; $y < count($_dataRequerimiento['pendientesubir']); $y++)
            {
                $tabla .= '<tr><td><p>'.$_dataRequerimiento['pendientesubir'][$y]['requerimiento'].'</p></td></tr>';
            }

            $tabla .= '</tbody></table>';
            $this->tablaRequerimientosPendientesSubir = $tabla;

        }


    }

    /** FIX: Construye la tabla HTML para aquellas comunidades asignadas que no tienen empleado asignado */
    private function ConstruirTablaEmpresaSinEmpleadosAsignados($_dataRequerimiento)
    {
        $this->tablaEmpresaEmpleadosSinAsignar = '';

        if(count($_dataRequerimiento['empleadosnoasignados']) > 0)
        {

            $tabla = '<p style="text-align:center;"><strong>COMUNIDADES SIN EMPLEADO ASIGNADO</strong></p>';
            $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
            $tabla .= '<table>';
            $tabla .= '     <thead>
                                <tr><th>Comunidad</th></tr>
                            </thead>
                            <tbody>';

            for($y = 0; $y < count($_dataRequerimiento['empleadosnoasignados']); $y++)
            {
                $tabla .= '<tr><td><p>'.$_dataRequerimiento['empleadosnoasignados'][$y]['nombre'].'</p></td></tr>';
            }

            $tabla .= '</tbody></table>';
            $this->tablaEmpresaEmpleadosSinAsignar = $tabla;

        }

    }

    /** Construye la tabla HTML de los requerimientos caducados de empresa y los envía por e-mail */
    private function ConstruirTablaRequerimientosCaducadosEmpresa($_dataRequerimiento)
    {
        $this->tablaRequerimientosCaducadosEmpresa = '';

        if(count($_dataRequerimiento['empresacaducado']) > 0)
        {

            $tabla = '<p style="text-align: center;"><strong>REQUERIMIENTOS DE EMPRESA CADUCADOS</strong></p>';
            $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
            $tabla .= '<table>';
            $tabla .= '     <thead>
                                <tr>
                                    <th>Requerimiento</th>
                                    <th style="text-align:center;">Fecha caducidad</th>
                                </tr>
                            </thead>
                            <tbody>';

            for($y = 0; $y < count($_dataRequerimiento['empresacaducado']); $y++)
            {
                $tabla .= '<tr>
                                <td><p>'.$_dataRequerimiento['empresacaducado'][$y]['requerimiento'].'</p></td>
                                <td style="text-align:center;"><p style="text-align:center;">'.$_dataRequerimiento['empresacaducado'][$y]['fecha'].'</p></td>
                            </tr>';
            }

            $tabla .= '</tbody></table>';
            $this->tablaRequerimientosCaducadosEmpresa = $tabla;

        }

    }

    /** Construye la tabla HTML de los requerimientos caducados de los empleados de la empresa */
    private function ConstruirTablaRequerimientosCaducadosEmpleados($_dataRequerimiento)
    {
        $this->tablaRequerimientosCaducadosEmpleado = '';

        if(count($_dataRequerimiento['empleadocaducado']) > 0)
        {

            $tabla = '<p style="text-align: center;"><strong>REQUERIMIENTOS DE EMPLEADO CADUCADOS</strong></p>';
            $tabla .= '      <p style="font-size: 14px; line-height: 140%;"><span style="font-family: Raleway, sans-serif; font-size: 14px; line-height: 19.6px;">&nbsp;</span></p>';
            $tabla .= '<table>';
            $tabla .= '     <thead>
                                <tr>
                                    <th>Trabajador</th>
                                    <th>Requerimiento</th>
                                    <th style="text-align:center;">Fecha caducidad</th>
                                </tr>
                            </thead>
                            <tbody>';

            for($y = 0; $y < count($_dataRequerimiento['empleadocaducado']); $y++)
            {
                $tabla .= '<tr>
                                <td><p>'.$_dataRequerimiento['empleadocaducado'][$y]['nombre'].'</p></td>
                                <td><p>'.$_dataRequerimiento['empleadocaducado'][$y]['requerimiento'].'</p></td>
                                <td style="text-align:center;"><p style="text-align:center;">'.$_dataRequerimiento['empleadocaducado'][$y]['fecha'].'</p></td>
                            </tr>';
            }

            $tabla .= '</tbody></table>';
            $this->tablaRequerimientosCaducadosEmpleado = $tabla;

        }


    }

    /** Envía el e-mail de requerimientos a la empresa correspondiente */
    private function EnviarEmailRequerimientos($email, $empresa, $personacontacto)
    {
        if($this->tablaEmpresaEmpleadosSinAsignar == '' && $this->tablaRequerimientosCaducadosEmpleado == '' &&
            $this->tablaRequerimientosCaducadosEmpresa == '' && $this->tablaRequerimientosPendientesSubir == '')
        {
            return false;
        }else{

            //  Recuperamos la plantilla del e-mail
                $templateEmail = $this->GetTemplateEmail('req_caducado_empresa');
                $body = str_replace('[@persona_contacto@]', $personacontacto, $templateEmail);
                $body = str_replace('[@tabla_datos_empresa@]', $this->tablaRequerimientosCaducadosEmpresa,$body);
                $body = str_replace('[@tabla_datos_empleado@]', $this->tablaRequerimientosCaducadosEmpleado,$body);
                $body = str_replace('[@tabla_datos_comunidades@]', $this->tablaEmpresaEmpleadosSinAsignar,$body);
                $body = str_replace('[@tabla_datos_pendientes@]', $this->tablaRequerimientosPendientesSubir,$body);

            //  Para debug:
               // $email = 'oscar@happysoftware.es';

            $this->SendEmail($email, $empresa, 'Actualización documentos CAE', $body, true);
            return true;
        }
    }

}