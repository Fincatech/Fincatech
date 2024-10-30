<?php

namespace Fincatech\Controller;

use Digitick\Sepa\GroupHeader;
use Digitick\Sepa\TransferFile\CustomerDirectDebitTransferFile;
use \HappySoftware\Controller\HelperController;
use \HappySoftware\Controller\ConfiguracionController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\BankController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\InvoiceController;
use Fincatech\Controller\InvoiceDetailController;
use Fincatech\Controller\RemesaDetalleController;
use Fincatech\Controller\RemesaDevolucionController;
use Fincatech\Controller\UsuarioController;

use Fincatech\Model\RemesaModel;
use Fincatech\Model\RemesaDetalleModel;
use Fincatech\Model\RemesaDevolucionModel;

use Fincatech\Entity\Remesa;
use Fincatech\Entity\RemesaDetalle;
use Fincatech\Entity\RemesaDevolucion;

//  Componente SEPA
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Facade\CustomerDirectDebitFacade;



use PHPUnit\TextUI\Help;
use stdClass;

class RemesaController extends FrontController{

    //  Controllers
    public  $ConfiguracionController;
    private $RemesaDetalleController;
    private $RemesaDevolucionController;

    //  Modelos
    public  $RemesaModel;
    private $RemesaDevolucionModel;

    //  Entidades
    private $remesaEntity;

    private string $_creditorId;    //  ID único que otorga el banco al crediticio
    private string $_creditorName;  //  Nombre de la empresa que emite los recibos
    private string $_tipoEmision = 'D';   //  D: (Adeudo directo) | T: (Transferencia de créditos)

    private string $_nombreFicheroRemesa = '';
    public function NombreFicheroRemesa(){
        return $this->_nombreFicheroRemesa;
    }

    public function __construct($params = null)
    {
        //$this->InitModel('Remesa', $params);
        $this->RemesaModel = new RemesaModel($params);
        //  Instancia Remesa Detalle
        $this->RemesaDetalleController = new RemesaDetalleController($params);
        //  Instancia Remesa Devolución Controller
        $this->RemesaDevolucionController = new RemesaDevolucionController($params);
        //  Instancia controller de configuración
        $this->ConfiguracionController = new \Fincatech\Controller\ConfiguracionController(null);
        //  Recuperamos el Creditor ID desde la configuración
        // $this->_creditorId = $this->ConfiguracionController->GetValue('creditorid');
        $this->_creditorName = $this->ConfiguracionController->GetValue('sepaempresa');
    }

    public function Create($entidadPrincipal, $datos)
    {

        $remesa = new Remesa();
        //  Llamamos al método de crear
        $remesa->SetReferencia($datos['referencia'])
        ->SetCreationDate(date('Y-m-d'))
        ->SetCreditorAccountIBAN($datos['iban'])
        ->SetCreditorAgentBIC(HelperController::GetBICFromIBAN($datos['bic']))
        ->SetCreditorId($this->_creditorId)
        ->SetCreditorName($this->_creditorName);

        $this->RemesaModel->CreateRemesa($remesa);
        $remesaId = $remesa->Id();
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RemesaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RemesaModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RemesaModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->RemesaModel->Get($id);
    }

    /**
     * Recupera los recibos asociados a una remesa por su ID
     * @param int $remesaId ID De la remesa a recuperar su detalle
     */
    public function RecibosByRemesaId($remesaId)
    {
        $data = [];

        //  Recuperamos los recibos asociados a la remesa
        $params['filterfield'] = 'idremesa';
        $params['filtervalue'] = $remesaId;
        $params['filteroperator'] = '=';

        $detalleRemesa = $this->RemesaDetalleController->List($params);       
        $data['Recibos'] = $detalleRemesa['RemesaDetalle'];
        return $data;
    }

    /**
     * Recupera el listado de recibos devueltos para todas las remesas
     */
    public function ListRecibosDevueltos()
    {
        $data['Remesa'] = $this->RemesaDetalleController->ListRecibosDevueltos();
        return $data;
    }

    /**
     * Recupera el listado de recibos cobrados para todas las remesas
     */
    public function ListRecibosCobrados()
    {
        $data['Remesa'] = $this->RemesaDetalleController->ListRecibosCobrados();
        return $data;
    }

    public function List($params = null)
    {
        $result = [];
        $result['Remesa'] = $this->RemesaModel->List($params);
        return $result;
    }

    /**
     * Crea una nueva remesa en el sistema
     */
    public function Insert($referencia, $fecha, $iban, $idAdministrador, $customerName)
    {

        $remesa = new Remesa();
        //  Llamamos al método de crear
        $remesa->SetReferencia($referencia)
        ->SetCreationDate($fecha)
        ->SetCreditorAccountIBAN($iban)
        ->SetCreditorAgentBIC(HelperController::GetBICFromIBAN($iban))
        ->SetCreditorId($this->_creditorId)
        ->SetCreditorName($this->_creditorName)
        ->SetCustomerId($idAdministrador)
        ->SetCustomerName($customerName);

        $this->RemesaModel->CreateRemesa($remesa);
        $remesaId = $remesa->Id();
        //  Si ha ocurrido error, avisamos
        if($remesaId === false){
            return false;
        }

        //  Procesamos los datos del contenido de la remesa y lo almacenamos
        return $remesaId;
    }

    /**
     * Genera una remesa SEPA en fichero XML con la información de las facturas recuperadas
     * @param array $invoiceData (optional). Información de la factura para ser incluida en el fichero de remesa
     * @param array $invoiceIds (optional). Id's de las facturas que se van a incluir en la remesa
     */
    public function CreateRemesaXML(string $sepaFileName, string $creditorId, int $customerId,  string $customerName, string $creditorIBAN, string $creditorBIC, int|null $invoiceId = null,  array|null $invoiceIds = null)
    {
        global $appSettings;

        try{

            //  Establecemos el creditor ID
            $this->_creditorId = $creditorId;

            $this->_nombreFicheroRemesa = $sepaFileName;
            //  Generamos la referencia de la remesa
            $referenciaRemesa = str_replace('.xml', '',$sepaFileName);
            $fecha = date('Y-m-d');

            //  Primero creamos la remesa en la bbdd
            $idRemesa = $this->Insert($referenciaRemesa, $fecha, $creditorIBAN, $customerId, $customerName);

            if($idRemesa === false){
                return 'error';
            }

            $msgId = 'REME' . date('Ymd') . str_pad($idRemesa, 6, '0', STR_PAD_LEFT) . 'FINCATECH';
            $transactionId = 'FINCATECH' . str_pad($idRemesa, 6, '0', STR_PAD_LEFT);

            $remesaPath = ROOT_DIR . $appSettings['storage']['remesas'];

            $header = new GroupHeader(date('Y-m-d-H-i-s'), $this->_creditorName);
            $header->setInitiatingPartyName($this->_creditorName);
            $header->setInitiatingPartyId($this->_creditorId);

            //  Normalizamos la cuenta IBAN para quitar guiones y espacios
            $creditorIBAN = HelperController::NormalizeIBAN($creditorIBAN);
            //  Creamos el cargo directo con cabecera
            $directDebit = TransferFileFacadeFactory::createDirectDebitWithGroupHeader($header, 'pain.008.001.02');
            //  Añadimos la información del pago
            $directDebit->addPaymentInfo($transactionId, array(
                'id'                    => $transactionId,
                'creditorName'          => $this->_creditorName,
                'creditorAccountIBAN'   => $creditorIBAN,
                'creditorAgentBIC'      => $creditorBIC,
                'seqType'               => PaymentInformation::S_RECURRING,
                'creditorId'            => $this->_creditorId,
                'localInstrumentCode'   => 'CORE',
                'batchBooking'          => false
            ));
    
            $recibos = [];

            //  Si se ha especificado el ID de manera individual
            if(!is_null($invoiceId)){
                $recibos[] = $invoiceId;
            }

            //  Si viene informado el parámetro de muchas facturas mediante ID
            if(is_null($invoiceId) && !is_null($invoiceIds)){
                $recibos = $invoiceIds;
            }

            //  Si se han pasado varios Id de factura hay que ir recuperando la información por cada una de ellas y adjuntarla a la remesa
            if(!is_null($recibos))
            {
                $invoiceController = new InvoiceController();

                for($i = 0; $i < count($recibos); $i++)
                {
                    $factura = $invoiceController->Get($recibos[$i]);
                    if(count($factura['Invoice']) > 0)
                    {
                        $invoiceData = $factura['Invoice'][0];
                        $comunidadData = $invoiceData['comunidad'][0];
                        $idComunidad = (int)$invoiceData['idcomunidad'];
                        $idAdministrador = (int)$invoiceData['idadministrador'];
                        $referenciaFactura = $invoiceData['numero'];
                        $descripcion = 'FINCATECH C. ANUAL ' . $invoiceData['anyo'] . ' ' . $invoiceData['referenciacontrato'];
                        $amount = str_replace(',','.', $invoiceData['total_taxes_inc']);
                        $customerIban = $invoiceData['iban'];
                        $customerBIC = HelperController::GetBICFromIBAN($customerIban);
                        $comunidad = $comunidadData['nombre'];
                        $customerName = $comunidadData['nombre'];      
                        $debtorMandate = HelperController::GenerateDebtorMandate($referenciaFactura, (int)$idAdministrador, (int)$idComunidad);                  
                        $this->AddTransfer($directDebit, $transactionId, $debtorMandate, $referenciaRemesa, $referenciaFactura, $descripcion, $amount, $customerIban, $customerBIC, $customerName, $idAdministrador, $idComunidad );
                        //  Actualizamos el estado de la factura como Cobrado
                        $invoiceController->UpdateStatusInvoice($invoiceData['id'], $invoiceController::ESTADO_COBRADO);
                        //  Añadimos la información relacionada con la remesa en bbdd
                        $this->CreateRemesaDetail($debtorMandate, $idRemesa, $invoiceData['id'], $descripcion, $amount, $customerName, $customerBIC, $customerIban);
                    }
                }

                //  Guardamos el fichero con la información de la remesa
                $xml = $directDebit->asXML();
                //  Guardamos el fichero en la ruta de remesas
                file_put_contents($remesaPath . DIRECTORY_SEPARATOR . $sepaFileName, $xml);

            }else{
                return false;
            }

        }catch(\Exception $ex)
        {
            return false;
        }

        return true;

    }

    /**
     * Inserta un recibo en la remesa
     * @param CustomerDirectDebitFacade &$directDebit
     * @param string $referenciaSepa
     * @param string $referenciaFactura
     * @param string $descripcion
     * @param string $amount
     * @param string $customerIban
     * @param string $customerBIC
     * @param string $customerName
     */
    private function AddTransfer(\Digitick\Sepa\TransferFile\Facade\CustomerDirectDebitFacade &$directDebit, string $paymentTransactionId, string $debtorMandate, string $referenciaSepa, string $referenciaFactura, string $descripcion, $amount, string $customerIban, string $customerBIC, string $customerName, int $idAdministrador, int $idComunidad)
    {
        //  Convertimos a céntimos el total de la factura
        $amount = round((float)$amount * 100);       

        $directDebit->addTransfer($paymentTransactionId, array(
            'amount'                => $amount,
            'debtorIban'            => $customerIban,
            'debtorBic'             => $customerBIC,
            'debtorName'            => $customerName,
            'debtorMandate'         => $debtorMandate,
            'debtorMandateSignDate' => date('d.m.Y'),//'27.12.2018',
            'remittanceInformation' => $descripcion,
            'endToEndId'            => 'Factura ' . $referenciaFactura
        ));
        
        return $directDebit;
    }

    /**
     * Genera el detalle de la remesa para la remesa que se está generando
     * 
     */
    private function CreateRemesaDetail(string $debtorMandate, $idRemesa, $invoiceId, $descripcion, $amount, $customerName, $customerBic, $customerIban)
    {
        $RemesaDetail = new RemesaDetalleController();
        $datosRemesa = new RemesaDetalle();
        $datosRemesa->idremesa = $idRemesa;
        $datosRemesa->invoiceid = $invoiceId;
        $datosRemesa->descripcion = $descripcion;
        $datosRemesa->amount = (float)$amount;
        $datosRemesa->customername = $customerName;
        $datosRemesa->customerbic = $customerBic;
        $datosRemesa->customeriban = $customerIban;
        $datosRemesa->uniqid = $debtorMandate;
        $remesaDetalleId = $RemesaDetail->CreateRemesaDetalle($datosRemesa);
        return $remesaDetalleId;
    }

    /**
     * Procesa la devolución de una remesa de recibos
     */
    public function ProcesarDevolucionRecibosRemesa($xml)
    {

        $totalDevolucionProcesada = 0;
        $numeroRecibosDevueltos = 0;
        $iRecibosProcesados = 0;
        $nombreRemesa = '';
        $remesasAfectadas = Array();
        try{

            //  Instanciamos el controller de facturación
            $invoice = new InvoiceController();

            // Registrar el espacio de nombres (namespace) si es necesario
            // $namespaces = $xml->getNamespaces(true);

            // Acceder al grupo <OrgnlGrpInfAndSts> para obtener OrgnlNbOfTxs y OrgnlCtrlSum
            if (isset($xml->CstmrPmtStsRpt)) {
                $numeroRecibosDevueltos = (string)$xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlNbOfTxs;
                $totalDevuelto = (string)$xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlCtrlSum;
                //  Nombre de la remesa que se presentó  CstmrPmtInfAndSts
                $nombreRemesa = (string)$xml->CstmrPmtStsRpt->OrgnlPmtInfAndSts[0]->OrgnlPmtInfId;
            }

            // Acceder al grupo <OrgnlPmtInfAndSts> y recorrer los elementos <TxInfAndSts>
            $informacionDevolucion = $xml->CstmrPmtStsRpt->OrgnlPmtInfAndSts;

            $numeroRemesas = count($informacionDevolucion);

            for($iRemesa = 0; $iRemesa < $numeroRemesas; $iRemesa++)
            {
                $datosDevolucion = $informacionDevolucion[$iRemesa];
                //  Validamos que sea una remesa de Fincatech
                if(strpos($datosDevolucion->OrgnlPmtInfId, 'FINCATECH') !== false)
                {
                    //  Incluimos el nombre de la remesa para control interno
                    array_push($remesasAfectadas, $datosDevolucion->OrgnlPmtInfId);
                    //  PROCESAMOS TODOS LOS RECIBOS QUE HAYAN PODIDO VENIR DEVUELTOS
                    foreach ($datosDevolucion->TxInfAndSts as $infoRecibo) 
                    {
                        // Número de factura
                        $numeroFactura = (string)$infoRecibo->OrgnlEndToEndId;
                        $numeroFactura = str_replace('Factura ', '', $numeroFactura);
                        
                        //  Recuperamos el id de la factura en base al identificador del recibo
                        if(!is_null($numeroFactura)){
        
                            // Estado del recibo
                            $estadoRecibo = (string)$infoRecibo->TxSts;
                        
                            // Referencia del recibo: uniqueid
                            $reciboUniqueId = (string)$infoRecibo->OrgnlTxRef->MndtRltdInf->MndtId;
        
                            // Motivo de la devolución
                            $codigoDevolucion = (string)$infoRecibo->StsRsnInf->Rsn->Cd;
                            $descripcionError = $this->DescripcionErrorSepa($codigoDevolucion);
        
                            //  Importe de la devolución
                            $importeDevolucion = (float)$infoRecibo->OrgnlTxRef->Amt->InstdAmt;
                            $totalDevolucionProcesada += (float)$importeDevolucion;
                            //  Fecha de devolución
                            $fechaDevolucion = (string)$infoRecibo->OrgnlTxRef->IntrBkSttlmDt;
                            //  Recuperamos el detalle del recibo desde el modelo
                            $this->RemesaDetalleController->GetByUniqueId($reciboUniqueId);
                            //  Si lo ha encontrado empezamos a procesar
                            if($this->RemesaDetalleController->remesaDetalle->id > 0)
                            {
                                $invoiceId = $this->RemesaDetalleController->remesaDetalle->invoiceid;
                                //  Cambiamos el estado a la factura
                                $invoice->UpdateStatusInvoice((int)$invoiceId, $invoice::ESTADO_FACTURAS_DEVUELTAS);
        
                                //  Comprobamos si el recibo para la remesa ya ha sido devuelto ya que si el mismo recibo para una remesa no puede procesarse 2 veces
                                if($this->ExisteDevolucionReciboRemesa()){
                                    //TODO:
                                }else{
                                    //  Establecemos la fecha al día que marca el fichero
                                    $this->RemesaDetalleController->remesaDetalle->datereturned = $fechaDevolucion;
                                    //  Cambiamos el estado al recibo a devuelto
                                    $this->RemesaDetalleController->remesaDetalle->estado = $invoice::ESTADO_FACTURAS_DEVUELTAS;
                                    $this->RemesaDetalleController->UpdateDetalle();
                                    //  Insertamos el recibo devuelto en el repositorio correspondiente
                                    $this->CreateDevolucionRemesa($codigoDevolucion, $descripcionError, $importeDevolucion);
                                }
        
                            }else{
                                // TODO: Registramos el error y lo adjuntamos al log de errores
                                $a = 0;
                            }
                            //  Comprobamos si el recibo ya está marcado como devuelto para la remesa
                            
        
                        }
        
                        // Mostrar la información extraída
                        // echo "Nº factura: $numeroFactura, Estado: $estadoRecibo, Razón: $descripcionError\n";
                        $iRecibosProcesados++;
                    }
                }
            }
            
            $msg = '<p>El proceso de devolución ha finalizado correctamente</p>';
            // $msg .= '<p class="mb-0 text-left"><strong>Remesa(s) afectada(s)</strong>: ' . $nombreRemesa . '</p>';
            $msg .= '<p class="mb-0 text-left"><strong>Remesa(s) afectada(s)</strong>: ' . implode(', ', $remesasAfectadas) . '</p>';
            $msg .= '<p class="mb-0 text-left"><strong>Total Recibos procesados</strong>: ' . $iRecibosProcesados . '</p>';
            $msg .= '<p class="mb-0 text-left"><strong>Importe total</strong>: ' . number_format($totalDevolucionProcesada, 2, ',','.') . '&euro;</p>';

            //  Devolvemos el mensaje al usuario
            return $msg;

        }catch(\Throwable $ex){
            return HelperController::errorResponse('error', $ex->getMessage());
        }
    }

    /**
     * Crea una entrada en la tabla de devoluciones de remesas
     */
    private function CreateDevolucionRemesa(string $codigoDevolucion, string $mensajeDevolucion, float $importeDevolucion)
    {
        $remesaDevolucion = new RemesaDevolucion();
        $remesaDevolucion->idremesa = $this->RemesaDetalleController->remesaDetalle->idremesa;
        $remesaDevolucion->idremesadetalle = $this->RemesaDetalleController->remesaDetalle->id;
        $remesaDevolucion->datereturned = $this->RemesaDetalleController->remesaDetalle->datereturned;
        $remesaDevolucion->amount = $importeDevolucion;
        $remesaDevolucion->codigo = $codigoDevolucion;
        $remesaDevolucion->message = $mensajeDevolucion;
        $remesaDevolucion->usercreate = $this->getLoggedUserId();
        $this->RemesaDevolucionController->CreateDevolucion($remesaDevolucion);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                             UTILIDADES DE LA GENERACIÓN DE REMESAS
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Procesa la devolución individual de un recibo
     * @param int $idRemesa. ID De la remesa
     * @param int $idRecibo. ID del recibo que se va a procesar
     */
    public function ReciboReturn(int $idRemesa, int $idRecibo)
    {
        //  Recuperamos el Recibo de la remesa
        $this->RemesaDetalleController->Get($idRecibo);

        //  Validamos que el recibo corresponda a la remesa a la cuál deseamos procesar la devolución
        if($idRemesa != $this->RemesaDetalleController->remesaDetalle->idremesa){
            return 'El recibo que intenta devolver no ha sido encontrado en la remesa';
        }

        //  Comprobamos que el recibo exista
        if($this->RemesaDetalleController->remesaDetalle->id >= 1)
        {
            //  Marcamos la factura como devuelta
            $invoice = new InvoiceController();
            $invoice->UpdateStatusInvoice(((int)$this->RemesaDetalleController->remesaDetalle->invoiceid), $invoice::ESTADO_FACTURAS_DEVUELTAS);            
            //  Establecemos la fecha al día que marca el fichero
            $this->RemesaDetalleController->remesaDetalle->datereturned = date('Y-m-d');
            //  Cambiamos el estado al recibo a devuelto
            $this->RemesaDetalleController->remesaDetalle->estado = $invoice::ESTADO_FACTURAS_DEVUELTAS;
            $this->RemesaDetalleController->UpdateDetalle();
            //  Creamos el apunte de devolución 
            $codigoDevolucion = 'FINC';
            //  Mensaje descriptivo de la devolución
            $descripcionError = $this->DescripcionErrorSepa($codigoDevolucion);
            //  Importe de la devolución
            $importeDevolucion = $this->RemesaDetalleController->remesaDetalle->amount;
            $this->CreateDevolucionRemesa($codigoDevolucion, $descripcionError, $importeDevolucion);
            return true;
        }else{
            return 'El recibo no se ha encontrado en el sistema';
        }
    }

    //TODO:
    public function RegenerateRemesa()
    {

    }

    /**
     * Devuelve el mensaje de error según el código que se lee del XML de devolución
     */
    private function DescripcionErrorSepa($codigoError)
    {
        /** Mensajes de error de devolución de recibo */
        $motivosRechazoSEPA = [
            'FINC' => 'Devuelto por Fincatech',
            'AC01' => 'Número de cuenta incorrecto',
            'AC04' => 'Cuenta cerrada',
            'AC06' => 'Cuenta bloqueada',
            'AG01' => 'Operación prohibida',
            'AG02' => 'Código de operación financiera inválido',
            'AM01' => 'Importe es cero',
            'AM02' => 'Importe no permitido',
            'AM03' => 'Divisa no permitida',
            'AM04' => 'Fondos insuficientes',
            'AM05' => 'Duplicada',
            'AM06' => 'Importe demasiado bajo',
            'AM07' => 'Importe bloqueado',
            'AM09' => 'Importe equivocado',
            'AM10' => 'Suma de control inválida',
            'BE01' => 'Incoherente con el cliente final',
            'BE04' => 'Falta la dirección del acreedor',
            'BE05' => 'Parte iniciadora no reconocida',
            'BE06' => 'Cliente final desconocido',
            'BE07' => 'Falta la dirección del deudor',
            'DT01' => 'Fecha inválida',
            'ED01' => 'Banco corresponsal no permitido',
            'ED03' => 'Información del saldo solicitada',
            'ED05' => 'Liquidación fallida',
            'FF01' => 'Formato de fichero inválido',
            'MD01' => 'Ausencia de orden de domiciliación',
            'MD02' => 'Falta información obligatoria en la orden de domiciliación',
            'MD03' => 'Formato del fichero inválido por razones distintas del indicador de agrupamiento',
            'MD04' => 'Formato del fichero inválido debido al indicador de agrupamiento',
            'MD06' => 'Solicitud de reembolso hecha por el cliente final',
            'MD07' => 'Cliente final fallecido',
            'MS02' => 'Razón no especificada, generada por el cliente',
            'MS03' => 'Razón no especificada, generada por el agente',
            'NARR' => 'Texto',
            'RC01' => 'Identificador de la entidad financiera incorrecto',
            'RF01' => 'Referencia de la operación no es única',
            'RR01' => 'Falta el identificador en la cuenta del ordenante',
            'RR02' => 'Falta el nombre o la dirección del ordenante',
            'RR03' => 'Falta el nombre o la dirección del beneficiario',
            'RR04' => 'Razones regulatorias',
            'TM01' => 'Hora de cierre (Cut-off time)'
        ];

        $motivo = 'Devuelto';

        if(array_key_exists($codigoError, $motivosRechazoSEPA) !== false){
            $motivo = $motivosRechazoSEPA[$codigoError];
        }

        return $motivo;

    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                             VALIDACIONES
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Valida si existe ya una devolución asociada a la remesa y recibo
     */
    public function ExisteDevolucionReciboRemesa():bool
    {
        return $this->RemesaDevolucionController->ExistsByIdRemesaAndIdRecibo($this->RemesaDetalleController->remesaDetalle);
    }

}