<?php

namespace  Fincatech\Controller;


use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\ConfiguracionController;
use HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\UsuarioController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\BankController;
use Fincatech\Model\InvoiceDetailModel;
use Fincatech\Model\InvoiceModel;
use PHPUnit\TextUI\Help;
use stdClass;

class InvoiceDetailController extends FrontController{

    public $InvoiceDetailModel;

    protected $UsuarioController, $ComunidadController;

    private $usuario;

    public function __construct($params = null)
    {
        $this->InvoiceDetailModel = new InvoiceDetailModel($params);
        //  Instanciamos el controller de comunidad
        $this->InitController('Comunidad');       
        //  Usuario
        $this->InitController('Usuario');
        //  Cargamos la configuración previamente almacenada

    }

    /**
     * Create user
     * @param string $entidadPrincipal. Entity Name
     * @param json $datos. JSON Object with values to create
     */
    public function Create($entidadPrincipal, $datos)
    {

        // $datos['salt'] = '';

        // if( trim($datos['password']) === '')
        // {
        //     $datos['password'] = md5('123456');
        // }else{
        //     $datos['password'] = md5( $datos['password'] );
        // }

        // //  19012023: Se comprueba el email contacto para evitar error
        // if(isset($datos['emailcontacto'])){
        //     $datos['email'] = trim($datos['emailcontacto']);
        // }else{
        //     $datos['emailcontacto'] = trim($datos['email']);
        // }

        // $datos['email'] = trim($datos['email']);

        // if($this->ExisteEmailLogin( $datos['email'] ))
        // {
        //     return HelperController::errorResponse('error','El e-mail ya existe', 200);
        // }else{
        //     //  Llenamos el modelo y lo guardamos
        //     // $this->InvoiceModel->Fill('usuario');
        //     // $this->InvoiceModel->_Save();
        //     //  Llamamos al método de crear
        //     return $this->InvoiceModel->Create($entidadPrincipal, $datos);
        // }
        
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
            // return $this->InvoiceModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->InvoiceDetailModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->InvoiceDetailModel->Delete($id);
    }

    public function Get($id)
    {

        $this->usuario = $this->InvoiceDetailModel->Get($id);

    }

    public function List($params = null)
    {
       return $this->InvoiceDetailModel->List($params);
    }

    /**
     * Guarda en bbdd las líneas de detalle de la factura
     */
    public function Save()
    {
        //  Validamos que haya líneas de detalle para insertar
        $detailLines = $this->InvoiceDetailModel->DetailLines();
        for($iDetail =  0; $iDetail < count($detailLines); $iDetail++)
        {
            $detail = $detailLines[$iDetail];
            //  Seteamos los datos de la línea de detalle
            $this->InvoiceDetailModel->SetIdInvoice($detail['idinvoice'])
            ->SetIdServicio($detail['idservicio'])
            ->SetDetail($detail['detail'])
            ->SetUnitPriceTaxInc($detail['unit_price_tax_inc'])
            ->SetUnitPriceTaxExc($detail['unit_price_tax_exc'])
            ->SetQuantity($detail['quantity'])
            ->SetTaxRate($detail['tax_rate'])
            ->SetTotalTaxesExc($detail['total_taxes_exc'])
            ->SetUnitPriceComunidad($detail['unit_price_comunidad'])
            ->SetTotalTaxesInc($detail['total_taxes_inc']);

            $this->InvoiceDetailModel->_Save();
            
        }

    }

    /**
     * Add Detail Lineç
     * @param object $detailLine Detalle de la línea asociada a la factura
     */
    public function AddDetailLine($detailLine)
    {
        $this->InvoiceDetailModel->AddDetailLine($detailLine);
    }

    /**
     * Get All detail lines
     * @return array All Detail Lines
     */
    public function DetailLines()
    {
        return $this->InvoiceDetailModel->DetailLines();
    }

    /**
     * Clear all detail lines from model
     */
    public function ClearDetailLines()
    {
        $this->InvoiceDetailModel->ClearDetailLines();
    }

}