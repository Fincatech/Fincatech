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
use Fincatech\Controller\InvoiceDetailController;
use Fincatech\Controller\UsuarioController;

use Fincatech\Model\RemesaModel;
use Fincatech\Entity\Remesa;
use Fincatech\Entity\RemesaDetalle;

//  Componente SEPA
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Facade\CustomerDirectDebitFacade;



use PHPUnit\TextUI\Help;
use stdClass;

class RemesaController extends FrontController{

    public $ConfiguracionController;

    public $RemesaModel;

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
        $datosRemesa->SetIdRemesa($idRemesa);
        $datosRemesa->SetInvoiceId($invoiceId);
        $datosRemesa->SetDescription($descripcion);
        $datosRemesa->SetAmount((float)$amount);
        $datosRemesa->SetCustomerName($customerName);
        $datosRemesa->SetCustomerBIC($customerBic);
        $datosRemesa->SetCustomerIBAN($customerIban);
        $datosRemesa->SetUniqueId($debtorMandate);
        $remesaDetalleId = $RemesaDetail->CreateRemesaDetalle($datosRemesa);
        return $remesaDetalleId;
    }

    public function RecibosByRemesaId($remesaId)
    {
        $data = [];
        $data['Recibos'] = $this->RemesaModel->Recibos($remesaId);
        return $data;
    }

    private function GetBICByIBAN($iban)
    {
        //  Cogemos los 4 primeros dígitos para buscar por código de entidad el banco
        $entidad = substr($iban, 0,4);
        $bankController = new BankController();

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                             UTILIDADES DE LA GENERACIÓN DE REMESAS
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function RegenerateRemesa()
    {

    }

}