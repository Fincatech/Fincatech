<?php

namespace Fincatech\Controller;

use \HappySoftware\Controller\HelperController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\UsuarioController;

use Fincatech\Model\LiquidacionModel;

use Fincatech\Entity\Liquidacion;
use Fincatech\Entity\LiquidacionDetalle;

use PHPUnit\TextUI\Help;
use stdClass;

class LiquidacionesController extends FrontController{

    public $LiquidacionModel;

    //  Constantes de mensajes de error
    private const ERROR_PERIODO = 'El periodo seleccionado no es válido';
    private const ERROR_NO_COMUNIDADES = 'No hay comunidades pendientes de liquidar para el periodo seleccionado';
    private const ERROR_NO_FACTURASPENDIENTES = 'No hay facturas cobradas pendiente de liquidar';
    private const ERROR_NO_LIQUIDACION_PENDIENTE = 'Actualmente no hay ninguna liquidación de generar';

    //  Propiedad que se utiliza para almacenar la información recibida desde el WS
    private array|null $_processData = null;
    //  Propiedad que se utiliza para almacenar mensajes de error en cualquier proceso
    private string|null $_errorMsg = '';

    public function __construct($params = null)
    {
        $this->LiquidacionModel = new LiquidacionModel($params);
    }

    public function Create($entidadPrincipal, $datos)
    {

        $liquidacion = new Liquidacion();

        $liquidacion->SetIdAdministrador($datos['idadministrador'])
        ->SetAdministrador($datos['idadministrador'])
        ->SetDateFrom($datos['datefrom'])
        ->SetDateTo($datos['dateto'])
        ->SetTotalTaxesExc($datos['totaltaxesexc'])
        ->SetTotalTaxesInc($datos['totaltaxesinc'])
        ->SetTaxRate($datos['taxrate'])
        ->SetTotalACuenta($datos['totalcuenta'])
        ->SetReferencia($datos['referencia'])
        ->SetEstado($datos['estado']);

        $this->LiquidacionModel->_Save($liquidacion);
        //  Comprobamos si tiene ID para poder devolverlo
        $a = 0;
        if( (int)$liquidacion->Id() <= 0){
            return 'No se ha podido generar la liquidacion';
        }else{
            return array(
                'id' => $liquidacion->Id()
            );
        }

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->LiquidacionModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->LiquidacionModel->getSchema();
    }

    /**
     * Elimina una liquidación
     * 
     */
    public function Delete($id)
    {
        return HelperController::errorResponse('error','La liquidación no puede ser eliminada');
    }

    public function Get($id)
    {
        return $this->LiquidacionModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->LiquidacionModel->List($params, false);
    }

    private function Insert()
    {

    }

    private function ProcessDataBeforeGeneration($data, $estadoFactura)
    {
        $filters = [];

        $filters['filters'] = [];

        //  ID de administrador
        $filter = [];
        $filter['filterfield'] = 'idadministrador';
        $filter['filtervalue'] = $data['idadministrador'];
        $filter['filteroperator'] = 'eq';
        $filter['filtertype'] = 'int';
        $filters['filters'][] = $filter;

        // No liquidada
        $filter = [];
        $filter['filterfield'] = 'liquidada';
        $filter['filtervalue'] = 0;
        $filter['filteroperator'] = 'eq';
        $filter['filtertype'] = 'int';
        $filters['filters'][] = $filter;

        //  Estado cobrado
        $filter = [];
        $filter['filterfield'] = 'estado';
        $filter['filtervalue'] = $estadoFactura;
        $filter['filteroperator'] = 'eq';
        $filter['filtertype'] = 'string';
        $filters['filters'][] = $filter;

        //  Fecha desde
        $filter = [];
        $filter['filterfield'] = 'dateinvoice';
        $filter['filtervalue'] = $data['datefrom'];
        $filter['filteroperator'] = 'gt';
        $filter['filtertype'] = 'string';
        $filters['filters'][] = $filter;

        //  Fecha Hasta
        $filter = [];
        $filter['filterfield'] = 'dateinvoice';
        $filter['filtervalue'] = $data['dateto'];
        $filter['filteroperator'] = 'lt';
        $filter['filtertype'] = 'string';
        $filters['filters'][] = $filter;

        return $filters;

    }

    /**
     * Procesa y genera una liquidación en base a los parámetros recibidos
     */
    public function Process(array $data)
    {
        $this->_processData = $data;
        $resValidation = $this->ValidateBeforeProcessLiquidacion();
        if($resValidation === true){

            //  Instanciamos el controller de facturación
            $invoiceController = new InvoiceController();
            //  Recuperamos las facturas que están cobradas y pendientes de liquidar
            //  en el periodo que ha elegido el usuario
            $params = $this->ProcessDataBeforeGeneration($data, $invoiceController::ESTADO_COBRADO);

            //  Recuperamos las facturas pendientes de liquidación para calcular el total de la facturación
            $facturasPendientesLiquidacion = $invoiceController->List($params);
            //  Comprobamos que tenga facturas pendientes de liquidación
            if(count($facturasPendientesLiquidacion['Invoice']) <= 0)
                return ['error' => 'El Administrador seleccionado no tiene facturas pendientes de liquidación'];

            //  Recuperamos el nombre del administrador
            $administrador = $facturasPendientesLiquidacion['Invoice'][0]['administrador'];

            //  Acumulado total de la liquidación
            $totalAcumuladoLiquidacion = 0;
            //  Total comunidades procesadas
            $totalComunidades = count($facturasPendientesLiquidacion['Invoice']);
            //  Comunidades que se han procesado
            $comunidades = [];              

            //  Instancia de la entidad
            $liquidacion = new Liquidacion();

            $liquidacion->SetAdministrador($administrador)->SetCreated(date('Y-m-d H:m:i'))
                ->SetDateFrom($data['datefrom'])
                ->SetDateTo($data['dateto'])
                ->SetEstado('P')
                ->SetTotalACuenta(0)
                ->SetIdAdministrador($facturasPendientesLiquidacion['Invoice'][0]['idadministrador'])
                ->SetTaxRate($facturasPendientesLiquidacion['Invoice'][0]['tax_rate'])
            ;
           
            $liquidacionDetails = [];

            //  Recorremos el array de facturas pendientes de liquidación
            for($iFactura = 0; $iFactura < count($facturasPendientesLiquidacion['Invoice']); $iFactura++)
            {

                $liquidacionDetalle = [];
                $factura = $facturasPendientesLiquidacion['Invoice'][$iFactura];

                //  Comprobamos si tiene factura rectificativa, ya que si la tiene no hay que contabilizar esta
                if( count($factura['invoicerectificativa']) <= 0)
                {

                    //  Por cada una de las comunidades que estén en las facturas hay que recuperar los servicios
                    //  que tenga contratados, y, recuperar tanto el pvp como el precio de coste para poder calcular el retorno
                    $acumuladoParcial = 0;

                    //  ID Comunidad
                    $liquidacionDetalle['idcomunidad'] = $factura['id'];
                    //  Comunidad Nombre
                    $liquidacionDetalle['comunidad'] = $factura['comunidad'][0]['nombre'];

                    //  Comunidad
                    $comunidad = $factura['comunidad'][0]['codigo'] . ' - ' . $factura['comunidad'][0]['nombre'];
                    $comunidades[$comunidad] = [];

                    //  Recorremos el detalle de la factura cogiendo el nombre de la comunidad primero
                    for($iDetalle = 0; $iDetalle < count($factura['invoicedetail']); $iDetalle++)
                    {
                        $detalleFactura = $factura['invoicedetail'][$iDetalle];
                        $infoComunidad = [];
                        $infoComunidad['servicio'] = $invoiceController->ServiceNameById($detalleFactura['idservicio']);
                        //  Calculamos el retorno
                        $totalRetorno = (float)$detalleFactura['unit_price_comunidad'] - (float)$detalleFactura['unit_price_tax_exc'];
                        //  Establecemos el retorno formateado
                        $infoComunidad['importe'] = number_format($totalRetorno, 2,',','.');
                        //  Insertamos en el objeto de la comunidad
                        $comunidades[$comunidad][] = $infoComunidad;
                        //  Incrementamos el total del acumulado
                        $acumuladoParcial += $totalRetorno;

                        ////////////////////////////////////////////////////////////////
                        ///                          DETALLE
                        ////////////////////////////////////////////////////////////////
                        $liquidacionDetalle['idinvoice'] = $detalleFactura['idinvoice'];
                        $liquidacionDetalle['idservicio'] = $detalleFactura['idservicio'];
                        $liquidacionDetalle['pvpcomunidad'] = $detalleFactura['unit_price_comunidad'];
                        $liquidacionDetalle['pvpretorno'] = $totalRetorno;
                        $liquidacionDetalle['total_taxes_exc'] = (float)$detalleFactura['total_taxes_exc'];
                        $liquidacionDetalle['total_taxes_inc'] = (float)$detalleFactura['total_taxes_inc'];

                    }

                    //  Establecemos el total del retorno para la comunidad que se está procesando
                    $comunidades[$comunidad]['totalretorno'] = $acumuladoParcial;
                    //  Incrementamos el acumulado
                    $totalAcumuladoLiquidacion += $acumuladoParcial;
                    //  Adjuntamos el detalle de la liquidación 
                    $liquidacionDetails[] = $liquidacionDetalle;
                    
                }

            } 

            //  Referencia de la liquidacion
            $referencia = $this->CreateReferenciaLiquidacion();
            //  Total impuestos incluidos
            $totalTaxesInc = (float)$totalAcumuladoLiquidacion + (float)$totalAcumuladoLiquidacion * ( (float)$factura['tax_rate'] / 100 );

            //  Rellenamos la entidad
            $liquidacion->SetTotalTaxesExc($totalAcumuladoLiquidacion)->SetTotalTaxesInc($totalTaxesInc)->SetReferencia($referencia);

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///                             GENERACION DEL REGISTRO DE LIQUIDACIÓN
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //  Asignamos el detalle a la entidad de liquidación
            foreach($liquidacionDetails as $liquidacionDetail)
            {
                $detail = new LiquidacionDetalle();
                $detail->SetIdComunidad($liquidacionDetail['idcomunidad'])
                ->SetComunidad($liquidacionDetail['comunidad'])
                ->SetIdInvoice($liquidacionDetail['idinvoice'])
                ->SetIdServicio($liquidacionDetail['idservicio'])
                ->SetPVPComunidad($liquidacionDetail['pvpcomunidad'])
                ->SetPVPRetorno($liquidacionDetail['pvpretorno'])
                ->SetTotalTaxesExc($liquidacionDetail['total_taxes_exc'])
                ->SetTotalTaxesInc($liquidacionDetail['total_taxes_inc']);
                //  Añadimos la línea al detalle de la liquidación
                $liquidacion->SetDetalle($detail);
            }

            //  Creamos la liquidación en el sistema
            $this->LiquidacionModel->_Save($liquidacion);

            //  Una vez generada la liquidación tenemos que marcar las facturas como incluidas en una liquidacion
            foreach($liquidacion->Detalle() as $liquidacionDetail)
            {
                //  Cada una de las facturas incluidas en la liquidación la marcamos como liquidada
                //  para que no pueda volver a procesarse
                $invoiceController->UpdateStatusLiquidacion($liquidacionDetail->IdInvoice(), 1);
            }

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///                 GENERACION DE PDF INFORMATIVO RESULTADO DE LA LIQUIDACIÓN
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////


            // TODO: Enviamos el e-mail de liquidación tanto al administrador como al master del sistema

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///             MENSAJE INFORMATIVO RESULTADO DEL PROCESO DE GENERACIÓN DE LIQUIDACIÓN
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $fechaDesde = $data['datefrom'];
            $fechaHasta = $data['dateto'];
            if($fechaDesde == ''){
                $fecha = 'Todo hasta el día ' . date('d-m-Y', strtotime($fechaHasta));
            }else{
                $fecha = 'Desde el día ' . date('d-m-Y', strtotime($fechaDesde)) . ' hasta el día ' . date('d-m-Y', strtotime($fechaHasta));
            }
            $msg = '<div class="text-left border p-3 br-8">';
            $msg .= '<p><i class="bi bi-info"></i> <strong>Referencia liquidación</strong>: ' . $referencia . '</p>';
            $msg .= '<p><i class="bi bi-calendar3"></i> <strong>Periodo de liquidación</strong>: ' . $fecha . '</p>';
            $msg .= '<p><i class="bi bi-person"></i> <strong>Administrador</strong>: ' . $administrador . '</p>';
            $msg .= '<p><i class="bi bi-cash-coin"></i> <strong>Importe total liquidacion</strong>: ' . number_format($totalAcumuladoLiquidacion, 2, ',','.') . '&euro;</p>';
            $msg .= '<p><i class="bi bi-buildings"></i> <strong>Nº de comunidades liquidadas</strong>: ' . count($comunidades) . '</p>';
            $msg .= '<p class="mb-0"><i class="bi bi-cloud-arrow-down"></i> <strong>Informe generado</strong>: <a href="javascript:void(0);" target="_blank">Descargar</a></p>';
            $msg .= '</div>';

            return $msg;

        }else{
            return ['error' => $resValidation];
        }

    }

    /**
     * Valida los datos recibidos antes de procesar la liquidación
     */
    private function ValidateBeforeProcessLiquidacion()
    {
        //  Validamos los datos obligatorios: ID Administrador, Fecha Hasta
        $msg = '';

        if($this->_processData['idadministrador'] == '' || $this->_processData['idadministrador'] == '-1'){
            $msg .= 'El Administrador no es válido<br>';
        }

        if($this->_processData['dateto'] == ''){
            $msg .= 'La fecha hasta no es válida<br>';
        }

        if($msg == '')
        {
            return true;
        }else{
            return $msg;
        }
        return ($msg == '') ? true : $msg;

    }

    /**
     * Recupera la información de liquidación pendiente para un administrador y un período determinado
     * @param int $idAdministrador. ID del administrador
     * @param string $dateFrom. Fecha desde
     * @param string $dateTo. Fecha hasta
     * 
     */
    public function Info(int $idAdministrador, string $dateFrom, string $dateTo)
    {
        $idAdministrador = DatabaseCore::PrepareDBString($idAdministrador);
        $dateFrom = DatabaseCore::PrepareDBString($dateFrom);
        $dateTo = DatabaseCore::PrepareDBString($dateTo);

        if($dateFrom == '')
            $dateFrom = '2000-01-01';

        if($dateTo == ''){
            return ['error' => 'La fecha hasta no está informada o no es válida'];
        }

        //  Recuperamos todas las facturas que tenga cobradas y pendientes de liquidar
        //  Instanciamos el controller de facturación
        $invoiceController = new InvoiceController();   

        $data = [];
        $data['idadministrador'] = $idAdministrador;
        $data['datefrom'] = $dateFrom;
        $data['dateto'] = $dateTo;
        $params = $this->ProcessDataBeforeGeneration($data, $invoiceController::ESTADO_COBRADO);

        //  Recuperamos las facturas pendientes de liquidación para calcular el total de la facturación
        $facturasPendientesLiquidacion = $invoiceController->List($params);

        //  Variables de contabilización
        $totalTaxesExc = 0;
        $totalComunidades = 0;
        $totalLiquidacion = 0;
        $totalIngresoCuenta = 0;

        //  Realizamos los cálculos en función de los datos obtenidos
        if(count($facturasPendientesLiquidacion['Invoice']) > 0)
        {
            //  Total comunidades que se van a procesar
            $totalComunidades = count($facturasPendientesLiquidacion['Invoice']);
            foreach($facturasPendientesLiquidacion['Invoice'] as $factura)
            {
                foreach($factura['invoicedetail'] as $detalleFactura)
                {
                    $totalTaxesExc += $detalleFactura['total_taxes_exc'];
                    //  Liquidación
                    $liquidacion = (float)$detalleFactura['unit_price_comunidad'] - (float)$detalleFactura['total_taxes_exc'];
                    $totalLiquidacion += $liquidacion;
                }
            }
        }

        //  Construimos el objeto que vamos a devolver
        $infoData = [];
        $infoData['total_taxes_inc'] = $totalTaxesExc * 1.21;
        $infoData['total_taxes_exc'] = $totalTaxesExc;
        $infoData['total_comunidades'] = $totalComunidades;
        $infoData['total_liquidacion'] = $totalLiquidacion;
        $infoData['total_ingreso_cuenta'] = $totalIngresoCuenta;

        return $infoData;

    }


    /**
     * Genera un código único de liquidación para asignarlo
     */
    private function CreateReferenciaLiquidacion()
    {

        //////////////////////////////////////////
        ///             NOMENCLATURA
        //////////////////////////////////////////
        ///
        /// FECHA: DDMMYYYY
        /// ADMINISTRADOR : 
        ///
        /// Ej:
        ///   FINCATECH_LIQ_01092024
        //////////////////////////////////////////
        return 'FINCATECH_LIQ_' . date('dmY');

    }

}