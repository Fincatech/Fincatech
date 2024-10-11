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

    public function __construct($params = null)
    {
        $this->RemesaDetalleModel = new RemesaDetalleModel($params);
    }

    /**
     * Crea el detalle de la remesa asociándolo a la remesa principal
     */
    public function CreateRemesaDetalle(RemesaDetalle $datos)
    {

        $remesaDetalle = new RemesaDetalle();
        $remesaDetalle->SetIdRemesa($datos->IdRemesa());
        $remesaDetalle->SetAmount($datos->Amount())
        ->SetDescription($datos->Description())
        ->SetInvoiceId($datos->InvoiceId())
        ->SetCustomerBIC($datos->CustomerBIC())
        ->SetCustomerIBAN($datos->CustomerIBAN())
        ->SetCustomerName($datos->CustomerName())
        ->SetUniqueId($datos->UniqueId())
        ->SetUserCreate($this->getLoggedUserId());

        $this->RemesaDetalleModel->CreateDetalleRemesa($remesaDetalle);
        $remesaId = $remesaDetalle->Id();
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

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RemesaDetalleModel->Update($entidadPrincipal, $datos, $usuarioId); 
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
        return $this->RemesaDetalleModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->RemesaDetalleModel->List($params);
    }

}