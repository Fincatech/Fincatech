<?php

namespace Fincatech\Controller;

use Fincatech\Model\InformeValoracionSeguimientoModel;

class InformevaloracionseguimientoController extends FrontController{

    public $InformeValoracionSeguimientoModel;
    public $InformevaloracionseguimientoModel;
    public $UsuarioController;

    public function __construct($params = null)
    {
        $this->InitModel('InformeValoracionSeguimiento', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        $resultado = $this->InformeValoracionSeguimientoModel->Create($entidadPrincipal, $datos);

        $administradorNombre = '';
        $emailAdministrador = ADMINMAIL;
        $enlaceInformeValoracion = "#";

        //  Recuperamos el nombre del administrador al que se le genera el informe de valoración y seguimiento
        if($datos['usuarioId'] != '-1')
        {
            $this->InitController('Usuario');
            $administrador = $this->UsuarioController->Get($datos['usuarioId']);
            $administradorNombre = $administrador['Usuario'][0]['nombre'];
            $emailAdministrador = $administrador['Usuario'][0]['email'];
        }

        //  Recuperamos el enlace del archivo
            $informeValoracion = $this->Get($resultado['id']);
            // var_dump($informeValoracion['informevaloracionseguimiento'][0]['ficheroscomunes'][0]['nombrestorage']);
            if(isset($informeValoracion['informevaloracionseguimiento'][0]['ficheroscomunes'][0]['nombrestorage']))
            {
                $enlaceInformeValoracion = $informeValoracion['informevaloracionseguimiento'][0]['ficheroscomunes'][0]['nombrestorage'];
            }
            // $emailAdministrador = 'oscar.livin@gmail.com';
            //  Enviamos el e-mail al administrador
            $this->EnviarEmailInformeValoracion($administradorNombre, $emailAdministrador, $enlaceInformeValoracion);

        return $resultado;
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->InformeValoracionSeguimientoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->InformeValoracionSeguimientoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->InformeValoracionSeguimientoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->InformeValoracionSeguimientoModel->Get($id);
    }

    public function List($params = null)
    {
        //  Si es un usuario de tipo Administrador de fincas hay que filtrar por los suyos
        if($this->isAdminFincas())
        {
            $params['filterfield'] = 'usuarioId';
            $params['filtervalue'] = $this->getLoggedUserId();
            return $this->InformeValoracionSeguimientoModel->List($params, false);
        }else{
            return $this->InformeValoracionSeguimientoModel->List($params);
        }
    }

    /** Envía un e-mail al master con la información de la comunidad que ha dado de baja */    
    public function EnviarEmailInformeValoracion($nombreAdministrador, $emailAdministrador, $enlaceFichero)
    {
        
        $body = $this->GetTemplateEmailInformeValoracion();
        $body = str_replace('[@administrador@]', $nombreAdministrador, $body);
        $body = str_replace('[@enlace@]', $enlaceFichero, $body);
        $body = str_replace('[@fecha@]', date('d/m/Y'), $body);
        
        $this->SendEmail($emailAdministrador, $nombreAdministrador, 'Nuevo Informe de Valoración y Seguimiento', $body, true);

    }
    
    /** Recupera el template del alta de comunidad */
    private function GetTemplateEmailInformeValoracion(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/informe_valoracion.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

}