<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\DpdModel;
use Fincatech\Controller\ComunidadController;

class DpdController extends FrontController{

    private $dpdModel;
    private $_emailDPD = 'dpd@fincatech.es';
    public $DpdModel;

    public $ComunidadController;

    public function __construct($params = null)
    {
        $this->InitModel('Dpd', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        $result = $this->DpdModel->Create($entidadPrincipal, $datos);
        
        //  Enviamos e-mail al DPD para avisar de nueva consulta
            $this->InitController('Comunidad');
            $comunidad = $this->ComunidadController->Get($datos['idcomunidad']);

        if(count($comunidad) > 0)
        {
            $nombreComunidad = $comunidad['Comunidad'][0]['nombre'];
            $nombreAdministrador = $comunidad['Comunidad'][0]['usuario'][0]['nombre'];
            $consulta = $datos['consulta'];

            //  Recuperamos la plantilla del e-mail para enviarle la consulta
            $template = $this->GetTemplateEmail('consultadpd');
            $template = str_replace('[@nombre]', $nombreAdministrador, $template);
            $template = str_replace('[@comunidad]', $nombreComunidad, $template);
            $template = str_replace('[@consulta]', htmlspecialchars($consulta), $template);

            //  Enviamos un e-mail al responsable del DPD con e-mail dpd@fincatech.es
            $this->SendEmail($this->_emailDPD, 'DPD', 'Nueva consulta DPD', $template, false);
        }

        return $result;
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->DpdModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->DpdModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->DpdModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->DpdModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->DpdModel->List($params);
    }

}