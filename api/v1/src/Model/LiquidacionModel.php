<?php

namespace Fincatech\Model;

use Fincatech\Entity\Liquidacion;
use Fincatech\Entity\LiquidacionDetalle;

class LiquidacionModel extends \HappySoftware\Model\Model{


    private $entidad = 'Liquidacion';
    private $tablasSchema = array('Liquidacion');

    public Liquidacion $liquidacion;
    public LiquidacionDetalle $liquidacionDetalle;

    //  Array que se utiliza para el detalle de la liquidación
    public $detail = [];

    /**
     * Añade una línea de detalle a la liquidación
     */
    public function AddDetailLine(LiquidacionDetalle $liquidacion)
    {
        $this->detail[] = $liquidacion;
        return $this;
    }

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel('Liquidacion', $params, $this->tablasSchema);

    }

    /**
     * Guarda en base de datos la información según los datos del modelo
     */
    public function _Save(Liquidacion $liquidacion)
    {

        $datos = [];
        $datos['idadministrador']  = $liquidacion->IdAdministrador();
        $datos['administrador']  = $liquidacion->Administrador();
        $datos['datefrom']  = $liquidacion->DateFrom();
        $datos['dateto']  = $liquidacion->DateTo();
        $datos['total_taxes_inc']  = $liquidacion->TotalTaxesInc();
        $datos['total_taxes_exc']  = $liquidacion->TotalTaxesExc();
        $datos['tax_rate']  = $liquidacion->TaxRate();
        $datos['total_a_cuenta']  = $liquidacion->TotalACuenta();
        $datos['referencia']  = $liquidacion->Referencia();
        $datos['estado']  = $liquidacion->Estado();

        //  Si es una inserción
        if( $liquidacion->Id() <= 0 ){
            $this->Insert($datos, $liquidacion);
        }else{
            $this->Update($this->entidad, $liquidacion, $liquidacion->Id());
        }

    }

    private function Insert(array $datos, Liquidacion $liquidacion){
        
        $result = $this->Create($this->entidad, $datos);
        
        if((int)$result['id'] > 0){
            $liquidacion->SetId($result['id']);
            //  Guardamos el detalle asociado
            $this->CreateDetail($liquidacion);
        }else{
            $liquidacion->SetId(-1);
        }
    }

    /**
     * Crea el detalle de la liquidación en bbdd
     */
    private function CreateDetail(Liquidacion $liquidacion)
    {
        foreach($liquidacion->Detalle() as $detalle)
        {
            $detalle->SetIdLiquidacion($liquidacion->Id());
            $insertData = [];
            $insertData['idliquidacion'] = $liquidacion->Id();
            $insertData['idcomunidad'] = $detalle->IdComunidad();
            $insertData['idinvoice'] = $detalle->IdInvoice();
            $insertData['comunidad'] = $detalle->Comunidad();
            $insertData['idservicio'] = $detalle->IdServicio();
            $insertData['pvpcomunidad'] = $detalle->PVPComunidad();
            $insertData['pvpretorno'] = $detalle->PVPRetorno();
            $insertData['total_taxes_exc'] = $detalle->TotalTaxesExc();
            $insertData['total_taxes_inc'] = $detalle->TotalTaxesInc();
            $this->Create('liquidaciondetalle', $insertData);
        }
    }

    /**
     * Método de búsqueda en el repositorio de Liquidaciones
     */
    public function Search($dataToSearch = null)
    {
        if(is_null($dataToSearch)){
            return parent::Search($this->SearchFields());
        }else{
            return parent::Search($dataToSearch);
        }
    }

    /**
     * Recupera la información de las liquidaciones pendientes para el administrador
     */
    public function LiquidacionPendiente($idAdministrador, $dateFrom, $dateTo)
    {
        /**
         * Se debe tener en cuenta lo siguiente:
         * Las facturas deben estar en estado Cobrado: C
         * Los ingresos a cuenta no deben estar procesados aún
         * Las facturas además no deben tener facturas rectificativas (Devoluciones)
         */
        // $sql = "
        //     SELECT 
        //         case when SUM(i.total_taxes_inc) is null then 0 else SUM(i.total_taxes_inc) end total_taxes_inc,
        //         case when SUM(i.total_taxes_exc) is null then 0 else SUM(i.total_taxes_exc) end total_taxes_exc,
        //         COUNT(DISTINCT (i.idcomunidad)) AS total_comunidades,
        //         case when ingresoscuenta.total_ingreso is null then 0 else ingresoscuenta.total_ingreso end total_ingreso_cuenta
        //     FROM
        //         invoice i left join (
        //             select idadministrador, sum(total) as total_ingreso from ingresoscuenta where procesado = 0 group by idadministrador
        //             ) ingresoscuenta on ingresoscuenta.idadministrador = i.idadministrador
        //     WHERE
        //         i.idadministrador = $idAdministrador
        //         and i.estado = 'C'
        //         and i.liquidada = 0
        //         AND i.dateinvoice BETWEEN '$dateFrom' AND '$dateTo'
        // ";
        $sql = "
        
        ";
        $res = $this->query($sql, false);
        return mysqli_fetch_assoc($res);
    }

}