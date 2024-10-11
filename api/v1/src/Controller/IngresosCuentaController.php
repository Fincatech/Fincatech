<?php

namespace Fincatech\Controller;

use \HappySoftware\Controller\HelperController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\UsuarioController;
use Fincatech\Model\IngresosCuentaModel;
use Fincatech\Entity\IngresosCuenta;
use PhpParser\Node\Expr\Exit_;
use PHPUnit\TextUI\Help;
use stdClass;

class IngresosCuentaController extends FrontController{

    public $IngresosCuentaModel;

    public function __construct($params = null)
    {
        $this->IngresosCuentaModel = new IngresosCuentaModel($params);
    }

    /**
     * Crea el detalle de la remesa asociándolo a la remesa principal
     */
    // public function CreateRemesaDetalle(IngresosCuenta $datos)
    // {

    //     $remesaDetalle = new RemesaDetalle();
    //     $remesaDetalle->SetIdRemesa($datos->IdRemesa());
    //     $remesaDetalle->SetAmount($datos->Amount())
    //     ->SetDescription($datos->Description())
    //     ->SetInvoiceId($datos->InvoiceId())
    //     ->SetCustomerBIC($datos->CustomerBIC())
    //     ->SetCustomerIBAN($datos->CustomerIBAN())
    //     ->SetCustomerName($datos->CustomerName())
    //     ->SetUniqueId($datos->UniqueId())
    //     ->SetUserCreate($this->getLoggedUserId());

    //     $this->IngresosCuentaModel->CreateDetalleRemesa($remesaDetalle);
    //     $remesaId = $remesaDetalle->Id();
    //     //  Si ha ocurrido error, avisamos
    //     if($remesaId === false){
    //         return false;
    //     }

    //     //  Procesamos los datos del contenido de la remesa y lo almacenamos
    //     return $remesaId;
    // }

    public function Create($entidadPrincipal, $datos)
    {

        $ingresoCuenta = new IngresosCuenta();
        $ingresoCuenta->SetConcepto($datos['concepto'])
        ->SetIdAdministrador($datos['idadministrador'])
        ->SetProcesado(0)
        ->SetObservaciones($datos['observaciones'])
        ->SetFechaIngreso($datos['fechaingreso'])
        ->SetTotal($datos['total']);
        $this->IngresosCuentaModel->_Save($ingresoCuenta);
        //  Comprobamos si tiene ID para poder devolverlo
        $a = 0;
        if( (int)$ingresoCuenta->Id() <= 0){
            return 'No se ha podido crear el ingreso a cuenta';
        }else{
            return array(
                'id' => $ingresoCuenta->Id()
            );
        }

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->IngresosCuentaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->IngresosCuentaModel->getSchema();
    }

    /**
     * Elimina un ingreso a cuenta mediante su id
     * 
     */
    public function Delete($id)
    {
        $result = 'ok';
        //  Recuperamos el ingreso a cuenta para validar si ya ha sido procesado en alguna liquidación y si además existe
        $ingresoCuenta = $this->Get($id);
        if(count($ingresoCuenta['IngresosCuenta']) > 0){
            $ingresoCuenta = $ingresoCuenta['IngresosCuenta'][0];
            if((int)$ingresoCuenta['procesado'] == 1){
                $result = 'El Ingreso a Cuenta no se ha podido eliminar porque ya ha sido procesado en una liquidación';
            }else{
                //$result = $this->IngresosCuentaModel->Delete($id);             
                $result = 'error de asdf';
            }
        }else{
            $result = 'El Ingreso a Cuenta no existe';
        }
        return $result;
    }

    public function Get($id)
    {
        return $this->IngresosCuentaModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->IngresosCuentaModel->List($params);
    }

}