<?php

namespace Fincatech\Controller;

use \HappySoftware\Controller\HelperController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\BankController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\InvoiceDetailController;
use Fincatech\Controller\UsuarioController;

use Fincatech\Model\RemesaDetalleModel;
use Fincatech\Entity\RemesaDetalle;

//  Componente SEPA
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use PHPUnit\TextUI\Help;
use stdClass;

class RemesaDetalleController extends FrontController{

    public $RemesaDetalleModel;

    private $remesaDetalleEntity;

    public RemesaDetalle $remesaDetalle;

    public function __construct($params = null, $id = null)
    {
        $this->RemesaDetalleModel = new RemesaDetalleModel($params);
        if(!is_null($id)){
            $this->Get($id);
        }
    }

    /**
     * Crea el detalle de la remesa asociándolo a la remesa principal
     */
    public function CreateRemesaDetalle(RemesaDetalle $datos)
    {

        $datos->presentado = 1;
        $datos->usercreate = $this->getLoggedUserId();

        $this->RemesaDetalleModel->CreateDetalleRemesa($datos);
        $remesaId = $datos->id;
        //  Si ha ocurrido error, avisamos
        if($remesaId === false){
            return false;
        }

        //  Procesamos los datos del contenido de la remesa y lo almacenamos
        return $remesaId;
    }

    public function Create($entidadPrincipal, $datos)
    {
        $remesaDetalle = new RemesaDetalle();

        // $remesa = new Remesa();
        // //  Llamamos al método de crear
        // $remesa->SetReferencia($datos['referencia'])
        // ->SetCreationDate(date('Y-m-d'))
        // ->SetCreditorAccountIBAN($datos['iban'])
        // ->SetCreditorAgentBIC($datos[''])
        // ->SetCreditorId($this->_creditorId)
        // ->SetCreditorName($this->_creditorName);

        // $this->RemesaDetalleModel->CreateRemesa($remesa);
        // $remesaId = $remesa->Id();
        //  Procesamos los datos del contenido de la remesa y lo almacenamos

    }

    public function Update($entidadPrincipal, $datos, $id)
    {
        return $this->RemesaDetalleModel->Update($entidadPrincipal, $datos, $id); 
    }

    /**
     * Actualiza el detalle de la remesa
     */
    public function UpdateDetalle()
    {
        $this->remesaDetalle->updated = date('Y-m-d H:i:s', time());
        $data = array_change_key_case(get_object_vars($this->remesaDetalle), CASE_LOWER);
        $entidad = $this->RemesaDetalleModel->getEntidad();
        $this->Update($entidad, $data, $this->remesaDetalle->id);
    }

    public function getSchemaEntity()
    {
        return $this->RemesaDetalleModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RemesaDetalleModel->Delete($id);
    }

    public function Get($id)
    {
        $this->remesaDetalle = new RemesaDetalle();
        $data = $this->RemesaDetalleModel->Get($id);
        $data = [];

        if($this->RemesaDetalleModel->remesa->id > 0)
        {
            $this->remesaDetalle = $this->RemesaDetalleModel->remesa;
        }
        
        return $data;
    }

    public function GetByUniqueId($uniqueId)
    {
        $remesaDetalleId = $this->RemesaDetalleModel->GetIdByUniqueId($uniqueId);
        if(count($remesaDetalleId) > 0){
            $this->Get($remesaDetalleId[0]['id']);            
        }
    }

    public function List($params = null)
    {
       return $this->RemesaDetalleModel->List($params);
    }

}