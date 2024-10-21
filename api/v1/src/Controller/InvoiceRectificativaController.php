<?php

namespace Fincatech\Controller;

use \HappySoftware\Controller\HelperController;
use \HappySoftware\Controller\ConfiguracionController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Controller\BankController;
use Fincatech\Controller\ComunidadController;
use Fincatech\Controller\ConfiguracionController as ControllerConfiguracionController;
use Fincatech\Controller\InvoiceController;
use Fincatech\Controller\UsuarioController;

use Fincatech\Model\InvoiceRectificativaModel;
use Fincatech\Entity\InvoiceRectificativa;
use PHPUnit\TextUI\Help;
use stdClass;

class InvoiceRectificativaController extends FrontController{

    private $debugMode = true;

    //  Prefijos ficheros PDF y ZIP
    private const FICHERO_PREFIX = 'FINCATECH_FACTRECT_';

    protected $UsuarioController, $ConfiguracionController;
    //  Modelo
    public $InvoiceRectificativaModel;
    //  Entidad
    private InvoiceRectificativa $invoiceRectificativa;
    //  Serie de facturación rectificativas
    private string $prefijoSerieFacturacionRectificativa = '';
    private $serieFacturacionRectificativa = 1;

    //  Ruta local de almacenamiento de los pdf
    private string $pdfPath;
    //  Servidor FTP que hace de almacén de documentos
    private string $invoiceServer;
    //  Cuerpo personalizado del e-mail
    public string $emailBody = '';
    //  Número de factura original
    public string $facturaOriginalNumero = '';
    //  Fecha de la factura original
    public string $facturaOriginalFecha = '';
    //  Nombre del fichero PDF generado
    public string $_pdfFileName;

    public function __construct($params = null)
    {
        global $appSettings;
        //$this->InitModel('Remesa', $params);
        $this->InvoiceRectificativaModel = new InvoiceRectificativaModel($params);
        //  Instanciamos el controller de configuración
        $this->ConfiguracionController = new ControllerConfiguracionController($params);
        //  Cargamos la configuración previamente almacenada
        $this->LoadConfiguracionFacturacion();
        //  Cargamos las rutas
        $this->pdfPath = $appSettings['storage']['facturas'] . DIRECTORY_SEPARATOR;
        $this->invoiceServer = $appSettings['ftp_servers']['facturacion']['server_url'];        
    }

    /**
     * Establece la entidad para poder leer los datos
     */
    public function SetEntity(InvoiceRectificativa $invoiceRectificativa){
        $this->invoiceRectificativa = $invoiceRectificativa;
    }

    /**
     * Carga la configuración relativa a la facturación desde bbdd: Serie, Prefijos e Impuesto
     */
    private function LoadConfiguracionFacturacion()
    {

        if($this->ConfiguracionController->HasValues())
        {
            //  Serie de facturación rectificativas
            $this->prefijoSerieFacturacionRectificativa = $this->ConfiguracionController->GetValue('prefseriefacrect');
            $this->serieFacturacionRectificativa = (float)$this->ConfiguracionController->GetValue('serierectificativa');           
        }
    }

    /**
     * Creates new invoice rectificativa
     * 
     */
    public function Create($entidadPrincipal, $datos)
    {
        return HelperController::errorResponse('error','Method not allowed', 200);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->InvoiceRectificativaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->InvoiceRectificativaModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->InvoiceRectificativaModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->InvoiceRectificativaModel->Get($id);
    }

    public function List($params = null)
    {
        $result = [];
        return $this->InvoiceRectificativaModel->List($params);
    }

    /**
     * Crea una nueva factura rectificativa
     */
    public function Insert(InvoiceRectificativa $invoiceRectificativa)
    {

        $invoiceRectificativa->SetNumero($this->GenerateNumeroFactura());
        //  Generamos el nombre del fichero que se va a utilizar para la creación del pdf
        $pdfName = $this->GeneratePDFFileName($invoiceRectificativa->Numero(), $invoiceRectificativa->Comunidad(), $invoiceRectificativa->Administrador());
        
        $this->_pdfFileName = $pdfName;

        $invoiceRectificativa->SetNombreFichero($pdfName);
        $invoiceRectificativa->SetCreated(date('Y-m-d', time()));
        //  Intentamos crear la factura rectificativa en el sistema
        $this->InvoiceRectificativaModel->CreateInvoiceRectificativa($invoiceRectificativa);
        //  Recuperamos el ID del registro recién creado
        $invoiceRectificativaId = $invoiceRectificativa->Id();
        //  Si ha ocurrido error, avisamos
        if($invoiceRectificativaId === false)
            return false;

        //  Actualizamos la serie de facturación
        $this->UpdateSerieFacturacion();
        $this->SetEntity($invoiceRectificativa);
        //  Generamos el PDF con la factura rectificativa
        $this->CreatePdfInvoiceRectificativa();
        //  Enviamos la factura rectificativa al administrador
        if(!$this->debugMode){
            $this->SendFacturaRectificativa($invoiceRectificativa);
        }

        //  Devolvemos el id de la inserción
        return $invoiceRectificativaId;
    }

    /**
     * TODO: Generación individual de PDF de factura rectificativa.
     */
    public function CreatePDFById($id)
    {
        $this->Get($id);
        //  Instanciamos la entidad
        $invoiceRectificativa = new InvoiceRectificativa();
        //  Mapeamos los datos antes de llamar a la generación del PDF


    }

    /**
     * Genera una factura rectificativa en fichero PDF
     */
    private function CreatePdfInvoiceRectificativa(string $fileName = null)
    {

        global $appSettings;

        $footerHTML = self::GetTemplate('facturacion/factura_footer.html');

        if(is_null($fileName))
            $fileName = $this->invoiceRectificativa->NombreFichero();

        //  Reemplazamos la url donde está disponible la factura para su descarga
        $invoiceServer = $appSettings['ftp_servers']['facturacion']['server_url'];
        $invoiceUrl = $invoiceServer . 'pdf/' . basename($fileName);
        $footerHTML = str_replace('[url_factura]', $invoiceUrl, $footerHTML);

        //  Generamos el nombre del fichero
        $this->InitializePDF($this->pdfPath . $fileName);
        //  Parseamos el contenido del template con los datos reales de la factura
        //  Para ello instanciamos el controller de la comuniad y recuperamos los datos de la factura original para poder mapear los datos
        $invoiceController = new InvoiceController();
        $invoiceData = $invoiceController->Get($this->invoiceRectificativa->IdInvoice());
        $parsedHTML = $this->ParseHTMLInvoice($invoiceData);

        $this->WriteToPDF($parsedHTML, $footerHTML, false, false);
        $result = $this->MakePDF();
        $this->SincronizeWithFTPServer();
        return $result;

    }

    /**
     * Recupera el template de factura y reemplaza las variables por los valores reales recuperados
     * @param array $data Datos de la factura
     * @return string HTML de salida ya parseado
     */
    private function ParseHTMLInvoice($data)
    {
        //  Si no hay datos de factura no parseamos y lanzamos excepción
        if(is_null($data))
            throw new \Exception('La factura original no ha sido encontrada');

        $factura = $data['Invoice'][0];
        //  Comunidad
        $comunidad = $factura['comunidad'][0];
       
        //  Recuperamos el template de la factura
        $html = self::GetTemplate('facturacion/factura_rectificativa.html');

        //   Logo
        $logo = ROOT_DIR . DIRECTORY_SEPARATOR . 'public/assets/img/logo-fincatech.png';
        $logo = HelperController::ConvertImageToBase64($logo);
        $html = str_replace('[logo]', $logo, $html);       

        //  Sustuimos los datos por los recuperados de la factura
        //  Administrador Id
        $html = str_replace('[administrador_id]', $this->invoiceRectificativa->IdAdministrador(), $html);
        //  Referencia contrato
        $html = str_replace('[referencia_contrato]', $factura['referenciacontrato'], $html);
        //  Nombre de la comunidad
        $html = str_replace('[comunidad_codigo]', $factura['comunidad'][0]['codigo'], $html);
        $html = str_replace('[comunidad_nombre]', $this->invoiceRectificativa->Comunidad(), $html);
        //  Localidad de la comunidad
        $html = str_replace('[comunidad_localidad]', $factura['comunidad'][0]['localidad'], $html);
        //  CIF de la comunidad
        $html = str_replace('[comunidad_cif]', $factura['comunidad'][0]['cif'], $html);
        //  Nº de Factura
        $html = str_replace('[factura_numero]', $this->invoiceRectificativa->Numero(), $html);
        //  Fecha de la factura
        $html = str_replace('[factura_fecha]', date('d-m-Y', strtotime($this->invoiceRectificativa->Created())), $html);

        //  Procesamos el detalle
        $detalleConceptos = $this->invoiceRectificativa->Concepto();
        $detalleImportes = number_format($this->invoiceRectificativa->TotalTaxesExc(), 2, ',','.') . '&euro;';

        $html = str_replace('[detalle_conceptos]', $detalleConceptos, $html);
        $html = str_replace('[detalle_importes]', $detalleImportes, $html);
        //  % IVA
        $taxRate = number_format((float)$this->invoiceRectificativa->TaxRate(), '2',',','.');
        $html = str_replace('[impuesto_porcentaje]', $taxRate , $html);
        //  TOTAL FACTURA IMPUESTOS NO INCLUIDOS
        $totalTaxesExcl = number_format((float)$this->invoiceRectificativa->TotalTaxesExc(), '2',',','.');
        $html = str_replace('[factura_total_taxes_excl]', $totalTaxesExcl, $html);
        //  TOTAL IMPUESTOS CALCULADOS
        $totalTaxes = ((float)$this->invoiceRectificativa->TotalTaxesInc() - (float)$this->invoiceRectificativa->TotalTaxesExc());
        $totalTaxes = number_format($totalTaxes, '2',',','.');
        $html = str_replace('[impuesto_importe_total]', $totalTaxes, $html);
        //  TOTAL FACTURA IMP. INCLUIDOS
        $totalTaxesInc = number_format((float)$this->invoiceRectificativa->TotalTaxesInc(), '2',',','.');
        $html = str_replace('[factura_total_taxes_inc]', $totalTaxesInc, $html);
        //  IBAN
        $comunidadIban = str_replace(' ','', $comunidad['ibancomunidad']);
        $comunidadIban = trim($comunidadIban);
        $comunidadIban = substr($comunidadIban, -9);
        $html = str_replace('[comunidad_iban]', $comunidadIban, $html);
        //  FECHA DE VENCIMIENTO
        $fechaVto = date('d-m-Y', time());
        $html = str_replace('[fecha_vencimiento]', $fechaVto, $html);
        //  Devolvemos el HTML ya parseado
        return $html;
    }

    /**
     * Sincroniza una factura rectificativa recién creada con el servidor FTP
     */
    private function SincronizeWithFTPServer()
    {
        //  Enviamos al almacén de ficheros por FTP
        $pdfFile = ROOT_DIR . $this->pdfPath . $this->invoiceRectificativa->NombreFichero();
        $pdfDestFile = $this->invoiceRectificativa->NombreFichero();
        
        if($this->FTPSendFile($pdfFile, '/pdf/' . $pdfDestFile) === true)
        {
            //  Una vez sincronizado, lo eliminamos de este servidor
            unlink($pdfFile);
        }else{
            $msgError = '<p>Ha ocurrido un error al enviar el fichero ' . $pdfDestFile . ': ' . $this->FTPError() . '</p>';
            return false;
        }        
        return true;
    }

    /**
     * Envía una factura rectificativa por e-mail al administrador de fincas y al sudo del sistema
     * @param int|null $id. (optional) ID de la factura rectificativa
     * @param InvoiceRectificativa|null $invoiceRectificativa. Entidad de la factura rectificativa
     * @return bool
     */
    public function SendFacturaRectificativa($id = null, InvoiceRectificativa $invoiceRectificativa = null)
    {

        //////////////////////////////////////////////////////////////////////
        //  Parseamos los datos del template para antes de enviar el e-mail
        //////////////////////////////////////////////////////////////////////
        $parsedEmail = $this->GetParsedEmailHTML();
        $administradorEmail = $this->invoiceRectificativa->Email();
        $sudoEmail = \ADMINMAIL;
        //TODO: Cambiar por e-mail real
        //  Enviamos al administrador de fincas y al sudo del sistema
        $this->SendEmail('desarrollo@fincatech.es', $this->invoiceRectificativa->Administrador(), 'Fincatech - Fra. Rectificativa ' . $this->invoiceRectificativa->Numero(), $parsedEmail, false);
    }

    /**
     * Genera el texto del e-mail ya parseado previo al envío del correo 
     * @return string Texto parseado
     */
    private function GetParsedEmailHTML()
    {
        //  Recuperamos el template del e-mail
        $html = self::GetEmailTemplate('facturacion/factura_rectificativa.html');
        //  Construimos el enlace de descarga de la factura rectificativa
        $urlDescarga = $this->invoiceServer . 'pdf/' . $this->invoiceRectificativa->NombreFichero();
        //   Logo
        $logo = ROOT_DIR . DIRECTORY_SEPARATOR . 'public/assets/img/logo-fincatech.png';
        $logo = HelperController::ConvertImageToBase64($logo);
        //  Nombre del administrador
        $html = str_replace('[administrador]', $this->invoiceRectificativa->Administrador(), $html);
        //  Número Fra. Rectificativa
        $html = str_replace('[factura_rectificativa_numero]', $this->invoiceRectificativa->Numero(), $html);
        //  Número Fra. 
        $html = str_replace('[factura_numero]', $this->facturaOriginalNumero, $html);
        //  Fecha factura original
        $html = str_replace('[factura_fecha]', $this->facturaOriginalFecha, $html);
        //  Importe Factura Rectificativa Impuestos incluidos
        $html = str_replace('[factura_rectificativa_importe]', number_format($this->invoiceRectificativa->TotalTaxesInc(), 2, ',','.'), $html);
        //  Nombre de la comunidad
        $html = str_replace('[comunidad_nombre]', $this->invoiceRectificativa->Comunidad(), $html);
        //  URL De descarga
        $html = str_replace('[url_factura]', $urlDescarga, $html);
        //  Cuerpo opcional
        $html = str_replace('[cuerpo_opcional]', $this->emailBody, $html);

        return $html;
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

        $pdfName = self::FICHERO_PREFIX . $numeroFactura . '_' . $nombreComunidad . '_' . date('m', time()) . '_' . date('Y', time()) . '.pdf';
        return $pdfName;        
    }

    /**
     * Genera un número de factura válido
     * @return string Número de factura con la serie
     */
    private function GenerateNumeroFactura()
    {
        return $this->prefijoSerieFacturacionRectificativa . date('y') . '/' . str_pad($this->serieFacturacionRectificativa, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Actualiza la serie de facturación según la última factura generada
     */
    private function UpdateSerieFacturacion()
    {
        $this->serieFacturacionRectificativa++;
        $this->ConfiguracionController->UpdateValue('serierectificativa', $this->serieFacturacionRectificativa);
    }


}