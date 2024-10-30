<?php

namespace Fincatech\Controller;

use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\ConfiguracionController;
use HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\BankController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\InvoiceDetailController;
use Fincatech\Controller\InvoiceRectificativaController;
use Fincatech\Controller\UsuarioController;
use Fincatech\Entity\InvoiceRectificativa;
use \Fincatech\Model\InvoiceModel;

//  Componente SEPA


use PHPUnit\TextUI\Help;
use SebastianBergmann\Template\Template;
use stdClass;
use Throwable;

class InvoiceController extends FrontController{

    private $debugMode = true;

    public $InvoiceModel;

    //  Controllers
    protected $UsuarioController, $ConfiguracionController, $ComunidadController, $BankController, $InvoiceDetailController;

    //  Estados de facturación
    public const ESTADO_COBRADO = 'C';
    public const ESTADO_FACTURADO = 'F';
    public const ESTADO_PENDIENTE = 'P';
    public const ESTADO_FACTURAS_DEVUELTAS = 'D';
    public const ESTADO_FACTURA_RECTIFICATIVA = 'R';

    public const NOMENCLATURA_DOCCAE = 'DOCUMENTOS CAE';

    //  Asunto predefinido del envío de Factura
    private const EMAIL_ASUNTO = 'Fincatech - Fra. ';
    //  Prefijos ficheros PDF y ZIP
    private const FICHERO_PREFIX = 'FINCATECH_FACT_';

    //  Log de errores de facturación
    private string  $logErrores = '';   //  Texto del log de errores
    private string  $erroresFileName;   //  Nombre del fichero de errores para mostrar al usuario cuando se facture
    private bool    $haveError = false; //  Se utiliza para saber si ha ocurrido algún error
    private int     $iErrores = 0;      //  Se utiliza para contabilizar el número de errores ocurridos durante la generación

    //  Total de la facturación generada
    public float $totalFacturado = 0;
    public int $totalFacturas = 0;

    //  Facturas que se han generado en el proceso para generar el SEPA
    private array $remesaInvoiceIds   = [];
    private array $invoiceIds         = [];
    private array $invoicePdfIds      = [];
    //  XML SEPA
    private string $xmlSepaName;
    //  Comunidad que se va a facturar
    private $comunidadToBill;

    //////////////////////////////////////////////////
    //  Propiedades reutilizables de configuración
    //////////////////////////////////////////////////

    //  Simulación de facturación
    private bool $simulacion = false;

    //  Serie de facturación
    private string  $prefijoSerieFacturacion = '';
    private $serieFacturacion = 1;
    
    //  Serie de facturación rectificativas
    private string $prefijoSerieFacturacionRectificativa = '';
    private $serieFacturacionRectificativa = 1;
    
    //  Impuesto a aplicar
    private $impuesto = 21;

    //  Conceptos por defecto
    private string $confConceptoCAE;
    private string $confConceptoDPD;
    private string $confCertificadoDigital;
    private string $confConceptoDOCCAE;

    //  Opciones de la generación
    protected bool $envioEmailAdministrador = true;
    protected bool $agruparServicios = false;
    protected bool $agruparFacturasZip = true;
    protected bool $envioAPI = false;
    protected string $conceptoCAE = '';
    protected string $conceptoDOCCAE = '';
    protected string $conceptoDPD = '';
    protected string $conceptoCertificadosDigitales = '';
    protected string $emailBody = '';

    //  Array que contiene los nombres de los ficheros PDF en caso que hayan de enviarse en un ZIP
    private $pdfFileNames = [];

    //////////////////////////////////////////////////////
    //  Propiedades que se utilizan para la generación
    //////////////////////////////////////////////////////
    private array $comunidadesFacturacion = [];
    private $iMesFacturacion = 1;
    private $iAnyoFacturacion;

    //  Se utiliza para saber si para un administrador y comunidad ya se ha facturado este servicio ya que es único, no se factura 2 veces
    private bool $servicioDOCCaeFacturado = false;

    private bool $servicioCAE = false;
    private bool $servicioDocCAE = false;
    private bool $servicioDPD = false;
    private bool $servicioCertificadosDigitales = false;

    private array $banco;
    private array $administrador;
    private bool $remesaGenerada = false;

    //  Propiedades para los nombres de ficheros generados
    private string  $zipFileName = '';
    private string  $zipPath;
    private string  $pdfPath;
    private array   $pdfNames = [];

    //  Variable que se utiliza para almacenar el nombre temporal del fichero que se generará de la remesa
    private string $ficheroRemesa = '';

    private string $invoiceServer;

    private $usuario;

    public function __construct($params = null)
    {
        global $appSettings;

        //  Modelo
        $this->InvoiceModel = new InvoiceModel($params);
        //  Instanciamos el controller del detalle
        $this->InvoiceDetailController = new InvoiceDetailController($params);
        //  Instanciamos el controller de configuración
        $this->ConfiguracionController = new ControllerConfiguracionController($params);
        //  Instanciamos el controller de comunidad
        $this->ComunidadController = new ComunidadController($params);
        //  Banco
        $this->BankController = new BankController($params);   
        //  Usuario
        $this->UsuarioController = new UsuarioController($params);
        //  Cargamos la configuración previamente almacenada
        $this->LoadConfiguracionFacturacion();
        //  Inicializamos el control de errores
        $this->InicializarLogErrores();
        //  Inicialización propiedades del controller
        $this->zipPath = $appSettings['storage']['facturaszip'] . DIRECTORY_SEPARATOR;
        $this->pdfPath = $appSettings['storage']['facturas'] . DIRECTORY_SEPARATOR;
        $this->invoiceServer = $appSettings['ftp_servers']['facturacion']['server_url'];
    }

    /**
     * Carga la configuración relativa a la facturación desde bbdd: Serie, Prefijos e Impuesto
     */
    private function LoadConfiguracionFacturacion()
    {

        if($this->ConfiguracionController->HasValues())
        {
            //  Serie de facturación
            $this->prefijoSerieFacturacion = $this->ConfiguracionController->GetValue('prefseriefact');
            //  TODO Hay que calcular la serie y reiniciar si es un nuevo año y aún no se han realizado facturas para ese año            
            $this->serieFacturacion = (int)$this->ConfiguracionController->GetValue('seriefacturacion');
            //  Serie de facturación rectificativas
            $this->prefijoSerieFacturacionRectificativa = $this->ConfiguracionController->GetValue('prefseriefacrect');
            $this->serieFacturacionRectificativa = (int)$this->ConfiguracionController->GetValue('serierectificativa');
            //  Impuesto a aplicar
            $this->impuesto = (float)$this->ConfiguracionController->GetValue('impuesto');
            //  Conceptos
            $this->confConceptoCAE = $this->ConfiguracionController->GetValue('nomcae');
            $this->confConceptoDPD = $this->ConfiguracionController->GetValue('nomdpd');
            $this->confCertificadoDigital = $this->ConfiguracionController->GetValue('nomcd');
            $this->confConceptoDOCCAE = $this->ConfiguracionController->GetValue('nomdoccae');
            
        }
    }

    /**
     * Establece en el controller las opciones de configuración para la generación. 
     * Por defecto si no viene informado se establece todo a false o nulo
     */
    private function SetOptionsFromPost($opciones)
    {
        //  Envío de e-mail al administrador tras generación
        // $this->envioEmailAdministrador = true; //(isset($opciones['envioEmailAdministrador']) ? $opciones['envioEmailAdministrador'] : false);
        //  Agrupar servicios en la misma factura
        $this->agruparServicios = (isset($opciones['agruparServicios']) ? $opciones['agruparServicios'] : false);
        //  Agrupar facturas para enviar en un ZIP
        // $this->agruparFacturasZip = true;//(isset($opciones['agruparFacturas']) ? $opciones['agruparFacturas'] : false);
        //  Envío de facturas individuales a la api de Tu Comunidad
        $this->envioAPI = (isset($opciones['envioAPI']) ? $opciones['envioAPI'] : false);

        //  Concepto CAE
        if(isset($opciones['conceptoCAE']))
        {
            $this->conceptoCAE = (trim($opciones['conceptoCAE']) !== '' ? $opciones['conceptoCAE'] : 'C. ANUAL ' . $this->confConceptoCAE);    
        }else{
            $this->conceptoCAE = $this->confConceptoCAE;
        }

        //  Concepto DOC CAE
        if(isset($opciones['conceptoDOCCAE']))
        {
            $this->conceptoDOCCAE = (trim($opciones['conceptoDOCCAE']) !== '' ? $opciones['conceptoDOCCAE'] : $this::NOMENCLATURA_DOCCAE);
        }else{
            $this->conceptoDOCCAE = $this::NOMENCLATURA_DOCCAE;
        }

        //  Concepto DPD
        if(isset($opciones['conceptoDPD']))
        {
            $this->conceptoDPD = (trim($opciones['conceptoDPD']) !== '' ? $opciones['conceptoDPD'] : 'C. ANUAL ' .$this->confConceptoDPD);    
        }else{
            $this->conceptoDPD = $this->confConceptoDPD;
        }

        //  Concepto Certificados digitales
        if(isset($opciones['conceptoCertificadoDigital']))
        {
            $this->conceptoCertificadosDigitales = (trim($opciones['conceptoCertificadoDigital']) !== '' ? $opciones['conceptoCertificadoDigital'] : 'C. ANUAL ' . $this->confCertificadoDigital);    
        }else{
            $this->conceptoCertificadosDigitales = $this->confCertificadoDigital;
        }

        //  Cuerpo del e-mail
        $this->emailBody = (isset($opciones['emailBody']) ? $opciones['emailBody'] : '');
    }

    /**
     * Create user
     * @param string $entidadPrincipal. Entity Name
     * @param json $datos. JSON Object with values to create
     */
    public function Create($entidadPrincipal, $datos)
    {
        return HelperController::errorResponse('error','Create Method Not Available', 200);       
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
            // return $this->InvoiceModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->InvoiceModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->InvoiceModel->Delete($id);
    }

    /**
     * Recupera una factura mediante su ID
     * @param int $id. ID de la factura
     * @return array Datos de la factura
     */
    public function Get($id)
    {
        
        $invoice = $this->InvoiceModel->Get($id);
        if(count($invoice['Invoice']) > 0)
        {

            $label = '';
            $color = '';

            //  Fecha de devolución formateado
            if(!is_null($invoice['Invoice'][0]['datereturned'])){
                $invoice['Invoice'][0]['datereturned'] = date('d-m-Y', strtotime($invoice['Invoice'][0]['datereturned']));            
            }else{
                $invoice['Invoice'][0]['datereturned'] = 'N/D';
            }
            //  Fecha de factura formateado
            $invoice['Invoice'][0]['dateinvoice'] = date('d-m-Y', strtotime($invoice['Invoice'][0]['dateinvoice']));
            //  Importe formateado
            $invoice['Invoice'][0]['total_taxes_inc'] = number_format($invoice['Invoice'][0]['total_taxes_inc'], 2,',','.');
            //  Estado factura
            $estado = $invoice['Invoice'][0]['estado'];

            switch ($estado)
            {
                case self::ESTADO_COBRADO:
                    $color = 'success';
                    $label = 'Cobrada';
                    break;                
                case self::ESTADO_FACTURAS_DEVUELTAS:
                    $color = 'danger';
                    $label = 'Devuelta';                    
                    break;
                case self::ESTADO_PENDIENTE:
                    $color = 'warning';
                    $label = 'Pendiente';                    
                    break;
                case self::ESTADO_FACTURA_RECTIFICATIVA;
                    $color = 'primary';
                    $label = 'Rectificativa';                
                    break;
            }
            $invoice['Invoice'][0]['lblestado'] = '<span class="badge px-3 bg-'.$color.'" style="font-size:14px;">'.$label.'</span>';
            //  Nombre del fichero
            $numero = $invoice['Invoice'][0]['numero'];
            $comunidad = $invoice['Invoice'][0]['comunidad'][0]['nombre'];
            $administrador = $invoice['Invoice'][0]['administrador'];
            $this->InvoiceModel->SetMes($invoice['Invoice'][0]['mes']);
            $this->InvoiceModel->SetAnyo($invoice['Invoice'][0]['anyo']);
            $invoice['Invoice'][0]['pdffile'] = $this->invoiceServer . 'pdf/' . $this->GeneratePDFFileName($numero, $comunidad, $administrador) . '.pdf';
            //  Remesa asociada
            $remesasAsociadas = $this->InvoiceModel->RemesaAsociada($id);
            if(count($remesasAsociadas) > 0)
            {
                $invoice['Invoice'][0]['remesapresentada'] = 'Sí';
            }else{
                $invoice['Invoice'][0]['remesapresentada'] = 'No';
            }

            //$invoice['Invoice'][0]['remesa'] = $remesasAsociadas;

            //  Total impuestos
            $totalTaxInc = HelperController::ConvertToFloat($invoice['Invoice'][0]['total_taxes_inc']);
            $totalTaxExc = HelperController::ConvertToFloat($invoice['Invoice'][0]['total_taxes_exc']);
            $totalTax = $totalTaxInc - $totalTaxExc;
            $invoice['Invoice'][0]['total_taxes'] = number_format($totalTax,2,',','.');
            //  % Taxes
            $invoice['Invoice'][0]['tax_rate'] = number_format($invoice['Invoice'][0]['tax_rate'],2,',','.');
            //  Subtotal
            $invoice['Invoice'][0]['total_taxes_exc'] = number_format($invoice['Invoice'][0]['total_taxes_exc'],2,',','.');
            //  Construimos la tabla de la vista de remesas
            $invoice['Invoice'][0]['table_remesa'] = $this->CreateRemesaTable($remesasAsociadas);
            //  Construimos la tabla de la vista de detalle de la factura
            $invoice['Invoice'][0]['table_detail'] = $this->CreateDetailTable($invoice['Invoice'][0]['invoicedetail']);

        }

        return $invoice;
    }

    public function GetIdByNumeroFactura($numeroFactura)
    {
        $id = $this->InvoiceModel->GetIdByNumero($numeroFactura);
        if(!is_null($id)){
            return $id;
        }else{
            return null;
        }
    }

    /**
     * Devuelve un listado de la entidad de facturas
     * @return Array Listado 
     */
    public function List($params = null)
    {
        $limitStart = (isset($params['start']) ? $params['start'] : null);
        $limitLength = (isset($params['length']) ? $params['length'] : null);        
        $orderType = 'asc';

        //  Parámetros de búsqueda
        if(isset( $params['search']['value'] ))
        {
            $params['searchfields'] =[];
            $params['searchvalue'] = $params['search']['value'];

            //  Número de factura
            $searchCondition = [
                'field' => 'numero',
                'operator' => '%'
            ];
            $params['searchfields'][] = $searchCondition;
            
            //  Administrador
            $searchCondition = [
                'field' => 'administrador',
                'condition' => 'or',
                'operator' => '%'
            ];
            $params['searchfields'][] = $searchCondition;    

            //  Nombre de la comunidad
            $searchCondition = [
                'field' => 'comunidad',
                'condition' => 'or',
                'operator' => '%'
            ];
            $params['searchfields'][] = $searchCondition;                
            
            //  Nombre de la comunidad
            $searchCondition = [
                'field' => 'iban',
                'condition' => 'or',
                'operator' => '%'
            ];
            $params['searchfields'][] = $searchCondition;  

        }

        return $this->InvoiceModel->List($params, false);

    }

    /**
     * Listados auxiliares: Pendientes y Emitidas
     * @param string $estado Estado de la factura: P, C, D
     * @return object Listado
     */
    public function Listado($estado)
    {
        $estado = DatabaseCore::PrepareDBString($estado);
        $params['filterfield'] = 'estado';
        $params['filtervalue'] = "'$estado'";
        $params['filteroperator'] = '=';

        return $this->InvoiceModel->List($params, false);
    }

    /**
     * Carga el administrador seleccionado o todos
     * @param int $idAdministrador (opcional). Default: null (Todos)
     */
    private function Administradores($idAdministrador = -1)
    {

        $result = true;
        

        if(intval($idAdministrador) <= 0){
            $administradores = $this->UsuarioController->ListAdministradoresFincas();
            $this->InvoiceModel->SetAdministradores( $administradores );
        }else{

            //  Comprobamos si es un usuario autorizado ya que si es un usuario autorizado no está permitido que se le facture
            $administradores = [];
            $administrador = $this->UsuarioController->Get($idAdministrador);
            if(count($administrador['Usuario']) <= 0)
            {
                return false;
            }else{
                $administradores['Usuario'] = $administrador['Usuario'];
                $this->InvoiceModel->SetAdministradores($administradores);
            }
        }

        return $result;

    }

    /**
     * Proceso que simula la facturación para un administrador y un mes en concreto
     */
    public function SimularFacturacion($data)
    {
        //  Validamos que esté informado al menos el mes y el ID del administrador
        if(!isset($data['mesfacturacion']) && !isset($data['idadministrador'])){
            return 'Los parámetros enviados no son correctos';
        }

        $simulacionGenerada = false;

        //  Validamos que el mes de facturación y el id sean numéricos
        $mesFacturacion = DatabaseCore::PrepareDBString($data['mesfacturacion']);
        $idAdministrador = DatabaseCore::PrepareDBString($data['idadministrador']);

        //  Recuperamos el administrador
        //  Seteamos el administrador en el modelo
        $this->InvoiceModel->SetIdAdministrador($idAdministrador);
        //  Administradores
        $administradores = $this->Administradores( $this->InvoiceModel->IdAdministrador() );        

        if(count($this->InvoiceModel->Administradores()['Usuario']) > 0){
            $administradores = $this->InvoiceModel->Administradores()['Usuario'];
        }else{
            return 'El administrador seleccionado no existe';
        }

        ////////////////////////////////////////////////////////////////////////////////////
        ///                             INICIO PROCESO
        ////////////////////////////////////////////////////////////////////////////////////        
        $fileName = 'FINCATECH_FACTURACION_SIMULACION_' . str_pad($mesFacturacion, 2,'0') . '_' . date('Y') . '.xlsx';

        //  Año de facturación
        $anyoFacturacion = date('Y');
        $this->iAnyoFacturacion = (int)$anyoFacturacion;
        //  Servicios
        $servicios = null;
        //  Mes Facturación
        $mesFacturacion = DatabaseCore::PrepareDBString($mesFacturacion);
        $this->iMesFacturacion = (int)$mesFacturacion;

        ////////////////////////////////////////////////////////////////////////////////////
        ///                             SERVICIOS
        ////////////////////////////////////////////////////////////////////////////////////
        //  Servicios que se van a facturar para el administrador seleccionado
        if(count($data['servicios']) > 0){
            $servicios = $data['servicios'];
        }else{
            $servicios = $this->InvoiceModel->IdsServiciosContratados();
            if(count($servicios) > 0)
            {
                $servicios = array_column($servicios, 'idservicio');
                $this->InvoiceModel->SetServices($servicios);
            }else{
                $this->InvoiceModel->SetServices(null);
            }
        }

        //  Si no hay servicios para facturar avisamos y salimos del proceso
        if(is_null($this->InvoiceModel->Services())){
            return 'Este administrador no tiene servicios contratados para facturar';
        }

        ////////////////////////////////////////////////////////////////////////////////////
        //  Establecemos el impuesto
        ////////////////////////////////////////////////////////////////////////////////////
        $this->InvoiceModel->SetTaxRate($this->impuesto);

        //  Iteramos sobre todos los posibles administradores que haya seleccionado el usuario que puede ser 1 ó todos
        foreach($administradores as $administrador)
        {
           
            //  ID del administrador
            $idAdministrador = $administrador['id'];

            $this->InvoiceModel->SetIdAdministrador($idAdministrador);
            $this->InvoiceModel->SetEmail($administrador['email']);
            $this->InvoiceModel->SetEmailAdministrador($administrador['email']);
            $this->InvoiceModel->SetCifAdministrador($administrador['cif']);

            $administradorNombre = $administrador['nombre'];

            //  Comprobamos si el usuario es un admin autorizado y un usuario de tipo administrador
            $authorizedAdmin = $this->UsuarioController->IsAuthorizedUserByAdmin($idAdministrador);
            $roleId = $administrador['rolid'];

            //  Si el usuario es de facturación
            if($roleId == 5 && $authorizedAdmin === false)
            {
                //  Establecemos el administrador en la propiedad del controller
                $this->administrador = $administrador;
                //  Recuperamos las posibles comunidades que tenga el administrador
                $comunidades = $this->ComunidadController->ListComunidadesWithServicesByAdministradorId($idAdministrador, $mesFacturacion, $this->InvoiceModel->Services());
                //  Si tiene comunidades, comenzamos con la facturación de las comunidades del administrador susceptibles de ser facturadas
                if( count($comunidades) > 0)
                {
                    //  Agrupamos los servicios contratados por cada comunidad del administrador
                    $this->AgruparServiciosComunidades($comunidades);
                    $iTotalComunidades = count($this->comunidadesFacturacion);
                    $iComunidad = 1;

                    //  Si hay comunidades para procesar comenzamos a facturar
                    if( $iTotalComunidades > 0)
                    {
                        $comunidadesFacturacion = $this->comunidadesFacturacion;

                        //  Iteramos sobre todas las comunidades que correspondan al administrador que se está facturando
                        foreach($comunidadesFacturacion as $comunidad)
                        {

                            $this->comunidadToBill = $comunidad;                       

                            //  Comprobamos si la comunidad tiene servicios asociados
                            if(isset($comunidad['services']))
                            {
                                //  Comprobamos si tiene servicios pendientes de facturar
                                if( count($comunidad['services']) > 0 )
                                {
                                    $this->InvoiceModel->SetComunidad($comunidad['nombre'])
                                    ->SetIdComunidad( trim($comunidad['id']) )
                                    ->SetCifComunidad( trim($comunidad['cif']) )
                                    ->SetIBAN($comunidad['ibancomunidad'])
                                    ->SetIdAdministrador($idAdministrador)
                                    ->SetAdministrador($administradorNombre)
                                    ->SetMes($mesFacturacion)
                                    ->SetAnyo($anyoFacturacion)
                                    ->SetCodigoComunidad($comunidad['codigo'])
                                    ->SetServices( $comunidad['services'] );
                                }                     
                            }
                            $iComunidad++;
                        }

                        //  Generamos el fichero Excel para devolver al usuario
                        $base64Excel = $this->ProcessExcelSimulacion($fileName, $comunidadesFacturacion);
                        if($base64Excel === false){
                            return 'El fichero de simulación no ha podido generarse';
                        }else{
                            $simulacionGenerada = true;
                        }
                    }else{
                        return 'Este administrador no tiene comunidades para facturar en el período seleccionado';
                    }
                }
            }
        }

        if( $simulacionGenerada ){
            return [
                'base64'    => $base64Excel,
                'filename'  => $fileName,
                'type'      => 'excel'
            ];
    
        }else{
            return 'Este administrador no tiene comunidades para facturar en el período seleccionado';
        }


    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                 PROCESOS DE FACTURACION
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Genera en el sistema una factura rectificativa de una factura
     * @param int $idinvoice. ID de la factura original
     * @param array $data. Datos de la factura que se va a generar
     */
    public function CreateInvoiceRectificativa($idinvoice, $data)
    {
        //  ID factura original
        $idInvoice = DatabaseCore::PrepareDBString($idinvoice);
        //  Primero comprobamos si existe ya una factura rectificativa y si existe la factura para la que quiere hacer la rectificativa
        $invoice = $this->InvoiceModel->Get($idinvoice);
        //  Concepto
        $concepto = DatabaseCore::PrepareDBString($data['concepto']);
        //  Importe impuestos excluidos
        $totalTaxesExc = DatabaseCore::PrepareDBString($data['importe']);
        //  Enviar Email
        $enviarEmail = (int)DatabaseCore::PrepareDBString($data['enviaremail']) == 0 ? false : true;

        if($totalTaxesExc == ''){
            $totalTaxesExc = $this->InvoiceModel->TotalTaxesExc();
        }else{
            $totalTaxesExc = HelperController::ConvertToFloat($totalTaxesExc);
        }

        //  Cuerpo del E-Mail
        $cuerpoEmail = $data['cuerpo'];

        if($this->InvoiceModel->Id() > 0)
        {

            //  Validamos que la factura no tenga ya una factura rectificativa
            if(!is_null($this->InvoiceModel->IdRectificativa())){
                return HelperController::errorResponse('error','Esta factura ya tiene asociada una factura rectificativa por lo tanto no se puede generar una nueva', 200);
            }else{

                //  Concepto
                if($concepto == ''){
                    $concepto = "Fra. Rectificativa s/" . $this->InvoiceModel->Numero();
                }
        
                // % IVA
                $taxRate = $this->InvoiceModel->TaxRate();
                //  Total Impuestos Incluidos
                //  Si el importe viene vacío, utilizamos el importe total de la factura, en caso contrario utilizamos el proporcionado por el usuario

                $totalTaxesInc = (float)$totalTaxesExc + ((float)$totalTaxesExc * ((float)$taxRate / 100));
                //  Instanciamos el controller de facturas rectificativas
                $InvoiceRectificativaController = new InvoiceRectificativaController(null);
                //  Email de destino
                $InvoiceRectificativaController->emailBody = $cuerpoEmail;
                //  Número de factura original
                $InvoiceRectificativaController->facturaOriginalNumero = $this->InvoiceModel->Numero();
                //  Fecha factura original
                $InvoiceRectificativaController->facturaOriginalFecha = date('d/m/Y', strtotime($this->InvoiceModel->DateInvoice()));
                //  Llenamos la entidad con los datos factura rectificativa según la factura
                $rectificativa = new InvoiceRectificativa();
                $rectificativa->SetConcepto($concepto)
                ->SetIdInvoice($idInvoice)
                ->SetTotalTaxesExc((float)$totalTaxesExc * -1)
                ->SetTotalTaxesInc((float)$totalTaxesInc * -1)
                ->SetTaxRate((float)$taxRate)
                ->SetIdComunidad($this->InvoiceModel->IdComunidad())
                ->SetComunidad($this->InvoiceModel->Comunidad())
                ->SetIdAdministrador($this->InvoiceModel->IdAdministrador())
                ->SetAdministrador($this->InvoiceModel->Administrador())
                ->SetEmail($this->InvoiceModel->Email());
                //  Creamos la factura rectificativa con los datos recibidos
                $InvoiceRectificativaController->Insert($rectificativa, $enviarEmail);
                $idFacturaRectificativa = $rectificativa->Numero();
                //  Actualizamos el ID de la factura rectificativa en la factura original
                $this->InvoiceModel->SetIdRectificativa($rectificativa->Id());
                $this->InvoiceModel->setId($idInvoice);
                $this->InvoiceModel->AssignIdFacturaRectificativa();
            }

        }else{
            //  Devolvemos error que la factura no se ha encontrado en el sistema
            return HelperController::errorResponse('error','La factura con ID: ' . $data['idinvoice'] . ' no se ha encontrado en el sistema.', 200);
        }

        //  Si el proceso ha sido correcto, devolvemos el estado
        if($idFacturaRectificativa !== false){

            //  Asignamos el ID de la factura rectificativa a la factura original
            $this->InvoiceModel->AssignIdFacturaRectificativa();
            //  Creamos el PDF de la factura rectificativa

            //  Sincronizamos el fichero con el servidor

        }else{
            return HelperController::errorResponse('error','La factura rectificativa no ha podido generarse. Si el problema persiste, por favor, contacte con <a href="mailto:desarrollo@fincatech.es" target="_blank" title="Enviar e-mail soporte">desarrollo@fincatech.es</a>', 200);
        }
        //  Devolvemos el estado de creación al usuario
        $result = $idFacturaRectificativa;
        return HelperController::successResponse($result);
    }
    
    /**
     * Genera la facturación para los parámetros seleccionados
     * @param object $opciones
     * @param int|null $mesFacturacion (optional).  Mes de facturación. Defaults: Null
     * @param int|null $idAdministrador (optinoal). ID del administrador. Defaults: Null
     * @return string Información del estado de la generación
     */
    public function GenerarFacturacion($opciones, $mesFacturacion = null, $idAdministrador = -1)
    {

        global $appSettings;

        //  Establecemos las opciones de generación
        $this->SetOptionsFromPost($opciones);

        //  Validamos los parámetros de configuración que vengan especificados en el request
        $this->ValidateConfiguracionFromRequest();

        //  Año de facturación
        $anyoFacturacion = date('Y');
        $this->iAnyoFacturacion = (int)$anyoFacturacion;
        //  Servicios
        $servicios = null;
        //  Resultado. Se utiliza para enviar información en formato HTML al buffer de salida
        $result = '';
        //  Facturas generadas
        $iFacturasGeneradas = 0;

        //  Con los parámetros correspondientes comprobamos el estado de la facturación
        $mesFacturacion = DatabaseCore::PrepareDBString($opciones['mesfacturacion']);
        $this->iMesFacturacion = (int)$mesFacturacion;

        $sMesFacturacion = HelperController::StringMonth((int)$mesFacturacion);
        
        ////////////////////////////////////////////////////////////////////////////////////
        ///                             ADMINISTRADOR
        ////////////////////////////////////////////////////////////////////////////////////       
        //  ID del administrador
        $idAdministrador = DatabaseCore::PrepareDBString($opciones['idadministrador']);
        //  Seteamos el administrador en el modelo
        $this->InvoiceModel->SetIdAdministrador($idAdministrador);
        //  Administradores
        $administradores = $this->Administradores( $this->InvoiceModel->IdAdministrador() );
        if($administradores === false)
            return HelperController::errorResponse('error','El administrador con ID ' . $idAdministrador . ' no es válido');

        ////////////////////////////////////////////////////////////////////////////////////
        ///                             BANCO
        ////////////////////////////////////////////////////////////////////////////////////
        //  ID del banco
        $idBanco = DatabaseCore::PrepareDBString($opciones['idBanco']);
        //  Recuperamos el banco según el ID que venga informado
        $banco = $this->BankController->Get($idBanco);
        if(count($banco['Bank']) > 0){
            $this->banco = $banco['Bank'][0];
        }
        //  Recuperamos el nombre del banco
        $bancoNombre = $this->banco['nombre'];

        ////////////////////////////////////////////////////////////////////////////////////
        ///                             SERVICIOS
        ////////////////////////////////////////////////////////////////////////////////////
        //  Servicios que se van a facturar para el administrador seleccionado
        if(count($opciones['servicios']) > 0){
            $servicios = $opciones['servicios'];
        }else{
            $servicios = $this->InvoiceModel->IdsServiciosContratados();
            if(count($servicios) > 0)
            {
                $servicios = array_column($servicios, 'idservicio');
                $this->InvoiceModel->SetServices($servicios);
            }else{
                $this->InvoiceModel->SetServices(null);
            }
        }

        //  Si no hay servicios para facturar avisamos y salimos del proceso
        if(is_null($this->InvoiceModel->Services())){
            return HelperController::errorResponse('error','Este administrador no tiene servicios contratados para facturar');
        }

        //  Descripción servicios
        $serviciosNombre = array();
        foreach($this->InvoiceModel->Services() as $key => $value)
        {
            array_push($serviciosNombre, $this->ServiceNameById($value));
        }

        ////////////////////////////////////////////////////////////////////////////////////
        ///                             INICIO PROCESO
        ////////////////////////////////////////////////////////////////////////////////////
        ob_start();

        header('Cache-Control: no-cache');
        header('Content-Type: text/event-stream');
        header('Connection: keep-alive');

        //  Iteramos sobre todos los administradores
        $administradores = $this->InvoiceModel->Administradores()['Usuario'];

        //  Validamos que tenga el e-mail de facturación informado ya que si no hay que parar el proceso y avisar al usuario
        if(is_null($administradores[0]['emailfacturacion']) || trim($administradores[0]['emailfacturacion']) == '')
            return HelperController::errorResponse('error','El administrador seleccionado no tiene configurado el e-mail de facturación');

        $totalAdministradores = count($administradores);

        $iAdministrador = 1; // Variable para el contador del administrador que se está procesando

        //  Inicializamos el array de id's de facturas que se van a incluir en la remesa de facturación de este lote
        $this->invoiceIds = [];

        //  Establecemos el impuesto
        $this->InvoiceModel->SetTaxRate($this->impuesto);

        //  Iteramos sobre todos los posibles administradores que haya seleccionado el usuario que puede ser 1 ó todos
        foreach($administradores as $administrador)
        {
            //  Inicializamos los id's para la generación de pdf
            $this->invoicePdfIds = [];
            
            //  ID del administrador
            $idAdministrador = $administrador['id'];

            $this->InvoiceModel->SetIdAdministrador($idAdministrador);
            $this->InvoiceModel->SetEmail($administrador['email']);
            $this->InvoiceModel->SetEmailAdministrador($administrador['email']);
            $this->InvoiceModel->SetCifAdministrador($administrador['cif']);

            $administradorNombre = $administrador['nombre'];

            //  Comprobamos si el usuario es un admin autorizado y un usuario de tipo administrador
            $authorizedAdmin = $this->UsuarioController->IsAuthorizedUserByAdmin($idAdministrador);
            $roleId = $administrador['rolid'];

            //  Si el usuario es de facturación
            if($roleId == 5 && $authorizedAdmin === false)
            {
                //  Establecemos el administrador en la propiedad del controller para posteriormente utilizarlo en la generación SEPA
                $this->administrador = $administrador;
                //  Recuperamos las posibles comunidades que tenga el administrador
                $comunidades = $this->ComunidadController->ListComunidadesWithServicesByAdministradorId($idAdministrador, $mesFacturacion, $this->InvoiceModel->Services());
                //  Si tiene comunidades, comenzamos con la facturación de las comunidades del administrador susceptibles de ser facturadas
                if( count($comunidades) > 0)
                {
                    //  Agrupamos los servicios contratados por cada comunidad del administrador
                    $this->AgruparServiciosComunidades($comunidades);
                    $iTotalComunidades = count($this->comunidadesFacturacion);
                    $iComunidad = 1;

                    //  Si hay comunidades para procesar comenzamos a facturar
                    if( $iTotalComunidades > 0)
                    {

                        $comunidadesFacturacion = $this->comunidadesFacturacion;

                        //  Iteramos sobre todas las comunidades que correspondan al administrador que se está facturando
                        foreach($comunidadesFacturacion as $comunidad)
                        {

                            $this->comunidadToBill = $comunidad;                       

                            //  Comprobamos si la comunidad tiene servicios asociados
                            if(isset($comunidad['services']))
                            {
                                //  Comprobamos si tiene servicios pendientes de facturar
                                if( count($comunidad['services']) > 0 )
                                // if( !$this->ExistsInvoice() )
                                {

                                    $this->InvoiceModel->SetComunidad($comunidad['nombre'])
                                    ->SetIdComunidad( trim($comunidad['id']) )
                                    ->SetCifComunidad( trim($comunidad['cif']) )
                                    ->SetIBAN($comunidad['ibancomunidad'])
                                    ->SetIdAdministrador($idAdministrador)
                                    ->SetAdministrador($administradorNombre)
                                    ->SetMes($mesFacturacion)
                                    ->SetAnyo($anyoFacturacion)
                                    ->SetCodigoComunidad($comunidad['codigo'])
                                    ->SetServices( $comunidad['services'] )
                                    ->SetEstado($this::ESTADO_PENDIENTE);
        
                                    //  Creamos la factura en el sistema
                                    if($this->CreateInvoice() !== false)
                                        $iFacturasGeneradas++;

                                    //  Enviamos el progreso actualizado del proceso por Comunidad
                                    $this->SendProgressResultMessage($iTotalComunidades, $iComunidad, $administradorNombre, $sMesFacturacion, $this->InvoiceModel->Comunidad(), null, $this->InvoiceModel->TotalTaxesInc() . '&euro;');
                                }else{
                                    //TODO Registramos en el error que ya se ha encontrado una factura y recuperamos la información de la misma
                                    //  para poder mostrar la información al usuario en el log de errores

                                }                     
                            }
                            $iComunidad++;
                        }

                        //  Por cada administrador generamos los pdf's correspondientes a la factura
                        $this->GenerarFacturasPDF();

                    }
                    
                    //  Enviamos el progreso actualizado del proceso por Administrador
                    $this->SendProgressResultMessage($totalAdministradores, $iAdministrador, $administradorNombre, $sMesFacturacion, null, null, $this->InvoiceModel->TotalTaxesInc());
                    
                }

            }
            $iAdministrador++;
        }

        $estado = $this->EstadoFacturacion();

        //  Si el estado es que no hay nada pendiente de facturar, avisamos al usuario también
        if($estado['pendientes'] == 0)
        {

            $result = '<span class="d-block text-center text-danger font-weight-bold my-2">No hay facturas pendientes para el período y parámetros seleccionados</span>';
            $result .= '<strong>Administrador</strong>: ' . $this->InvoiceModel->Administrador() . '<br>';
            $result .= '<strong>Servicio/s</strong>: ' . implode(', ', $serviciosNombre) . '<br>';
            $result .= '<strong>Mes de facturación</strong>: ' . HelperController::StringMonth($mesFacturacion) . ' ' . date('Y') . '<br>';
            $result .= '<strong>Banco utilizado para la remesa</strong>: ' . $bancoNombre . ' - Cuenta: ' . $this->banco['iban'] . '<br>';

        }else{

            //  Generamos la remesa siempre y cuando se hayan generado facturas
            if(count($this->invoiceIds) > 0){
                $this->GenerarRemesaFacturasGeneradas();
            }

            $result = '<p class="text-center">El proceso de facturación ha finalizado</p>';
            //  Mostramos el mensaje final al usuario si se han generado facturas
            if($iFacturasGeneradas > 0){
                $result .= '<p class="mb-0"><i class="bi bi-journal-check text-success"></i> Se han generado ' . count($this->invoiceIds) . ' facturas por un importe total de ' . number_format((float)$this->totalFacturado, 2, ',','.') . '&euro;</p>';
            }else{
                $result = '<span class="d-block text-center text-danger font-weight-bold my-2">No hay facturas pendientes para el período y parámetros seleccionados</span>';
                $result .= '<strong>Administrador</strong>: ' . $this->InvoiceModel->Administrador() . '<br>';
                $result .= '<strong>Servicio/s</strong>: ' . implode(', ', $serviciosNombre) . '<br>';
                $result .= '<strong>Mes de facturación</strong>: ' . HelperController::StringMonth($mesFacturacion) . ' ' . date('Y') . '<br>';
                $result .= '<strong>Banco utilizado para la remesa</strong>: ' . $bancoNombre . ' - Cuenta: ' . $this->banco['iban'] . '<br>';     
            }
       
            //  Fichero zip
            if($this->zipFileName !== ''){
                $sepaFile = $this->xmlSepaName;
                $result .= '<p class="mb-0"><i class="bi bi-file-earmark-zip text-success"></i> <a href="'. $this->invoiceServer . 'zip/'. $this->zipFileName . '" target="_blank" download>Descargar Fichero ZIP con las facturas generadas</a>';            
            }
    
            //  Fichero de la remesa si ha sido generada ya que no interesa mostrar el enlace en otras situaciones
            if($this->remesaGenerada){
                $remesaPath = HelperController::RootURL() . '/public/storage/remesas/' . $this->ficheroRemesa;
                $result .= '<p class="mb-0"><i class="bi bi-cloud-arrow-down text-success"></i> <a href="'.$remesaPath.'" target="_blank" download>Descargar fichero remesa</a></p>';
            }

            if(!$this->debugMode)
            {
                //  Enviamos el e-mail del proceso al administrador de fincas
                $this->SendEmailProcesoFacturacionAdministrador();
                //  Enviamos un informe al sudo del sistema con la información de la facturación generada ¿?
                $this->SendEmailProcesoFacturacion();
            }

        }

        //  Si han ocurrido errores durante el proceso mostramos la información al usuario para que pueda descargarse el fichero
        if($this->haveError){
            $result .= '<p class="my-2"><i class="bi bi-x-octagon text-danger"></i> Se han producido ' . $this->iErrores . ' error(es) durante el proceso. ';
            $result .= '<a href="' . HelperController::RootURL() . $this->erroresFileName . '" class="text-danger" target="_blank" download>Ver log de errores</a></p>';
        }

        //  Fin del proceso
        $this->SendProgressResultCustomMessage(100, 100, $result);
        //HelperController::sendProgressResponse(100, 100, 'Proceso completado', false);
        ob_flush();
        flush();
        ob_end_flush();
        
        return $result;

    }

    /**
     * Crea una factura para un administrador y comunidad para los servicios especificados
     */
    private function CreateInvoice()
    {
        $totalFactura = 0;
        $totalImpuestos = 0;
        $serviciosFacturarComunidad = $this->InvoiceModel->Services();
        $agruparServicios = $this->agruparServicios;

        //  Si el IBAN de la comunidad no es válido, añadimos al log de errores para mostrar al usuario
        if(!HelperController::ValidateIban( $this->InvoiceModel->IBAN() ))
        {
            $mensaje = 'Administrador: ' . $this->InvoiceModel->Administrador() . PHP_EOL;
            $mensaje .= 'El código IBAN [' . $this->InvoiceModel->IBAN() . ']';
            $mensaje .= ' de la comunidad ' . $this->InvoiceModel->CodigoComunidad() . ' - ' . strtoupper($this->InvoiceModel->Comunidad()) . ' ';
            $mensaje .= trim($this->InvoiceModel->IBAN()) == '' ? ' no está informado' : ' no es válido';
            $this->AddErrorToLog($mensaje);
            return false;
        }

        //  Validamos que el IBAN tenga código BIC/Swift asociado
        $bicIBAN = HelperController::GetBICFromIBAN(trim($this->InvoiceModel->IBAN()));
        if($bicIBAN == '' || is_null($bicIBAN))
        {
            $mensaje = 'Administrador: ' . $this->InvoiceModel->Administrador() . PHP_EOL;
            $mensaje .= 'El código IBAN [' . $this->InvoiceModel->IBAN() . ']';
            $mensaje .= ' de la comunidad ' . $this->InvoiceModel->CodigoComunidad() . ' - ' . strtoupper($this->InvoiceModel->Comunidad()) . ' ';
            $mensaje .= ' no es válido y/o el código BIC/Swift no se ha podido recuperar';
            $this->AddErrorToLog($mensaje);
            return false;  
        }

        //  Establecemos la fecha de la factura
        $fechaFactura = date('Y-m-d');
        $this->InvoiceModel->SetDateInvoice($fechaFactura);

        //  Serie de facturación FAño/Serie. Ej: F24/00001
        $numeroFactura = $this->GenerateNumeroFactura();
        $this->InvoiceModel->SetNumero($numeroFactura);

        //  Inicializamos el detalle
        $this->InvoiceDetailController->ClearDetailLines();
        //  Recalculamos el total de la factura para inicializar a 0
        $this->CalculateTotals();

        //  Creamos la nueva factura siempre y cuando haya servicios para facturar
        if( count($serviciosFacturarComunidad) > 0)
        {

            //  Necesitamos conocer el ID de la última factura generada para poder crear la relación entre el detalle y la factura
            $invoiceId = $this->InvoiceModel->GetNextId('invoice');

            for($iServicio = 0; $iServicio < count($serviciosFacturarComunidad); $iServicio++)
            {

                $servicio = $serviciosFacturarComunidad[$iServicio];
                //  Recuperamos el ID del servicio
                $idServicio = (int)$servicio['idservicio'];
                //  Recuperamos el precio del servicio
                $precioComunidad = (float)$servicio['preciocomunidad'];
                //  Precio del administrador
                $precioServicio = (float)$servicio['precio'];
                //  Concepto para incluir en el detalle
                $detalle = $this->GenerarDetalleLinea($idServicio);
                
                //  Añadimos la línea al modelo. 
                $this->AddDetailLine($invoiceId, $idServicio, $detalle, $precioServicio, 1, $this->impuesto, $precioComunidad);

                //  Si no requiere agrupación de servicios guardamos tanto la factura como el detalle asociado
                if(!$agruparServicios)
                {
                    //  Recuperamos el valor de la referencia de contrato
                    $this->InvoiceModel->SetReferenciaContrato( $this->GenerarReferenciaContrato($idServicio) );
                    //  Guardamos tanto el detalle como la factura
                    $this->SaveInvoice();
                    //  Limpiamos el detalle anterior
                    $this->InvoiceDetailController->ClearDetailLines();                        
                    //  Recuperamos el siguiente ID para poder hacer la relación
                    $invoiceId = $this->InvoiceModel->GetNextId('invoice');
                    //  Añadimos el ID de la factura para la posterior generación de la remesa
                    $this->invoiceIds[] = $this->InvoiceModel->Id();  
                    $this->invoicePdfIds[] =  $this->InvoiceModel->Id();  
                    // TOFIX: Mirar a ver para refactorizar y sacar a método propio
                    //  Serie de facturación FAño/Serie. Ej: F24/00001 
                    $this->InvoiceModel->SetNumero($this->prefijoSerieFacturacion . date('y') . '/' . str_pad($this->serieFacturacion, 6, '0', STR_PAD_LEFT) );

                }

            }
                
            //  Si está seleccionada la opción de agrupar los servicios en una misma factura guardamos en este punto toda la información
            if($agruparServicios){
                //  Establecemos la referencia del contrato
                $this->InvoiceModel->SetReferenciaContrato( $this->GenerarReferenciaContrato( $serviciosFacturarComunidad, true) );
                //  Guardamos tanto el detalle como la factura
                $this->SaveInvoice();
                //  Añadimos el ID de la factura para la posterior generación de la remesa
                $this->invoiceIds[] = $this->InvoiceModel->Id();
                $this->invoicePdfIds[] =  $this->InvoiceModel->Id();  
            }

        }

    }

    /**
     * Agrega una línea de detalle al modelo
     */
    private function AddDetailLine($idinvoice, $idservicio, $detail, $unit_price_tax_exc, $quantity, $tax_rate, $unitPriceComunidad)
    {

        //  Hay que coger el precio que está marcado para la comunidad
        $unit_price_tax_exc = $unitPriceComunidad;

        //  Calculamos el total sin impuestos
        $totalTaxesExc = HelperController::Redondeo($unit_price_tax_exc * $quantity);

        //  Calculamos el total por artículo impuestos
        $totalImpuestos = HelperController::Redondeo(($unit_price_tax_exc * ($tax_rate / 100)));
        $unit_price_tax_inc = $unit_price_tax_exc + $totalImpuestos;
        $unit_price_tax_inc = HelperController::Redondeo($unit_price_tax_inc);

        //  Calculamos el total con impuestos incluidos
        $totalTaxesInc = ($unit_price_tax_inc * $quantity);
        $totalTaxesInc = HelperController::Redondeo($totalTaxesInc);

        $detailLine = [];
        $detailLine['idinvoice'] = $idinvoice;
        $detailLine['idservicio'] = $idservicio;
        $detailLine['detail'] = $detail;
        $detailLine['unit_price_tax_inc'] = $unit_price_tax_inc;
        $detailLine['unit_price_tax_exc'] = $unit_price_tax_exc;
        $detailLine['quantity'] = $quantity;
        $detailLine['tax_rate'] = $tax_rate;
        $detailLine['total_taxes_exc'] = $totalTaxesExc;
        $detailLine['total_taxes_inc'] = $totalTaxesInc;
        $detailLine['unit_price_comunidad'] = $unitPriceComunidad;

        //  Añadimos el detalle al modelo
        $this->InvoiceDetailController->AddDetailLine($detailLine);
        //  Recalculamos los totales de la factura
        $this->CalculateTotals();
    }

    /** 
     * Genera la factura y su detalle en el sistema
     */
    private function SaveInvoice()
    {
        //  Guardamos los datos de la factura
        $invoiceId = $this->InvoiceModel->_Save();
        
        if((int)$invoiceId['id'] > 0)
        {
            //  Seteamos el ID de la factura que se acaba de generar en el sistema
            $this->InvoiceModel->setId((int)$invoiceId['id']);
            //  Actualizamos el total de facturación generada
            $totalTaxesInc = (float)str_replace(',','.', $this->InvoiceModel->TotalTaxesInc());
            $tmpTotalFacturado = (float)str_replace(',','.', $this->totalFacturado);
            $this->totalFacturado = (float)$totalTaxesInc + (float)$tmpTotalFacturado;
            //  Guardamos todo el detalle asociado a la factura
            $this->InvoiceDetailController->Save();
            //  Actualizamos el número de la serie de facturación
            $this->serieFacturacion++;
            $this->UpdateSerieFacturacion(); 
        }
    }

    /**
     * Calcula el total en función de las líneas de detalle
     */
    private function CalculateTotals()
    {

        $totalTaxesExc = 0;
        $totalTaxesInc = 0;

        //  Recuperamos todas las líneas de detalle 
        $detailLines = $this->InvoiceDetailController->DetailLines();

        //  Calculamos el bruto por cada línea de detalle y lo acumulamos en el total
        for($iDetail = 0; $iDetail < count($detailLines); $iDetail++)
        {
            //$totalTaxesExc += (float)((float)$detailLines[$iDetail]['unit_price_tax_inc'] * (float)$detailLines[$iDetail]['quantity']);
            $totalTaxesExc += (float)str_replace(',', '.', $detailLines[$iDetail]['total_taxes_exc']);
            $totalTaxesInc += (float)str_replace(',', '.', $detailLines[$iDetail]['total_taxes_inc']);

        }
        
        $totalTaxesInc = HelperController::Redondeo($totalTaxesInc);

        //  Aplicamos el redondeo siempre y cuando el importe sea superior a 0
        if($totalTaxesExc > 0){
            $totalTaxesInc = HelperController::Redondeo($totalTaxesInc);
        }

        if($totalTaxesInc > 0){
            $totalTaxesInc = HelperController::Redondeo($totalTaxesInc);
        }

        $this->InvoiceModel->SetTotalTaxesExc($totalTaxesExc)->SetTotalTaxesInc($totalTaxesInc);

    }

    /**
     * Actualiza el estado de liquidación de una factura
     */
    public function UpdateStatusLiquidacion(int $idinvoice, bool $newStatus)
    {
        $this->InvoiceModel->SetId($idinvoice)
        ->SetLiquidada($newStatus)
        ->ChangeLiquidacionStatus();
    }

    /**
     * Actualiza el estado de una factura
     */
    public function UpdateStatusInvoice(int $idInvoice, string $newStatus, string $dateReturned = null)
    {
        $this->InvoiceModel->SetId($idInvoice)->SetEstado($newStatus)->SetDateReturned($dateReturned)->UpdateStatus();
    }

    /**
     * Actualiza el estado de devolución de una factura
     */
    public function UpdateRejectionStatus(float $idInvoice, string $dateReturned, string $motivo)
    {
        $this->InvoiceModel->SetDateReturned($dateReturned)
        ->SetReturnedMessage($motivo)
        ->setId($idInvoice)
        ->UpdateRejection();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                          REMESAS
    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Genera el fichero SEPA con la información de las facturas generadas para el administrador
     */
    private function GenerarRemesaFacturasGeneradas()
    {
        ///////////////////////////////////////////////
        //          Nomenclatura fichero SEPA 
        ///////////////////////////////////////////////
        //  FINCA_SEPA_NB_MM_YYYY.XML
        //  + AD: Nombre del administrador saneado. Máximo 50 caracteres incluyendo _
        //  + NB: Nombre del banco saneado
        //  + MM: Mes 2 dígitos
        //  + YYYY: Año 4 dígitos
        //
        ////////////////////////////////

        //  Componemos el nombre del fichero SEPA
        $admin = HelperController::GenerarLinkRewrite($this->administrador['nombre']);
        if(strlen($admin) > 50)
            $admin = substr($admin, 0, 50);

        $tmpFile = 'FINCA_SEPA_' . HelperController::GenerarLinkRewrite($this->administrador['nombre']);
        $tmpFile .= '_' . str_pad(date('m', time()), 2, '0', STR_PAD_LEFT) . '_' . date('Y') . '_' . date('d_H_i_s'). '.xml';
        $this->xmlSepaName = $tmpFile;

        //  Seteamos el nombre del fichero de la remesa
        $this->ficheroRemesa = $tmpFile;

        //  Inicializamos el controller de la remesa
        $remesa = new RemesaController();
        //  Ejecutamos el proceso de generación de la remesa
        //  Recuperamos el IBAN del banco seleccionado / BIC / CREDITOR ID
        $iban = $this->banco['iban'];
        $bic = $this->banco['bic'];
        $creditorId = $this->banco['creditorid'];

        $this->remesaGenerada = $remesa->CreateRemesaXML($tmpFile, $creditorId, (int)$this->administrador['id'], $this->administrador['nombre'], $iban, $bic, null, $this->invoiceIds);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ///                               INFORMACIÓN DE FACTURACION
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Devuelve la información del total de facturación para el dashboard
     * @param bool (optional) $anual Total de facturación anual para el año en curso. Defaults: True
     * @param int (optional) $mes Mes que se desea consultar. Defaults: Mes en curso
     * @param int (optional) $anyo Año que se desea consultar. Defaults: Año en curso
     */
    public function TotalFacturacion($anual = true, $mes = null, $anyo = null)
    {
        //  Si no ha especificado el mes se calcula con el mes actual
        if(is_null($mes)){
            $mes = date('m');
        }

        if(is_null($anyo)){
            $anyo = date('Y');
        }

        $this->InvoiceModel->SetAnual(true);
        $this->InvoiceModel->SetMonth($mes);
        $this->InvoiceModel->SetAnyo($anyo);

        //  Facturación anual
        $data = [];
        $data['month'] = HelperController::StringMonth($mes);
        $data['year'] = $anyo;
        // $data['facturacion_anual'] = $this->InvoiceModel->TotalFacturacion(true, $mes, $anyo)[0];
        $data['facturacion_anual'] = $this->InvoiceModel->TotalFacturacion()[0];
        //  Facturación del mes solicitado. Por defecto debería ser el mes en curso
        $data['facturacion_mes'] = [];
        $this->InvoiceModel->SetAnual(false);
        //  Facturación estimada del mes actual
        $data['facturacion_mes']['facturacion_estimada'] = $this->InvoiceModel->TotalFacturacion()[0];
        //  Facturación real del mes en curso
        $facturacionMesCurso = $this->InvoiceModel->TotalesFacturacionMesPorServicio();
        $data['facturacion_mes']['facturacion_cobradas'] = $facturacionMesCurso[0];
        $data['facturacion_mes']['facturacion_devueltas'] = $facturacionMesCurso[1];
        //  Total ingresos a cuenta pendientes de liquidar
        $data['ingresoscuentapendiente'] = $this->InvoiceModel->TotalIngresosCuentaPendienteLiquidacion()['total'];
        //  Total liquidaciones realizadas
        $data['totalliquidaciones'] = abs($this->InvoiceModel->TotalLiquidaciones()['total']);
        //  Total Remesas generadas
        $data['totalremesas'] = $this->InvoiceModel->TotalRemesas();
        //  Total Facturas pendientes de cobro
        $facturasPendientesCobro = $this->InvoiceModel->PendientesCobro();
        //  Nº Total Facturas devueltas
        $data['facturasdevueltas'] = $facturasPendientesCobro['total_facturas'];
        //  Importe total Facturas pendientes devueltas
        $data['totalfacturasdevueltas'] = $facturasPendientesCobro['total_importe'];
        $bestCustomer = $this->InvoiceModel->BestCustomer();
        $data['bestcustomer'] = $bestCustomer['administrador'];
        $data['bestcustomer_total_facturacion'] = $bestCustomer['total'];

        return $data;
    }

    /**
     * Valida los parámetros necesarios para la facturación
     * @return object|bool  Devuelve true o un objeto con el mensaje de error
     */
    private function ValidateParamsFacturacion()
    {
        
        $error = '';

        //  ¿El administrador tiene servicios contratados para las comunidades activas?
        if(!is_array($this->InvoiceModel->Services()) || is_null($this->InvoiceModel->Services())){
            // $error = '- No ha especificado los servicios a consultar para la facturación<br>';
            $error = 'Actualmente este administrador no tiene servicios activos y/o contratados para las comunidades que tiene dadas de alta en la plataforma<br>';
        }

        //  Administrador
        if($this->InvoiceModel->IdAdministrador() == '' || (int)$this->InvoiceModel->IdAdministrador() <= 0){
            $error .= 'No ha seleccionado ningún administrador<br>';
        }

        //  Validamos el mes de facturación
        if(intval($this->InvoiceModel->Month()) < 1 || intval($this->InvoiceModel->Month()) > 12){
            $error .= 'El mes de facturación especificado no es correcto<br>';
        }

        if($error !== ''){
            return ['error' => $error];
        }else{
            return true;
        }
    }

    /**
     * Comprueba si el servicio DOC CAE ya ha sido facturado
     * @return bool Estado de la facturación del Servicio de DOC CAE
     */
    private function ServicioDOCCAEFacturado($idComunidad)
    {
        $facturado = $this->InvoiceModel->ServicioDOCCaeFacturado($idComunidad);
        return (count($facturado) > 0);
    }

    private function TieneServiciosPendientesFacturar()
    {

    }

    /** 
     * Comprueba el estado de la facturación según los parámetros establecidos
     * @return Array Array asociativo con la información del estado de las facturas para un período concreto
     */
    public function EstadoFacturacion()
    {
        $resultado = [
            'label_estado' => self::ESTADO_PENDIENTE,
            'pendientes' => 0,
            'pendientes_total' => 0,
            'cobradas' => 0,
            'cobradas_total' => 0,
            'devueltas' => 0,
            'devueltas_total' => 0,
        ];

        $estado = $this->InvoiceModel->EstadoFacturacion();

        if(count($estado) > 0)
        {
            $facturacion = $estado[0];
            //  Comprobamos el total de facturas
            $resultado['pendientes'] = intval($facturacion['pendientes']);
            $resultado['pendientes_total'] = number_format((float)$facturacion['pendientes_total'],2,',','.');
            $resultado['cobradas'] = intval($facturacion['cobradas']);
            $resultado['cobradas_total'] = number_format((float)$facturacion['cobradas_total'], 2, ',','.');
            $resultado['devueltas'] = intval($facturacion['devueltas']);
            $resultado['devueltas_total'] = number_format((float)$facturacion['devueltas_total'], 2, ',','.');


            switch(true)
            {
                case $resultado['devueltas'] > 0:
                    $resultado['label_estado'] = self::ESTADO_FACTURAS_DEVUELTAS;
                    break;
                case ($resultado['devueltas'] == 0 && $resultado['pendientes'] == 0 && $resultado['cobradas'] > 0):
                    $resultado['label_estado'] = self::ESTADO_FACTURADO;
                    break;
                case ($resultado['devueltas'] == 0 && $resultado['pendientes'] > 0):
                    $resultado['label_estado'] = self::ESTADO_PENDIENTE;
                    break;
            }

        }

        return $resultado;

    }

    /** TOFIX:
     * Devuelve la información de la facturación relativa a un mes según servicios y administrador
     * @param array $servicios. Servicios que se van a calcular
     * @param int $mesFacturación. Mes ordinal que se va a consultar
     * @param int $idAdministrador (Optional). ID del administrador que se va a consultar. Default: -1
     */
    public function Info($servicios, $mesFacturacion, $idAdministrador = -1)
    {

        $error = '';

        $mesFacturacion = DatabaseCore::PrepareDBString($mesFacturacion);
        $idAdministrador = DatabaseCore::PrepareDBString($idAdministrador);

        //  ID administrador
        $this->InvoiceModel->SetIdAdministrador($idAdministrador);

        //  Datos de facturación
        $this->InvoiceModel->SetAnual(false)->SetMonth($mesFacturacion);

        //  Recuperamos la información de los servicios contratados del administrador
        //  según las comunidades que tenga activas en este mismo momento
        $servicios = null;
        $serviciosContratados = $this->InvoiceModel->ServiciosContratados()[0];

        foreach($serviciosContratados as $key => $value)
        {
            //  Si el servicio está contratado, lo procesamos
            if((int)$value == 1)
            {
                //  Si es la primera iteración inicializamos la variable de servicios a array
                if(is_null($servicios)){
                    $servicios = [];
                }

                switch($key){
                    case 'cae':
                        $servicios[] = 1;
                        $this->servicioCAE = true;
                        break;
                    case 'dpd':
                        $servicios[] = 2;
                        $this->servicioDPD = true;
                        break;
                    case 'doccae':
                        $servicios[] = 3;
                        $this->servicioDocCAE = true;
                        break;                        
                    case 'certificadosdigitales':
                        $servicios[] = 5;
                        $this->servicioCertificadosDigitales = true;
                        break;
                }
            }
        }

        $this->InvoiceModel->SetServices($servicios);

        //  Validamos los parámetros
        $validation = $this->ValidateParamsFacturacion();
        if($validation !== true)
            return $validation;

        //  Recuperamos la información del Administrador para la configuración de la facturación
        $administrador = $this->UsuarioController->Get($idAdministrador);
        $configuracion = [];
        $configuracion['optagrupaservicios'] = 0;
        $configuracion['optenvioapi'] = 1;

        if(count($administrador['Usuario']) > 0){
            $administrador = $administrador['Usuario'][0];
            $configuracion['optagrupaservicios'] = (int)$administrador['optagrupaservicios'];
            $configuracion['optenvioapi'] = (int)$administrador['optenvioapi'];
        }

        $servicios = implode(',',$servicios);

        $this->InvoiceModel->SetAnual(false)
        ->SetMonth($mesFacturacion)
        ->SetServices($servicios);

        //  Validamos que no se hayan emitido ya facturas para el mes
        $estadoFacturacion = $this->EstadoFacturacion();

        //  Consultamos el total de la facturación para los parámetros seleccionados
        $resultado = $this->InvoiceModel->TotalFacturacion()[0];

        $totalFacturacion = 0;
        $totalFacturacion += $this->servicioCAE ? floatval($resultado['total_cae']) : 0;
        $totalFacturacion += $this->servicioDPD ? floatval($resultado['total_dpd']) : 0;
        $totalFacturacion += $this->servicioDocCAE ? floatval($resultado['total_doccae']) : 0;
        $totalFacturacion += $this->servicioCertificadosDigitales ? floatval($resultado['total_certificados']) : 0;

        return [
            'servicios' => $serviciosContratados,
            'configuracion' => $configuracion,
            'estadofacturacion' => $estadoFacturacion,
            'numerocomunidades' => $resultado['total_comunidades'],
            'totalfacturacion' => number_format((float)$totalFacturacion, 2, ',','.'),
            'total_cae' => number_format((float)$resultado['total_cae'], 2, ',','.'),
            'total_doccae' => number_format((float)$resultado['total_doccae'], 2, ',','.'),
            'total_dpd' => number_format((float)$resultado['total_dpd'], 2, ',','.'),
            'total_certificados' => number_format((float)$resultado['total_certificados'], 2, ',','.'),
        ];

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                 VALIDACIONES
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Comprueba si ya hay una factura generada para el administrador, comunidad, mes y año de facturación
     * @param int $idAdministrador ID del administrador
     * @param int $idComunidad ID de la comunidad
     * @param int $idServicio ID del servicio
     * @param int $mesFacturacion Mes de facturación (Nº ordinal del mes)
     * @param int $anyoFacturacion Año de facturación
     * @return bool Resultado de la validación
     */
    private function ExistsInvoice()
    {

        $idAdministrador = $this->InvoiceModel->IdAdministrador();
        $idComunidad = $this->InvoiceModel->IdComunidad();
        $servicios = $this->InvoiceModel->Services();
        $mesFacturacion = $this->InvoiceModel->Mes();
        $anyoFacturacion = $this->InvoiceModel->Anyo();

        //  Seteamos las propiedades para la entidad que vamos a recuperar
        $this->InvoiceModel->SetComunidadId( $this->InvoiceModel->IdComunidad() )
        ->SetMonth( $this->InvoiceModel->Month() )
        ->SetYear( $this->InvoiceModel->Year() );

        //  Añadimos los campos de búsqueda para la factura
        $this->InvoiceModel->AddFieldToSearch('idadministrador', $this->InvoiceModel::_OPERATOR_EQUALS_, 'int', $idAdministrador)
        ->AddFieldToSearch('idcomunidad', $this->InvoiceModel::_OPERATOR_EQUALS_, 'int', $idComunidad)
        ->AddFieldToSearch('mes', $this->InvoiceModel::_OPERATOR_EQUALS_, 'int', $mesFacturacion)
        ->AddFieldToSearch('anyo', $this->InvoiceModel::_OPERATOR_EQUALS_, 'int', $anyoFacturacion);

        //  Recuperamos la entidad si es que existe
        $invoice = $this->InvoiceModel->Search();

        //  Si existe, con su ID recuperamos la factura y comprobamos si tiene el servicio ya facturado
        if(count($invoice['Invoice']) <= 0){
            return false;
        }


        return true;

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                 HELPERS Y METODOS AUXILIARES
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Genera el fichero en excel de los datos facilitados
     * @param string $fileName Nombre del fichero que se va a generar
     * @return string|bool Devuelve el fichero codificado en base64 o false si no se pudo generar
     */
    private function ProcessExcelSimulacion(string $fileName, $comunidades)
    {
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $fileName;
        $errores = 0;
        $etiquetaError = '<style bgcolor="#FF0000" color="#ffffff"><b>Error</b></style>: ';
        //  Nombre del administrador
        $nombreAdministrador = $this->administrador['nombre'];

        //  Email de facturación
        if($this->administrador['emailfacturacion'] == '' || is_null($this->administrador['emailfacturacion'])){
            $errores++;
        }

        $emailFacturacion = ($this->administrador['emailfacturacion'] == '' || is_null($this->administrador['emailfacturacion']) ? $etiquetaError . 'No configurado' : $this->administrador['emailfacturacion']);
        //  Total facturación estimada
        $totalFacturacion = 0;

        $mesFacturacion = HelperController::StringMonth($this->iMesFacturacion);

        //  Recuperamos la información de las comunidades para calcular totales y pintarlo después
        $iComunidades = 0;
        $datosComunidades = [];
        foreach($comunidades as $comunidad)
        // for($iComunidad = 0; $iComunidad < count($comunidades); $iComunidad++)
        {
            $serviciosFacturables = [];
            $subtotal = 0;
            $servicios = 0;

            if(isset($comunidad['services']))
            {

                //  Procesamos los servicios
                for($i = 0; $i < count($comunidad['services']); $i++)
                {

                    $idServicio = $comunidad['services'][$i]['idservicio'];
                    $precioServicio =  (float)$comunidad['services'][$i]['preciocomunidad'];

                    //  Comprobamos si el servicio ya ha sido facturado con anterioridad
                    $servicioFacturado = false;
                    
                    $idComunidad = (int)$comunidad['id'];

                    if($idServicio == 3) // DOCCAE
                    {
                        $servicioFacturado = $this->ServicioDOCCaeFacturado($idComunidad);
                    }else{
                        $servicioFacturado = count($this->InvoiceModel->ServicioFacturado($idComunidad, (int)$idServicio, (int)$this->iMesFacturacion, (int)date('Y'))) > 0;
                    }

                    if(!$servicioFacturado){
                        $serviciosFacturables[] = $this->ServiceNameById( $idServicio ) . ': ' . $precioServicio . '€ ';
                        $subtotal = (float)$subtotal + (float)$comunidad['services'][$i]['preciocomunidad'];
                        $servicios++;
                    }
                }

            }

            //  Comprobamos si la comunidad tiene servicios facturables ya que si no, hay que quitarla
            if($servicios > 0)
            {
                $iComunidades++;
                $servicios = implode(', ',$serviciosFacturables);

                $impuestos = ( (float)$this->impuesto * (float)$subtotal ) / 100;
                $impuestos = HelperController::Redondeo($impuestos);

                $totalComunidad = (float)$subtotal + (float)$impuestos;
                $totalComunidad = HelperController::Redondeo($totalComunidad);

                //  Validamos el IBAN de la comunidad
                $ibanValido = HelperController::ValidateIban($comunidad['ibancomunidad']);

                //  Validamos el CIF de la comunidad
                $cifValido = HelperController::ValidarNIFCIF($comunidad['cif']);
                if(!$cifValido){
                    $errores++;
                    $cif = $etiquetaError . (trim($comunidad['cif']) == '' ? 'No tiene CIF/NIF' : $comunidad['cif']);
                }else{
                    $cif = $comunidad['cif'];
                }

                //  Acumulamos el total de la facturación global
                $totalFacturacion = (float)$subtotal + (float)$impuestos + (float)$totalFacturacion;
                $datosComunidades[] = [
                    $comunidad['codigo'], 
                    $comunidad['nombre'], 
                    $ibanValido ? $comunidad['ibancomunidad'] : $etiquetaError . $comunidad['ibancomunidad'], 
                    $cif,
                    $servicios, 
                    $subtotal,
                    $impuestos,
                    $totalComunidad
                ];

            }

        }
        //  Montamos la cabecera del fichero Excel
        $datosExcel = [];
        $datosExcel[] = ['<b>Administrador</b>:', '<center>' . $nombreAdministrador . '</center>', '<b>E-mail de Facturación</b>:', $emailFacturacion];
        $datosExcel[] = ['<b>Mes de facturación</b>:' , '<center>'.ucfirst($mesFacturacion).'</center>', '<b>Año de facturación</b>:' , '<center>' . $this->iAnyoFacturacion . '</center>'];
        $datosExcel[] = ['<b>Nº de comunidades para facturar</b>:', '<center>' . $iComunidades . '</center>', '<b>Total Facturación estimada</b>:', '<right>' . $totalFacturacion . '€</right>'];
        $datosExcel[] = ['<b>Errores detectados</b>:', '<center>'. $errores . '</center>'];

        $datosExcel[] = ['' => ''];
        //  Montamos la cabecera para las comunidades que son susceptibles de facturación
        $datosExcel[] = ['<b><right>Cód.</right>', 
        '<b>Comunidad</b>', 
        '<b>IBAN</b>', 
        '<b>CIF</b>', 
        '<b>Servicios facturables</b>', 
        '<b><center>Subtotal</center></b>', 
        '<b><center>Impuestos</center></b>',
        '<b><center>Total</center></b>'];

        //  Metemos la información de las comunidades que se van a facturar
        foreach($datosComunidades as $dato){
            $datosExcel[] = $dato;
        }

        \HappySoftware\Controller\Traits\ExcelGen::fromArray($datosExcel, $nombreAdministrador)->saveAs($path);
        //  Validamos que se haya generado correctamente
        if(file_exists($path)){
            $base64 = base64_encode(file_get_contents($path));
            unlink($path);
            return $base64;
        }else{
            return false;
        }
    }

    /**
     * Genera un número de factura válido 
     * @return string Número de factura con la serie
     */
    private function GenerateNumeroFactura()
    {
        return $this->prefijoSerieFacturacion . date('y') . '/' . str_pad($this->serieFacturacion, 6, '0', STR_PAD_LEFT);
    }

    /** TODO: 
     * Valida el contador de la serie de facturación ya que debe establecerse a 1 por cada año nuevo
     */
    private function ValidateSerieFacturacion()
    {
        //  Comprobamos si existe ya la factura
        $serieFacturacionToValidate = $this->prefijoSerieFacturacion . date('y') . '/' . str_pad($this->serieFacturacion, 6, '0', STR_PAD_LEFT);
        //  Establecemos la búsqueda
        $result = $this->InvoiceModel->AddFieldToSearch('numero', $this->InvoiceModel::_OPERATOR_EQUALS_, 'string', $serieFacturacionToValidate)
        ->Search();

        if(count($result['Invoice']) > 0){
            //  Ya 
        }
    }

    /**
     * Devuelve el nombre del servicio en base a su ID.
     * REFACTORIZAR: Ahora mismo está hardcodeado pero lo suyo sería que lo devolviese de la bbdd instanciando el controller correspondiente
     */
    public function ServiceNameById($servicioId)
    {
        $nombre = '';
        switch(intval($servicioId)){
            case 1:
                $nombre = 'CAE';
                break;
            case 2:
                $nombre = 'DPD';
                break;
            case 3:
                $nombre = 'DOC. CAE';
                break;
            case 4:
                $nombre = 'Instalaciones';
                break;
            case 5:
                $nombre = 'Certificados digitales';
                break;                
        }
        return $nombre;
    }

    /**
     * Valida que los parámetros obligatorios para ejecutar el proceso son correctos
     * @return bool
     */
    private function ValidateConfiguracionFromRequest()
    {
        //  Validamos que sea un usuario de tipo facturación o de tipo sudo
        if(!$this->isFacturacion() && !$this->isSudo()){
            return HelperController::errorResponse('error','No tiene acceso a esta funcionalidad');
        }

        //  Validamos las opciones
        if(!isset($opciones['idadministrador']) || !isset($opciones['mesfacturacion'])){
            return HelperController::errorResponse('error','Los parámetros especificados no son válidos');
        }

        //  Validamos que haya seleccionado un administrador válido
        if((int)$opciones['idadministrador'] <= 0){
            return HelperController::errorResponse('error','No ha seleccionado ningún administrador válido');
        }

        //  Si no hay configuración almacenada, avisamos al usuario y no procesamos
        if(!$this->ConfiguracionController->HasValues())
            return HelperController::errorResponse('error','No se ha encontrado la configuración de las series de facturación');

        return true;
    }

    /**
     * Construye el mensaje de progreso que se enviará durante el proceso de actualización
     */
    public function SendProgressResultMessage($totalProgress, $currentProgress, $administrador, $mesfacturacion, $comunidad, $servicio, $importefactura)
    {
        //  Nombre del administrador
        $result = '<strong>Administrador</strong>: <span class="text-truncate">' . $administrador . '</span><br>';

        //  Servicio facturado
        if(!is_null($servicio)){
            $result .= '<strong>Servicio</strong>: ' . $servicio . '<br>';
        }
        //  Nombre de la comunidad
        if(!is_null($comunidad)){
            $result .= '<strong>Comunidad</strong>: ' . $comunidad . '<br>';
        }
        //  Mes de facturación
        $result .= '<strong>Mes de facturaci&oacute;n</strong>: ' . $mesfacturacion . '<br>';
        //  Importe
        $result .= '<strong>Importe</strong>: ' . $importefactura . '<br>';     

        HelperController::sendProgressResponse($totalProgress, $currentProgress, $result);
    }

    /**
     * Envía un mensaje personalizado al buffer de salida del estado de progreso
     */
    public function SendProgressResultCustomMessage($totalProgress, $currentProgress, $message)
    {
        HelperController::sendProgressResponse($totalProgress, $currentProgress, $message);
    }

    /**
     * Agrupa las comunidades en un solo objeto
     * @param array $comunidades Comunidades del Administrador que van a ser procesadas
     */
    private function AgruparServiciosComunidades($comunidades)
    {
        //  Inicializamos la propiedad
        $this->comunidadesFacturacion = [];

        for($iComunidad = 0; $iComunidad < count($comunidades); $iComunidad++)
        {
            $comunidad = $comunidades[$iComunidad];
            $idComunidad = $comunidad['idcomunidad'];

            if((int)$comunidad['servicio_contratado'] == 1 && $idComunidad !== '')
            {
                //  Datos de la comunidad para procesar la facturación
                if(!isset($this->comunidadesFacturacion[$idComunidad])){
                    $this->comunidadesFacturacion[$idComunidad]['id'] = $comunidad['idcomunidad'];
                    $this->comunidadesFacturacion[$idComunidad]['codigo'] = $comunidad['codigo'];
                    $this->comunidadesFacturacion[$idComunidad]['nombre'] = $comunidad['comunidad'];
                    $this->comunidadesFacturacion[$idComunidad]['direccion'] = $comunidad['direccion'];
                    $this->comunidadesFacturacion[$idComunidad]['localidad'] = $comunidad['localidad'];
                    $this->comunidadesFacturacion[$idComunidad]['provincia'] = $comunidad['provincia'];
                    $this->comunidadesFacturacion[$idComunidad]['cif'] =  $comunidad['cif'];
                    $this->comunidadesFacturacion[$idComunidad]['ibancomunidad'] =  $comunidad['ibancomunidad'];
                }

                //  Datos del servicio
                $idServicio = (int)$comunidad['idservicio'];

                //  Por defecto se factura el servicio
                $facturarServicio = true;

                //  Si el servicio es el de Doc CAE hay que comprobar si ya ha sido facturado con anterioridad ya que si no, no se incluye
                if($idServicio == 3)
                {
                    //  Validamos que el servicio de DOC Cae no haya sido ya facturado para la comunidad
                    $doccaeFacturado = $this->ServicioDOCCaeFacturado($idComunidad);
                    $facturarServicio = $doccaeFacturado == true ? false : true;
                }else{
                    $servicioFacturado = $this->InvoiceModel->ServicioFacturado($idComunidad, $idServicio, $this->iMesFacturacion, $this->iAnyoFacturacion);
                    $facturarServicio = count($servicioFacturado) <= 0;
                }

                if($facturarServicio)
                {
                    $servicio = [];
                    $servicio['idservicio'] = $idServicio;
                    $servicio['precio'] = $comunidad['precio'];
                    $servicio['preciocomunidad'] = $comunidad['preciocomunidad'];
                    $this->comunidadesFacturacion[$idComunidad]['services'][] = $servicio;         
                }

            }

        }
    }

    /**
     * Actualiza la serie de facturación según la última factura generada
     */
    private function UpdateSerieFacturacion()
    {
        $this->ConfiguracionController->UpdateValue('seriefacturacion', $this->serieFacturacion);
    }

    /** TODO: Antes de generar la referencia del contrato hay que validar si hay que facturar el servicio
     * Genera el string que compone la referencia del contrato
     * @param int|array $idServicio (opcional) ID del servicio
     * @param bool $allServices (opcional) Defaults: false. Indica si se debe generar la referencia del contrato para todos los servicios o de forma individual
     * @return string Referencia del contrato
     */
    private function GenerarReferenciaContrato($idServicio = null, $allServices = false)
    {

        $referencia = '';

        //  Comprobamos primero si los servicios se agrupan para concatenar todos los que sean necesarios
        if($allServices){

            //  Por cada 1 de los servicios recuperamos el string correspondiente para construir la referencia del contrato

            //  Empezamos por la CAE
            if( in_array('1', array_column($idServicio, 'idservicio')) ){
                $referencia .= $this->confConceptoCAE;
            }
            
            //  Continuamos con DPD
            if( in_array('2', array_column($idServicio, 'idservicio')) ){
                $referencia .= $this->confConceptoDPD;
            }
            
            //  DOC CAE
            if( in_array('3', array_column($idServicio, 'idservicio')) ){
                $referencia .= $this->confConceptoDOCCAE;
            }

            //  Certificados Digitales
            if( in_array('5', array_column($idServicio, 'idservicio')) ){
                $referencia .= $this->confCertificadoDigital;
            }            

        }else{
            //  Recuperamos la referencia según el servicio que se esté facturando
            switch((int)$idServicio){
                // CAE
                case 1: 
                    $referencia = $this->confConceptoCAE;
                    break;
                //  DPD
                case 2:
                    $referencia = $this->confConceptoDPD;
                    break;
                //  DOC CAE
                case 3:
                     $referencia = $this->confConceptoDOCCAE;
                     break;
                //  Certificados digitales
                case 5:
                    $referencia = $this->confCertificadoDigital;
                    break;
            }
        }

        return $referencia;

    }

    /**
     * Genera el concepto de facturación según el servicio
     */
    private function GenerarDetalleLinea($idServicio)
    {
        $result = '';        
        switch($idServicio)
        {
            case 1: // CAE
                $result = $this->conceptoCAE;
                break;
            case 2: //  DPD
                $result = $this->conceptoDPD;
                break;
            case 3: // DOC CAE
                $result = $this::NOMENCLATURA_DOCCAE; //$this->conceptoDOCCAE;
                break;
            case 5: //  Certificados digitales
                $result = $this->conceptoCertificadosDigitales;
                break;
        }

        return $result;
    }

    /**
     * Genera los PDF de facturas para los procesos de generación de factura automática
     */
    private function GenerarFacturasPDF(int $idInvoice = -1 )
    {
        ////////////////////////////////////////////////////////////////
        //      NOMENCLATURA FICHEROS ZIP: FINCATECH_FACT_CA_MM_YYYY
        //==============================================================
        //  CA:     Cif del administrador
        //  MM:     Mes 2 dígitos
        //  YYYY:   Año 4 dígitos
        //==============================================================
        //      NOMENCLATURA FICHERO PDF: FINCATECH_FACT_CC_NC_MM_YYYY
        //==============================================================
        //  CC:     Cif Comunidad [No]
        //  NC:     Nombre_comunidad_parseado. Máx 20 caracteres
        //  MM:     Mes 2 dígitos
        //  YYYY:   Año 4 dígitos        
        //==============================================================

        //  Es una factura individual
        if($idInvoice > 0){
            $this->invoicePdfIds[] = $idInvoice;
        }

        //  Comprobamos si hay id's para las facturas que se van a generar
        if(count($this->invoicePdfIds) > 0)
        {
            $pdfFileName = '';
            $pdfInvoiceNames = [];
            $this->pdfNames = [];

            if($this->agruparFacturasZip){             
                $this->zipFileName = self::FICHERO_PREFIX . $this->InvoiceModel->CifAdministrador() . '_' . $this->InvoiceModel->Mes() . '_' . $this->InvoiceModel->Anyo() . '_' . date('H_i_s') . '.zip';
            }

            $administradorNombre = '';
            //  Iteramos sobre todas las facturas a enviar al administrador
            for($xInvoice = 0; $xInvoice < count($this->invoicePdfIds); $xInvoice++)
            {
                //  Recuperamos el ID de la factura que se va a procesar
                $id_invoice = $this->invoicePdfIds[$xInvoice];
                //  Recuperamos la información de la factura
                $facturaData = $this->Get($id_invoice);
                //  Recuperamos la comunidad asociada a la factura
                $comunidad = $facturaData['Invoice'][0]['comunidad'][0];
                //  Recuperamos el administrador asociado a la factura
                $administrador = $facturaData['Invoice'][0]['usuario'][0];

                $nombreComunidad = strtoupper( HelperController::GenerarLinkRewrite( $comunidad['nombre'] ) );
                $nombreAdministrador = strtoupper( HelperController::GenerarLinkRewrite( $administrador['nombre']) );
                $administradorNombre = $administrador['nombre'];
                $numeroFactura = strtoupper( HelperController::GenerarLinkRewrite( $facturaData['Invoice'][0]['numero']) );
                $emailFacturacion = $administrador['emailfacturacion'];

                //$pdfName = self::FICHERO_PREFIX . $numeroFactura . '_' . $nombreComunidad . '_' . $this->InvoiceModel->Mes() . '_' . $this->InvoiceModel->Anyo();
                $pdfName = $this->GeneratePDFFileName($facturaData['Invoice'][0]['numero'], $comunidad['nombre'], $administrador['nombre']);
                
                //  Componemos el mensaje de salida y enviamos al buffer de salida
                $message = '<p class="font-weight-bold text-center">Procesando PDF de facturas</p>';
                $message .= '<p class="mb-0"><span class="font-weight-bold">Factura</span>: ' . $numeroFactura . '</p>';
                $message .= '<p><span class="font-weight-bold">Fichero</span>: ' . $pdfName . '</p>';
                $message .= '<p class="font-weight-bold text-center">Factura ' . $xInvoice + 1 . ' de ' . count($this->invoicePdfIds) . '</p>';
                $porcentaje = ((($xInvoice + 1) * 100) / (count($this->invoicePdfIds) + 1));
                $this->SendProgressResultCustomMessage(100, $porcentaje, $message);
                //  Si hay que incluirlo en un zip, generamos la factura de manera individual
                if($this->agruparFacturasZip)
                {
                    //  Generamos el nombre del fichero PDF
                    $pdfTmpFile = $this->pdfPath . $pdfName;
                    //  Incluimos el fichero generado en un array para posteriormente generar el fichero ZIP
                    $this->pdfNames[] = ROOT_DIR . $this->pdfPath . $pdfName . '.pdf';
                    //  Creamos el fichero de manera individual para incluirlo posteriormente en un fichero zip
                    $this->CreatePdfInvoice($pdfTmpFile, $facturaData, false);
                }else{ 
                    // Archivo con todas las facturas 
                    $pdfTmpFile = $this->pdfPath . $pdfFileName . $nombreAdministrador . '_' . str_pad($this->InvoiceModel->Mes(), 2,'0', STR_PAD_LEFT) . '_' . $this->InvoiceModel->Anyo();
                    //  Añadimos la factura al PDF
                    $this->CreatePdfInvoice($pdfTmpFile, $facturaData, true, ($xInvoice == count($this->invoicePdfIds) - 1));
                }

                //  Asignamos el nombre del fichero que se va a generar a la factura
                $this->InvoiceModel->SetId($id_invoice)
                ->SetFichero($pdfName)
                ->AssignPDFFileToInvoice();

            } // END FOR

            $attachmentFile = null;

            //  Comprobamos primero la configuración de si hay que agruparlas en un zip o van por separado
            if($this->agruparFacturasZip){
                //  Generamos el fichero ZIP con todas las facturas
                $message = '<p class="font-weight-bold text-center">Generando Fichero ZIP con las facturas generadas</p>';
                $message .= '<p class="mb-0"><span class="font-weight-bold">Fichero</span>: ' . $this->zipFileName . '</p>';
                $this->SendProgressResultCustomMessage(100, 50, $message);
                $result = $this->GenerateZipFile($this->pdfNames, $this->zipPath, $this->zipFileName, true);
                $attachmentFile = $result ? $this->zipFileName : false; 
            }else{
                //  Escribimos el fichero con el resultado de la comunidad para el administrador
                $attachmentFile = $this->MakePDF();
            }
            
            //  Validamos que se haya generado el fichero que se va a enviar al administrador
            if($attachmentFile === false){
                //  Escribimos el error en el log y salimos de aquí
                $this->AddErrorToLog('<p>El fichero ZIP no se ha podido generar</p>');
                //  Enviamos al administrador del sistema el error que ha ocurrido para avisarle
                $mensaje = 'Ha ocurrido un error al intentar generar el ZIP de facturación<br><br>';
                $mensaje .= 'Administrador: ' . $administradorNombre . '<br>' ;
                $mensaje .= 'Mes de facturación: ' . $this->InvoiceModel->Mes() . '-' . $this->InvoiceModel->Anyo();
                $this->SendEmail('desarrollo@fincatech.es','Desarrollo', 'Error generando ZIP Facturación', $mensaje, false);
                return;
            }else{
                $this->SincronizarFicherosGenerados();
            }

        }

        //  Inicializamos los pdf's generados
        $this->pdfNames = [];

        
    }

    /**
     * Genera un único PDF para enviarlo.
     */
    private function CreatePdfInvoceById($id_invoice, $id_administrador = null, $appendToFile = false)
    {

    }

    /**
     * Genera tantos PDF como haya configurado. Este método se utiliza para generar los pdf durante el proceso de generación automática
     * @param bool $agrupar. Indica si se debe agrupar todo en un solo PDF
     */
    private function CreatePdfInvoice(string $fileName, array $invoice_data, bool $oneFile = false, bool $finalPage = false)
    {
        global $appSettings;
        $footerHTML = self::GetTemplate('facturacion/factura_footer.html');

        //  Reemplazamos la url donde está disponible la factura para su descarga
        $invoiceServer = $appSettings['ftp_servers']['facturacion']['server_url'];
        $invoiceUrl = $invoiceServer . 'pdf/' . basename($fileName) . '.pdf';
        $footerHTML = str_replace('[url_factura]', $invoiceUrl, $footerHTML);

        //  Generamos el nombre del fichero
        $this->InitializePDF($fileName);
        //  Parseamos el contenido del template con los datos reales de la factura
        $parsedHTML = $this->ParseHTMLInvoice($invoice_data);
        //  Si va agrupado en un solo PDF incluimos el salto de página
        if($oneFile){ 
            // n Facturas por Administrador
            $this->WriteToPDF($parsedHTML, $footerHTML, true, $finalPage);
        }else{ 
            // 1 Factura por Comunidad
            $this->WriteToPDF($parsedHTML, $footerHTML, false, false);
            return $this->MakePDF();
        }

    }

    /**
     * Recupera el template de factura y reemplaza las variables por los valores reales recuperados
     * @param array $data Datos de la factura
     * @return string HTML de salida ya parseado
     */
    private function ParseHTMLInvoice($data)
    {
        try{
            $factura = $data['Invoice'][0];
            //  Comunidad
            $comunidad = $data['Invoice'][0]['comunidad'][0];
            $administrador = $data['Invoice'][0]['usuario'][0];
            $detalle = $data['Invoice'][0]['invoicedetail'];
        
            //  Recuperamos el template de la factura
            $html = self::GetTemplate('facturacion/factura.html');

            //   Logo
            $logo = ROOT_DIR . DIRECTORY_SEPARATOR . 'public/assets/img/logo-fincatech.png';
            $logo = HelperController::ConvertImageToBase64($logo);
            $html = str_replace('[logo]', $logo, $html);       

            //  Sustuimos los datos por los recuperados de la factura
            //  Administrador Id
            $html = str_replace('[administrador_id]', $factura['idadministrador'], $html);
            //  Referencia contrato
            $html = str_replace('[referencia_contrato]', $factura['referenciacontrato'], $html);
            //  Nombre de la comunidad
            $html = str_replace('[comunidad_codigo]', $comunidad['codigo'], $html);
            $html = str_replace('[comunidad_nombre]', $comunidad['nombre'], $html);
            //  Localidad de la comunidad
            $html = str_replace('[comunidad_localidad]', $comunidad['localidad'], $html);
            //  CIF de la comunidad
            $html = str_replace('[comunidad_cif]', $comunidad['cif'], $html);
            //  Nº de Factura
            $html = str_replace('[factura_numero]', $factura['numero'], $html);
            //  Fecha de la factura
            $html = str_replace('[factura_fecha]', date('d-m-Y', strtotime($factura['dateinvoice'])), $html);

            //  % IVA
            $taxRate = str_replace(',','.', $factura['tax_rate']);
            $taxRate = (float)$taxRate;

            //  Total Impuestos exluidos
            $totalTaxesExcl = str_replace(',','.', $factura['total_taxes_exc']);
            $totalTaxesExcl = (float)$totalTaxesExcl;

            //  Total Impuestos incluidos
            $totalTaxesInc = str_replace(',','.', $factura['total_taxes_inc']);
            $totalTaxesInc = (float)$totalTaxesInc;

            //  Total impuestos calculados
            $totalTaxes = (float)($totalTaxesInc - $totalTaxesExcl);

            //  Procesamos el detalle para enviarlo
            $detalleConceptos = '';
            $detalleImportes = '';

            //  Procesamos los conceptos e importes
            for($iDetail = 0; $iDetail < count($detalle); $iDetail++){
                $detail = $detalle[$iDetail];
                $detalleConceptos .= $detail['detail'] . '<br>';
                $detailTotalTaxesExc =  str_replace(',','.', $detail['total_taxes_exc']);
                $detailTotalTaxesExc = (float)$detailTotalTaxesExc;
                $detalleImportes .= number_format($detailTotalTaxesExc, 2, ',','.') . '&euro;'  . '<br>';
            }

            $html = str_replace('[detalle_conceptos]', $detalleConceptos, $html);
            $html = str_replace('[detalle_importes]', $detalleImportes, $html);
            //  % IVA
            $sTaxRate = number_format($taxRate, '2',',','.');
            $html = str_replace('[impuesto_porcentaje]', $sTaxRate , $html);
            //  TOTAL FACTURA IMPUESTOS NO INCLUIDOS
            $sTotalTaxesExcl = number_format($totalTaxesExcl, '2',',','.');
            $html = str_replace('[factura_total_taxes_excl]', $sTotalTaxesExcl, $html);        
            //  TOTAL IMPUESTOS CALCULADOS
            $sTotalTaxes = number_format($totalTaxes, '2',',','.');
            $html = str_replace('[impuesto_importe_total]', $sTotalTaxes, $html);

            //  TOTAL FACTURA IMP. INCLUIDOS
            $sTotalTaxesInc = number_format($totalTaxesInc, '2',',','.');
            $html = str_replace('[factura_total_taxes_inc]', $sTotalTaxesInc, $html);
            //  IBAN
            $comunidadIban = str_replace(' ','',$comunidad['ibancomunidad']);
            $comunidadIban = trim($comunidadIban);
            $comunidadIban = substr($comunidadIban, -9);
            $html = str_replace('[comunidad_iban]', $comunidadIban, $html);
            //  FECHA DE VENCIMIENTO
            $fechaVto = date('d-m-Y', time());
            // $fechaVto .= '/' . HelperController::StringMonth(date('m', time()));
            // $fechaVto .= date('Y', time());
            $html = str_replace('[fecha_vencimiento]', $fechaVto, $html);
            //  Devolvemos el HTML ya parseado
            return $html;
        }catch(Throwable $ex){
            die($ex->getMessage());
        }
    }


    /**
     * Parsea el template de email para una factura por los datos reales
     * @param array|null $facturaRectificativa  (Optional) Array con los datos de la factura rectificativa. Defaults: Null
     * @param string|null $cuerpo               (Optional) Cuerpo del e-mail. Defaults: Null
     * @return string                           HTML Parseado
     */
    private function ParseHTMLInvoiceEmail(array|null $facturaRectificativa = null, string|null $cuerpo = null)
    {
        global $appSettings;
        $urlServer = $appSettings['ftp_servers']['facturacion']['server_url'];

        //  Recuperamos el Template de factura
        $html = self::GetEmailTemplate('facturacion/factura.html');

        $htmlRectificativa = '';

        //  Si tiene Factura Rectificativa Asociada inyectamos el texto
        if(!is_null($this->InvoiceModel->IdRectificativa()) && !is_null($facturaRectificativa))
        {
            //  URL Descarga
            $url =  $urlServer . 'pdf/' . $facturaRectificativa['nombrefichero'];
            //  Template de factura rectificativa dentro de e-mail
            $htmlRectificativa = self::GetEmailTemplate('facturacion/partials/texto_rectificativa.html');
            //  Número Factura Rectificativa
            $htmlRectificativa = str_replace('[factura_rectificativa_numero]', $facturaRectificativa['numero'], $htmlRectificativa);            
            //  Fecha factura rectificativa
            $htmlRectificativa = str_replace('[factura_rectificativa_fecha]', date('d/m/Y', strtotime($facturaRectificativa['created']) ), $htmlRectificativa);            
            //  Importe Factura Rectificativa
            $htmlRectificativa = str_replace('[factura_rectificativa_importe]', number_format($facturaRectificativa['total_taxes_inc'], 2, ',','.'), $htmlRectificativa);
            //  URL Descarga Factura rectificativa
            $htmlRectificativa = str_replace('[factura_rectificativa_url]', $url, $htmlRectificativa);             

        }

        //  Comprobamos si se ha generado un ZIP ya que esto indica que el parseo se realiza desde la generación automática en el proceso de facturación
        if($this->zipFileName != '')
        {
            //  TODO: Obtenemos el nombre del fichero junto con la ruta para poder enviarlo
            $url =  $urlServer . 'zip/' . $this->zipPath . $this->zipFileName;
        }else{
            //  Generamos el nombre del fichero PDF en base a los datos de la factura
            $pdfName = $this->GeneratePDFFileName($this->InvoiceModel->Numero(), $this->InvoiceModel->Comunidad(), $this->InvoiceModel->Administrador());
            $url = $urlServer . 'pdf/' . $pdfName;
        }

        //  Nombre del administrador
        $html = str_replace('[administrador]', $this->InvoiceModel->Administrador(), $html);
        //  Nº de Factura
        $html = str_replace('[factura_numero]', $this->InvoiceModel->Numero(), $html);
        //  Factura Fecha
        $html = str_replace('[factura_fecha]', date('d/m/Y', strtotime($this->InvoiceModel->DateInvoice())), $html);
        //  Importe de la factura
        $html = str_replace('[factura_importe]', number_format($this->InvoiceModel->TotalTaxesInc(), 2, ',','.'), $html);
        //  URL De la factura
        $html = str_replace('[url_factura]', $url, $html);
        //  Cuerpo
        $html = str_replace('[cuerpo_opcional]', (is_null($cuerpo) ? '' : $cuerpo), $html);
        //  Factura Rectificativa
        $html = str_replace('[factura_rectificativa]', $htmlRectificativa, $html);        
        //  Nombre de la comunidad
        $html = str_replace('[comunidad_nombre]', $this->InvoiceModel->CodigoComunidad() . ' - ' . $this->InvoiceModel->Comunidad(), $html);        

        return $html;
    }

    /**
     * Parsea el template de email de proceso de facturación automático enviándolo al SUDO
     * @return string HTML Parseado
     */
    private function ParseHTMLInvoiceEmailProcesoFacturacion()
    {
        global $appSettings;
        $urlServer = $appSettings['ftp_servers']['facturacion']['server_url'];

        //  Recuperamos el Template de factura
        $html = self::GetEmailTemplate('facturacion/factura_envio_proceso_sudo.html');

        //  Obtenemos el nombre del fichero junto con la ruta para poder enviarlo
        $urlZIP =  $urlServer . 'zip/' . $this->zipFileName;

        //  Nombre del administrador
        $html = str_replace('[administrador]', $this->InvoiceModel->Administrador(), $html);
        //  Mes de facturación
        $html = str_replace('[facturacion_mes]', HelperController::StringMonth($this->InvoiceModel->Month()), $html);
        //  Año de facturación
        $html = str_replace('[facturacion_anyo]', $this->InvoiceModel->Anyo(), $html);
        //  Total Facturas generadas
        $html = str_replace('[numero_facturas]', count($this->invoicePdfIds), $html);
        //  Total Facturación
        //  Nos aseguramos que el total sea correcto
        $totalFacturado = (float)str_replace(',','.', $this->totalFacturado);
        $html = str_replace('[total_facturacion]', number_format($totalFacturado, 2, ',','.'), $html);
        //  Número de comunidades facturadas
        $html = str_replace('[facturacion_icomunidades]', count($this->comunidadesFacturacion), $html);
        //  URL del paquete de descarga
        $html = str_replace('[url_zip_factura]', $urlZIP, $html);
        //  URL Remesa XML
        $html = str_replace('[xml_remesa]', $this->ficheroRemesa, $html);
        //  Cuerpo
        $html = str_replace('[cuerpo_opcional]', (is_null($this->emailBody) ? '' : $this->emailBody), $html);

        return $html;
    }

    /**
     * Parsea el template de email de proceso de facturación automático enviándolo al administrador
     * @return string HTML Parseado
     */
    private function ParseHTMLInvoiceEmailProcesoFacturacionAdministrador()
    {
        global $appSettings;
        $urlServer = $appSettings['ftp_servers']['facturacion']['server_url'];
        //  Obtenemos el nombre del fichero junto con la ruta para poder enviarlo
        $urlZIP =  $urlServer . 'zip/' . $this->zipFileName;
        //  Recuperamos el Template de factura
        $html = self::GetEmailTemplate('facturacion/factura_envio_administrador.html');
        //  Nombre del administrador
        $html = str_replace('[administrador]', $this->InvoiceModel->Administrador(), $html);
        //  Mes de facturación
        $html = str_replace('[facturacion_mes]', HelperController::StringMonth($this->InvoiceModel->Month()), $html);
        //  Año de facturación
        $html = str_replace('[facturacion_anyo]', $this->InvoiceModel->Anyo(), $html);
        //  Total Facturas generadas
        $html = str_replace('[numero_facturas]', count($this->invoicePdfIds), $html);
        //  Total Facturación
        $totalFacturado = (float)str_replace(',','.', $this->totalFacturado);
        $html = str_replace('[total_facturacion]', number_format($totalFacturado, 2, ',','.'), $html);
        //  Número de comunidades facturadas
        $html = str_replace('[facturacion_icomunidades]', count($this->comunidadesFacturacion), $html);
        //  URL del paquete de descarga
        $html = str_replace('[url_zip_factura]', $urlZIP, $html);
        //  Cuerpo
        $html = str_replace('[cuerpo_opcional]', (is_null($this->emailBody) ? '' : $this->emailBody), $html);

        return $html;
    }    

    /**
     * Añade un mensaje de error al log
     * @param string $mensaje Mensaje que se va a agregar al log de errores
     */
    private function AddErrorToLog(string $mensaje)
    {
        //  Incrementamos el número de errores
        $this->iErrores++;
        $this->haveError = true;

        $ch = fopen(ROOT_DIR . $this->erroresFileName, 'a');

        if($ch){
            //  Construimos el mensaje incluyendo fecha y hora
            $mensaje = '[' . date('d-m-Y h:i') .'] ' . $mensaje . PHP_EOL;
            $mensaje = mb_convert_encoding($mensaje, 'UTF-8', 'auto');
            // $mensaje = htmlentities($mensaje, ENT_QUOTES, 'UTF-8');
            fwrite($ch, $mensaje);
            fclose($ch);
        }
    }

    /**
     * Envía todos los ficheros generados al servidor que hace de almacén de ficheros
     */
    private function SincronizarFicherosGenerados()
    {
        //  Si estamos en modo debug no se envía nada al servidor
        if($this->debugMode){
            return;
        }

        $msg = '<p class="font-weight-bold">Sincronizando Ficheros con el almacén de Fincatech</p>';
                
        //  Enviamos los ficheros pdf generados al servidor almacén
        for($iFichero = 0; $iFichero < count($this->pdfNames); $iFichero++)
        {
            //  Enviamos al almacén de ficheros por FTP
            $pdfFile = $this->pdfNames[$iFichero];
            $pdfDestFile = basename($pdfFile);
            
            $msgFichero = '<p><span class="font-weight-bold">Fichero</span>: ' . $pdfDestFile . '</p>';
            $msgFichero .= '<p class="mb-0 text-center">Sincronizando ' . $iFichero . ' de ' . (count($this->pdfNames) + 1) . '</p>';
            $porcentaje = ((($iFichero + 1) * 100) / (count($this->pdfNames) + 1));

            $this->SendProgressResultCustomMessage(100, $porcentaje, $msg . $msgFichero);

            if($this->FTPSendFile($pdfFile, '/pdf/' . $pdfDestFile) === true)
            {
                //  Una vez sincronizado, lo eliminamos de este servidor
                unlink($pdfFile);
            }else{
                $msgError = '<p>Ha ocurrido un error al enviar el fichero ' . $pdfDestFile . ': ' . $this->FTPError() . '</p>';
                $this->SendProgressResultCustomMessage(100, $porcentaje, $msg . $msgError);
                $this->AddErrorToLog('No se ha podido sincronizar el fichero ' . $pdfDestFile);
            }
        }

        //  Enviamos el fichero zip al almacén de ficheros siempre y cuando exista
        if(!is_null($this->zipFileName) && $this->zipFileName !== ''){
            $msgFichero = '<p class="mb-0"><span class="font-weight-bold">Fichero</span>: ' . $this->zipFileName . '</p>';
            $msgFichero .= '<p class="mb-0">Sincronizados ' . (count($this->pdfNames) + 1) . ' de ' . (count($this->pdfNames) + 1) . '</p>';
    
            if($this->FTPSendFile(ROOTDIR . $this->zipPath . $this->zipFileName, '/zip/'. $this->zipFileName) === true)
            {
                //  Eliminamos el fichero del servidor
                unlink( ROOTDIR . $this->zipPath . $this->zipFileName);
            }else{
                //  Una vez enviado, lo eliminamos de este servidor
                $msgError = '<p>Ha ocurrido un error al enviar el fichero ' . $pdfDestFile . ': ' . $this->FTPError() . '</p>';
                $this->SendProgressResultCustomMessage(100, $porcentaje, $msg . $msgFichero);
                $this->AddErrorToLog('No se ha podido sincronizar el fichero ' . basename($this->zipFileName));
            }
        }

    }

    /**
     * Inicializa el gestor de errores propio de facturación
     */
    private function InicializarLogErrores()
    {
        global $appSettings;
        $this->iErrores = 0;
        $this->haveError = false;        
        $this->erroresFileName = $appSettings['storage']['log'] . 'log_facturacion_' . date('Y_m_d_h_i') . '.log';
    }

    /**
     * Genera el nombre del fichero PDF
     * @param string $numeroFactura Número de la factura
     * @param string $nombreComunidad Nombre de la comunidad
     * @return string Nombre del Fichero
     */
    private function GeneratePDFFileName(string $numeroFactura, string $nombreComunidad, string $nombreAdministrador)
    {

        $nombreComunidad = strtoupper( HelperController::GenerarLinkRewrite( $nombreComunidad ) );
        $nombreAdministrador = strtoupper( HelperController::GenerarLinkRewrite( $nombreAdministrador) );
        $numeroFactura = strtoupper( HelperController::GenerarLinkRewrite( $numeroFactura) );

        $pdfName = self::FICHERO_PREFIX . $numeroFactura . '_' . $nombreComunidad . '_' . $this->InvoiceModel->Mes() . '_' . $this->InvoiceModel->Anyo();
        return $pdfName;        
    }

    private function CreateRemesaTable($data)
    {
        $parsedHTML = '<p>No está incluida en ninguna remesa</p>';
        if(count($data) > 0){
            $templateName = 'facturacion/tabla_remesa.php';
            $parsedHTML = $this::GetTemplateWithData($templateName, $data);
        }
        return $parsedHTML;
    }

    private function CreateDetailTable($data)
    {
        $parsedHTML = '';
        if(count($data) > 0){
            //  Recuperamos la vista con los datos ya parseados
            $templateName = 'facturacion/tabla_detalle.php';
            $parsedHTML = $this::GetTemplateWithData($templateName, $data);
        }
        return $parsedHTML;
    }

    /**
     * Genera un e-mail con información del proceso de facturación para el SUDO del sistema
     */
    public function SendEmailProcesoFacturacion()
    {
        //  Recuperamos el Template de factura
        $html = $this->ParseHTMLInvoiceEmailProcesoFacturacion($this->emailBody);

        $email = ADMINMAIL;

        if($this->debugMode){
            $email = 'desarrollo@fincatech.es';
        }

        $urlXML = HelperController::RootDir() . '/public/storage/remesas/' . $this->ficheroRemesa;
        //  Enviamos sin guardar el mensaje en bbdd
        // $this->SendEmail($email, 'Fincatech', 'Proceso de Facturación Realizado', $html, false, $urlXML);
        $this->SendEmail($email, 'Fincatech', 'Proceso de Facturación Realizado', $html, false, $urlXML);

    }

    /**
     * Genera un e-mail con información del proceso de facturación para el administrador de fincas
     */
    public function SendEmailProcesoFacturacionAdministrador()
    {
        //  Recuperamos el Template de factura
        $html = $this->ParseHTMLInvoiceEmailProcesoFacturacionAdministrador();

        $email = $this->InvoiceModel->EmailAdministrador();
        
        //  Si no tiene asignado el e-mail de facturación se lo enviamos al Master
        if($email == '' || is_null($email)){
            $email = ADMINMAIL;
        }

        if($this->debugMode){
            $email = 'desarrollo@fincatech.es';
        }

        //  Enviamos sin guardar el mensaje en bbdd
        $this->SendEmail($email, 'Fincatech', 'Fincatech - Nuevas facturas disponibles', $html, false);
        $this->SendEmail(ADMINMAIL, 'Fincatech', 'Fincatech - Nuevas facturas disponibles', $html, false);

    }

    /**
     * Envía una factura individual por correo electrónico
     * @param int $invoiceId    ID de la factura que se desea enviar
     * @param array|null $data  Datos que se recogen desde el WS
     */
    public function Send($invoiceId, $data = null)
    {
        global $appSettings;

        //  Verificamos que el usuario esté autenticado y, además que coincida con el mismo que el que creó la factura ya que solo se permite al usuario de facturación.
        //  Además valida que no sea sudo. Los usuarios de tipo SUDO sí que pueden enviar cualquier factura.
        if(!$this->isFacturacion() && !$this->isSudo())
            return HelperController::errorResponse('error','Lo siento pero no tiene acceso a esta funcionalidad');

        //  Recuperamos la factura mediante su ID
        $invoice = $this->Get($invoiceId);
        //  Si no existe, enviamos el error
        if($this->InvoiceModel->Id() == '' || is_null($this->InvoiceModel->Id()))
        {
            return HelperController::errorResponse('error','La factura no existe');
        }
        
        $asunto = self::EMAIL_ASUNTO . $this->InvoiceModel->Numero();
        $cuerpo = null;
        $email = $this->InvoiceModel->Email();

        //  Descargamos temporalmente el fichero de la factura para poder adjuntarlo en el e-mail
        $url = 'https://factura.fincatech.es/pdf/' . $this->InvoiceModel->Fichero();
        if(strpos($url, '.pdf') === FALSE)
            $url .= '.pdf';

        $archivoAdjunto = null;
        $archivoDescargadoFactura = file_get_contents($url);

        if($archivoDescargadoFactura !== false)
        {
            //  Ruta temporal de almacenamiento, utilizamos la carpeta de private
            $path = HelperController::RootDir();
            $archivoAdjunto = $path . $appSettings['storage']['private'] . DIRECTORY_SEPARATOR . basename($url);
            file_put_contents($archivoAdjunto, $archivoDescargadoFactura);
        }

        //  Si viene del WS debemos de modificar los datos
        if( !is_null($data) ){
            $cuerpo = $data['cuerpo'];
            $cuerpo = str_replace('<p>', '<p style="font-family: Raleway, sans-serif !important; font-size: 14px !important; line-height: 19.6px;">', $cuerpo);
            $asunto = $data['asunto'];
            // $email = explode(';',$data['email']);
            $email = $data['email'];
        }

        //  Comprobamos si tiene factura rectificativa asociada
        if(count($invoice['Invoice'][0]['invoicerectificativa']) > 0)
        {
            $dataInvoiceRectificativa = $invoice['Invoice'][0]['invoicerectificativa'][0];
        }else{
            $dataInvoiceRectificativa = null;
        }

        $html = $this->ParseHTMLInvoiceEmail($dataInvoiceRectificativa, $cuerpo);
        //  Enviamos al administrador de fincas
        $to = $email;
        //  DEBUG: Quitar al terminar
        // $to = 'desarrollo@fincatech.es';
        $nombre = $this->InvoiceModel->Administrador();
        $this->SendEmail($to, $nombre, $asunto, $html, false, $archivoAdjunto);
        //  Eliminamos el fichero descargado puesto que ya se ha adjuntado en el e-mail
        unlink($archivoAdjunto);
        //  Enviamos al Master de Fincatech
        $this->SendEmail(ADMINMAIL, 'Fincatech', $asunto, $html, false);
        return HelperController::successResponse('ok');
    }

}