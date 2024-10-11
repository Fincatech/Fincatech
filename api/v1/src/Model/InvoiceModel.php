<?php

namespace Fincatech\Model;

use Exception;
use Fincatech\Entity\Facturacion;

class InvoiceModel extends \HappySoftware\Model\Model{

    private $tablaFacturacion = 'invoice';
    private $tablaFacturacionDetail = 'invoicedetail';
    private $tableIngresosCuenta = 'ingresoscuenta';
    private $tableLiquidaciones = 'liquidaciones';
    private $tableRemesas = 'remesa';

    //  ID
    public $id;
    //  ID Administrador
    private $_idAdministrador;
    //  Nombre de fichero generado
    private $_fichero = '';
    //  ID Factura rectificativa
    private $_idrectificativa = null;
    //  ID Comunidad
    private $_idComunidad;
    private $_referenciaContrato;
    //  Número de factura
    private $_numero;
    //  Total Taxes EXC
    private $_total_taxes_exc;
    //  Total Taxes INC
    private $_total_taxes_inc;
    //  TAX RATE
    private $_tax_rate;
    //  Fecha de factura
    private $_dateinvoice;
    //  Fecha de pago
    private $_datepaid;
    //  Fecha de devolución
    private $_datereturned;
    private $_mes;
    private $_anyo;
    //  Nombre del administrador
    private $_administrador;
    //  Email del administrador
    private $_emailAdministrador;    
    //  CIF Del administrador
    private string $_cifAdministrador;
    //  Comunidad
    private $_comunidad;
    //  CIF Comunidad
    private $_cifComunidad;
    private $_codigoComunidad;
    //  IBAN
    private $_iban;
    //  Email
    private $_email;
    //  Notas
    private $_notas;
    //  ¿Liquidada?
    private $_liquidada = 0;
    //  Estado
    private $_estado;
    //  Created
    public $created;
    //  Updated
    public $updated;
    //  UserCreate
    public $usercreate;
    
    /**
     * ID Factura rectificativa
     */
    public function IdRectificativa(){
        return $this->_idrectificativa;
    }
    public function SetIdRectificativa($value){
        $this->_idrectificativa = $value;
        return $this;
    }


    /**
     * ID
     */
    public function Id(){return $this->id;}
    public function setId($value){
        $this->id = $value;
        return $this;
    }

    /**
     * ID del administrador
     */
    public function IdAdministrador(){return $this->_idAdministrador;}
    public function SetIdAdministrador($value){
        $this->_idAdministrador = $value;
        return $this;
    }

    /**
     * ID de la comunidad
     */
    public function IdComunidad(){return $this->_idComunidad;}
    public function SetIdComunidad($value){
        $this->_idComunidad = $value;
        return $this;
    }

    /**
     * Referencia de la factura según los contratos
     */
    public function ReferenciaContrato(){ return $this->_referenciaContrato;}
    public function SetReferenciaContrato($value)
    {
        $this->_referenciaContrato = $value;
        return $this;
    }

    /**
     * Número y serie de factura
     */
    public function Numero(){return $this->_numero;}
    public function SetNumero($value){
        $this->_numero = $value;
        return $this;
    }

    /**
     * Total impuestos excluidos
     */
    public function TotalTaxesExc(){return $this->_total_taxes_exc;}
    public function SetTotalTaxesExc($value){
        $this->_total_taxes_exc = $value;
        return $this;
    }

    /**
     * Total impuestos incluidos
     */
    public function TotalTaxesInc(){return $this->_total_taxes_inc;}
    public function SetTotalTaxesInc($value){
        $this->_total_taxes_inc = $value;
        return $this;
    }

    /**
     * % Impuesto aplicado
     */
    public function TaxRate(){return $this->_tax_rate;}
    public function SetTaxRate($value){
        $this->_tax_rate = $value;
        return $this;
    }

    /**
     * Fecha de la factura
     */
    public function DateInvoice(){return $this->_dateinvoice;}
    public function SetDateInvoice($value){
        $this->_dateinvoice = $value;
        return $this;
    }

    /**
     * Fecha de pago
     */
    public function DatePaid(){return $this->_datepaid;}
    public function SetDatePaid($value){
        $this->_datepaid = $value;
        return $this;
    }

    /**
     * Fecha de devolución de factura
     */
    public function DateReturned(){return $this->_datereturned;}
    public function SetDateReturned($value){
        $this->_datereturned = $value;
        return $this;
    }


    /**
     * Set Mes de facturación
     */
    public function SetMes($value){
        $this->_mes = $value;
        return $this;
    }
    /**
     * Mes de facturación
     */
    public function Mes(){
        return $this->_mes;
    }


    /**
     * Set Año de facturación
     */
    public function SetAnyo($value){
        $this->_anyo = $value;
        return $this;
    }
    /**
     * Año de facturación
     */
    public function Anyo(){
        return $this->_anyo;
    }

    /**
     * Nombre del administrador
     */
    public function Administrador(){return $this->_administrador;}
    public function SetAdministrador($value){
        $this->_administrador = $value;
        return $this;
    }

    /**
     * Email del administrador
     */
    public function EmailAdministrador(){return $this->_emailAdministrador;}
    public function SetEmailAdministrador($value){
        $this->_emailAdministrador = $value;
        return $this;
    }

    /**
     * Cif del administrador
     */
    public function CifAdministrador(): string{
        return $this->_cifAdministrador;
    }
    /**
     * Sets Cif del Administrador
     * @param string $value CIF
     */
    public function SetCifAdministrador(string $value){
        $this->_cifAdministrador = $value;
        return $this;
    }

    /**
     * Nombre de la comunidad
     */
    public function Comunidad(){return $this->_comunidad;}
    public function SetComunidad($value){
        $this->_comunidad = $value;
        return $this;
    }

    /**
     * Cif de la comunidad
     */
    public function CifComunidad(){ return $this->_cifComunidad;}
    public function SetCifComunidad($value){
        $this->_cifComunidad = $value;
        return $this;
    }

    /**
     * Set Código de la comunidad
     * @param string $value
     */
    public function SetCodigoComunidad($value){
        $this->_codigoComunidad = $value;
        return $this;
    }
    /**
     * Código de la comunidad
     * @return string Código de la comunidad
     */
    public function CodigoComunidad(){return $this->_codigoComunidad;}


    /**
     * IBAN de la comunidad
     * @return string IBAN
     */
    /**
     * IBAN de la comunidad
     */
    public function IBAN(){return trim(str_replace(' ', '', $this->_iban));}
    public function SetIBAN($value){
        $this->_iban = ltrim(rtrim(trim(str_replace(' ', '', $value))));
        return $this;
    }


    /**
     * Email de facturación del administrador
     */
    public function Email(){return $this->_email;}
    public function SetEmail($value){
        $this->_email = $value;
        return $this;
    }


    /**
     * Notas sobre la factura
     */
    public function Notas(){return $this->_notas;}
    public function SetNotas($value){
        $this->_notas = $value;
        return $this;
    }

    /**
     * Nombre de fichero
     */
    public function Fichero(){ return $this->_fichero;}
    public function SetFichero(string|null $value){
        $this->_fichero = $value;
        return $this;
    }

    /**
     * ¿Factura incluida en liquidación?
     */
    public function Liquidada(){return  $this->_liquidada;}
    public function SetLiquidada($value){
        $this->_liquidada = (int)$value;
        return $this;
    }

    /**
     * Estado de la factura
     */
    public function Estado(){return $this->_estado;}
    public function SetEstado($value){
        $this->_estado = $value;
        return $this;
    }

    public function Created(){
        return $this->created;
    }
    public function setCreated($value){
        $this->created = $value;
        return $this;
    } 


    public function Updated(){
        return $this->updated;
    }
    public function setUpdated($value){
        $this->updated = $value;
        return $this;
    } 


    public function UserCreate(){
        return $this->usercreate;
    }
    public function setUserCreate($value){
        $this->usercreate = $value;
        return $this;
    }     

    //  PROPIEDADES
    private $anual = true;
    public function SetAnual($value){
        $this->anual = $value;
        return $this;
    }
    public function Anual(){
        return $this->anual;
    }
    //  Mes facturación
    private $month;
    public function SetMonth($value){
        $this->month = $value;
        return $this;
    }
    public function Month(){
        return $this->month;
    }

    //  Año facturación
    private $year;
    public function SetYear($value){
        $this->year = $value;
        return $this;
    }
    public function Year(){
        return $this->year;
    }

    //  ID Comunidad
    private $comunidadId = null;
    public function SetComunidadId($value){
        $this->comunidadId = (int)$value;
        return $this;
    }
    public function ComunidadId(){
        return $this->comunidadId;
    }

    //  ID Servicio
    private $serviceId = null;
    public function SetServiceId($value){
        $this->serviceId = (int)$value;
        return $this;
    }
    public function ServiceId(){
        return $this->serviceId;
    }

    /**
     * Líneas de detalle
     */
    private $detailLines = [];
    public function AddDetailLine($value){
        $this->detailLines[] = $value;
    }
    public function DetailLines(){
        return $this->detailLines;
    }
    /**
     * Inicializa el detalle
     */
    public function ClearDetailLines(){
        $this->detailLines = [];
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////
    /// entidades relacionadas
    ////////////////////////////////////////////////////////////////////////////////
    private $_administradores;
    public function SetAdministradores($value){
        $this->_administradores = $value;
        return $this;
    }
    public function Administradores(){
        return $this->_administradores;
    }

    private $services = Array('1,2,5');
    public function SetServices($value){
        $this->services = $value;
        return $this;
    }
    /**
     * @return Array
     */
    public function Services(){
        return $this->services;
    }

    private $entidad = 'Invoice';

    private $tablasSchema = array('Invoice');

    /**
     * @var \Fincatech\Entity\Invoice
     */
    public $invoice;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function Get($id)
    {
        //  Recuperamos los datos de la factura
        $invoice = parent::Get($id);
        //  Comprobamos si existe la factura
        if(count($invoice['Invoice']) > 0)
        {
            $invoiceData = $invoice['Invoice'][0];
            $comunidad = $invoiceData['comunidad'][0];
            //  Rellenamos el modelo
            $this->setId($id)
            ->SetIdAdministrador($invoiceData['idadministrador'])
            ->SetIdComunidad($invoiceData['idcomunidad'])
            ->SetIdRectificativa($invoiceData['idrectificativa'])
            ->SetReferenciaContrato($invoiceData['referenciacontrato'])
            ->SetNumero($invoiceData['numero'])
            ->SetMes($invoiceData['mes'])
            ->SetAnyo($invoiceData['anyo'])
            ->SetTotalTaxesExc($invoiceData['total_taxes_exc'])
            ->SetTotalTaxesInc($invoiceData['total_taxes_inc'])
            ->SetTaxRate($invoiceData['tax_rate'])
            ->SetDateInvoice($invoiceData['dateinvoice'])
            ->SetAdministrador($invoiceData['administrador'])
            ->SetComunidad($comunidad['nombre'])
            ->SetEmail($invoiceData['email'])
            ->SetIBAN($invoiceData['iban'])
            ->SetEstado($invoiceData['estado'])
            ->SetFichero($invoiceData['fichero'])
            ->SetUpdated(date('Y-m-d H:m:i'))
            ->SetLiquidada($invoiceData['liquidada']);

            //  Detalle de la factura
            for($i = 0; $i < count($invoiceData['invoicedetail']); $i++)
            {
                $this->AddDetailLine($invoiceData['invoicedetail'][$i]);
            }

        }else{
            throw new Exception('Factura No encontrada');
        }
        return $invoice;
    }

    /**
     * Guarda en base de datos la información según los datos del modelo
     */
    public function _Save()
    {

        $data = [];
        $data['idadministrador'] = $this->IdAdministrador();
        $data['idcomunidad'] = $this->IdComunidad();
        $data['referenciacontrato'] = $this->ReferenciaContrato();
        $data['numero'] = $this->Numero();
        $data['mes'] = $this->Mes();
        $data['anyo'] = $this->Anyo();
        $data['total_taxes_exc'] = $this->TotalTaxesExc();
        $data['total_taxes_inc'] = $this->TotalTaxesInc();
        $data['tax_rate'] = $this->TaxRate();
        $data['dateinvoice'] = $this->DateInvoice();
        $data['administrador'] = $this->Administrador();
        $data['comunidad'] = $this->Comunidad();
        $data['email'] = $this->Email();
        $data['iban'] = $this->IBAN();
        $data['estado'] = $this->Estado();
        $data['fichero'] = $this->Fichero();
        $data['liquidada'] = $this->Liquidada();
        return $this->Create($this->entidad, $data);

    }

    /** TODO:
     * Recupera el estado de la facturación según los parámetros seleccionados
     */
    public function EstadoFacturacion()
    {
        /**
         * pendientes
         * pendientes_total
         * cobradas
         * cobradas_total
         * devueltas
         * devueltas_total
         */
        //$sql = "select * from " . $this->tablaFacturacion;
        $sql = "
            select 
                COUNT(case when i.estado = 'P' then 1 else null end) as pendientes,
                SUM(case when i.estado = 'P' then i.total_taxes_inc else 0 end) as pendientes_total,
                COUNT(case when i.estado = 'C' then 1 else  null end) as cobradas,
                SUM(case when i.estado = 'C' then i.total_taxes_inc else 0 end) as cobradas_total,
                COUNT(case when i.estado = 'D' then 1 else  null end) as devueltas,
                SUM(case when i.estado = 'D' then i.total_taxes_inc else 0 end) as devueltas_total
            from 
                invoice i";

        $condition = '';

        $oCondition = [];
        
        //  Mes de facturación
        if($this->Month() !== '' && !is_null($this->Month())){
            $oCondition[] = 'i.mes = ' . (int)$this->Month();
        }

        //  Año de facturación
        if($this->Year() !== '' && !is_null($this->Year())){
            $oCondition[] = 'i.anyo = ' . (int)$this->Year();
        }else{
            $oCondition[] = 'i.anyo = ' . date('Y');
        }

        //  Validate if administradorId is seted
        if(!is_null($this->IdAdministrador())){
            $oCondition[] = " i.idadministrador = " . $this->IdAdministrador() . " ";
        }


        if(count($oCondition) > 0){
            $sql .= ' WHERE ' . implode(' AND ', $oCondition);
        }

        return $this->query($sql);
    }

    /**
     * Recupera el total de la facturación anual
     * @param bool $anual. Indica si el totalizado es por año
     * @param int $mes (Opcional). Mes para el que se va a calcular el totalizado
     * @param int $anyo (Opcional). Año para el que se va a calcular el totalizado
     */
    public function TotalFacturacion()
    {

        //  Validamos el valor de services
        $services = $this->Services();
        if(is_array($services)){
            $services = implode(',',$services);
        }

        $sql = "
            select 
                count(total_facturacion.comunidad_id) as total_comunidades,
                IF(cae is null, 0, sum(cae) ) as total_cae, 
			    IF(dpd is null, 0, sum(dpd)) as total_dpd, 
                IF(certificados is null, 0, sum(certificados)) as total_certificados, 
                IF(total_factura is null, 0, sum(total_factura)) as total
            FROM (
                SELECT 
                    c.id AS comunidad_id,
                    c.nombre AS comunidad_nombre,
                    SUM(CASE WHEN ts.id = 1 THEN csc.preciocomunidad ELSE 0 END) AS cae,
                    SUM(CASE WHEN ts.id = 2 THEN csc.preciocomunidad ELSE 0 END) AS dpd,
                    SUM(CASE WHEN ts.id = 5 THEN csc.preciocomunidad ELSE 0 END) as certificados,
                    SUM(CASE WHEN ts.id = 1 THEN csc.preciocomunidad ELSE 0 END) + SUM(CASE WHEN ts.id = 2 THEN csc.preciocomunidad ELSE 0 END) + SUM(CASE WHEN ts.id = 5 THEN csc.preciocomunidad ELSE 0 END) AS total_factura
                FROM
                    comunidad c
                    INNER JOIN comunidadservicioscontratados csc ON c.id = csc.idcomunidad
                    INNER JOIN tiposservicios ts ON ts.id = csc.idservicio
                WHERE
                    ts.id IN (". $services .")
                    and c.estado = 'A' ";

        //  Validate if are anual
        if(!$this->Anual())
        {
            $sql .= "and csc.mesfacturacion = " . intval($this->Month());
        }

        //  Validate if administradorId is seted
        if(!is_null($this->IdAdministrador())){
            $sql .= " and c.usuarioid = " . $this->IdAdministrador() . " ";
        }

        $sql .= " GROUP BY 
                c.id
            ) as total_facturacion";

        return $this->query($sql);
    }

    /**
     * Recupera los id's de los servicios contratados por un administrador según las comunidades que tenga en el sistema en estado de alta
     * @return array Resultado
     */
    public function IdsServiciosContratados()
    {
        $sql = "
            SELECT csc.idservicio
            FROM
                comunidadservicioscontratados csc
                join comunidad c on c.id = csc.idcomunidad and c.estado = 'A'
            WHERE
                csc.contratado = 1
                and c.usuarioid = {$this->IdAdministrador()}
            group by csc.idservicio";
        // La sentencia UNION ALL SELECT NULL se utiliza para garantizar que siempre tenga valores la consulta
        return $this->query($sql);
    }

    /**
     * Recupera los servicios contratados por un administrador según las comunidades que tenga en el sistema en estado de alta
     * @return array Resultado
     */
    public function ServiciosContratados()
    {
        $sql = "select
            MAX(CASE WHEN servicioscontratados.idservicio = 1 THEN 1 ELSE 0 END) AS cae,
            MAX(CASE WHEN servicioscontratados.idservicio = 2 THEN 1 ELSE 0 END) AS dpd,
            MAX(CASE WHEN servicioscontratados.idservicio = 5 THEN 1 ELSE 0 END) AS certificadosdigitales
        from(
            SELECT csc.idservicio
            FROM
                comunidadservicioscontratados csc
                join comunidad c on c.id = csc.idcomunidad and c.estado = 'A'
            WHERE
                csc.contratado = 1
                and c.usuarioid = {$this->IdAdministrador()}
            group by csc.idservicio
            UNION ALL SELECT NULL
            ) as servicioscontratados";
        // La sentencia UNION ALL SELECT NULL se utiliza para garantizar que siempre tenga valores la consulta
        return $this->query($sql);
    }

    /**
     * Método de búsqueda en el repositorio de Facturas
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
     * Remesas asociadas a la factura
     */
    public function RemesaAsociada(int $idFactura)
    {
        $sql = "select r.id as idremesa, r.customername, r.referencia, r.creationdate as dateremesa, r.creditoraccountiban as ibandomiciliacion,
                i.mes, i.anyo
                from remesadetalle rd, remesa r, invoice i
                where rd.invoiceid = $idFactura
                and r.id = rd.idremesa
                and i.id = rd.invoiceid";
        return $this->query($sql);
    }

    /**
     * Calcula el total de ingresos a cuenta pendientes de liquidar
     */
    public function TotalIngresosCuentaPendienteLiquidacion()
    {
        $sql = 'select sum(total) as total from ' . $this->tableIngresosCuenta . ' where idliquidacion is null';
        $result = $this->query($sql, false);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Importe total facturas pendientes de cobro
     */
    public function PendientesCobro(){
        $sql = 'select sum(total_taxes_inc) as total_importe, count(id) as total_facturas from ' . $this->tablaFacturacion . " where estado = 'P' ";
        $result = $this->query($sql, false);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Calcula el total de liquidaciones realizadas a lo largo del tiempo
     */
    public function TotalLiquidaciones(){
        $sql = 'select sum(total_taxes_inc) as total from ' . $this->tablaFacturacion . " where estado = 'P' ";
        $result = $this->query($sql, false);
        return mysqli_fetch_assoc($result);
    }

    public function TotalRemesas(){
        return $this->getTotalRows($this->tableRemesas);
    }

    /**
     * Mejor cliente en base al total de la facturación realizada
     */
    public function BestCustomer(){
        $sql = "select 
                    sum(i.total_taxes_inc) as total, i.idadministrador , administrador 
                from invoice i
                where 
                    estado = 'P'
                group by idadministrador
                order by total asc
                limit 1";
        $result = $this->query($sql, false);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Asigna el ID de una factura rectificativa a una factura existente
     */
    public function AssignIdFacturaRectificativa()
    {
        $sql = "update " . $this->tablaFacturacion . ' set idrectificativa = ' . $this->IdRectificativa() . ' where id = ' . $this->Id();
        $this->queryRaw($sql);
        return true;
    }

    /**
     * Asigna el nombre de un fichero PDF a una factura
     */
    public function AssignPDFFileToInvoice()
    {
        $sql = "update " . $this->tablaFacturacion . " set fichero = '" . $this->Fichero() . "' where id = " . $this->Id();
        $this->queryRaw($sql);
        return true;
    }

    /**
     * Cambia el estado de liquidación de una factura
     */
    public function ChangeLiquidacionStatus()
    {
        $sql = "update " . strtolower($this->entidad) . ' set liquidada = ' . $this->Liquidada() . " where id = " . $this->Id();
        $this->queryRaw($sql);
    }

    /**
     * Actualiza el estado de una factura al estado que corresponda
     */
    public function UpdateStatus()
    {
        $sql = "update " . strtolower($this->entidad) . " set estado = '" . $this->Estado() . "' where id = " . $this->Id();
        $this->queryRaw($sql);
    }

}