<?php

namespace Fincatech\Model;

use Fincatech\Entity\InvoiceRectificativa;
use HappySoftware\Controller\HelperController;
use HappySoftware\Model\Model;

class InvoiceRectificativaModel extends \HappySoftware\Model\Model{

    private $entidad = 'InvoiceRectificativa';
    private $tablasSchema = array("invoicerectificativa");
    //  Tabla Factura Rectificativa
    private $_tablaFacturaRectificativa = 'invoicerectificativa';

    /**
     * @var \Fincatech\Entity\InvoiceRectificativa
     */
    public $invoiceRectificativa;

    public function __construct($params = null)
    {
        $this->invoiceRectificativa = new InvoiceRectificativa();
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /**
     * Get Single Invoice Rectificativa
     * @param string $id ID de la entidad
     */
    public function Get($id)
    {
        $data = parent::Get($id);
        if(count($data['invoicerectificativa']) > 0)
        {
            $dataInvoice = $data['invoicerectificativa'][0];
            $this->invoiceRectificativa->SetId((int)$id)
            ->SetIdAdministrador($dataInvoice['idadministrador'])
            ->SetAdministrador($dataInvoice['administrador'])
            ->SetIdComunidad($dataInvoice['idcomunidad'])
            ->SetComunidad($dataInvoice['comunidad'])
            ->SetEmail($dataInvoice['email'])
            ->SetConcepto($dataInvoice['concepto'])
            ->SetCreated($dataInvoice['created'])
            ->SetIdInvoice($dataInvoice['idinvoice'])
            ->SetNombreFichero($dataInvoice['nombrefichero'])
            ->SetNumero($dataInvoice['numero'])
            ->SetTaxRate($dataInvoice['tax_rate'])
            ->SetTotalTaxesExc($dataInvoice['total_taxes_exc'])
            ->SetTotalTaxesInc($dataInvoice['total_taxes_inc'])
            ->SetUpdated($dataInvoice['updated'])
            ->SetUserCreate($dataInvoice['usercreate']);
        }

        return $data;

    }

    /**
     * Crea una factura rectificativa desde una factura ya generada
     * @param InvoiceRectificativa Entidad
     * @return bool|null
     */
    public function CreateInvoiceRectificativa(InvoiceRectificativa $invoiceRectificativa){
        //  Construimos el almacenamiento
        $data = [];
        $data['idinvoice'] = $invoiceRectificativa->IdInvoice();
        $data['idadministrador'] = $invoiceRectificativa->IdAdministrador();
        $data['administrador'] = $invoiceRectificativa->Administrador();
        $data['idcomunidad'] = $invoiceRectificativa->IdComunidad();
        $data['comunidad'] = $invoiceRectificativa->Comunidad();
        $data['email'] = $invoiceRectificativa->Email();
        $data['concepto'] = $invoiceRectificativa->Concepto();
        $data['numero'] = $invoiceRectificativa->Numero();
        $data['nombrefichero'] = $invoiceRectificativa->NombreFichero();
        $data['total_taxes_inc'] = $invoiceRectificativa->TotalTaxesInc();
        $data['total_taxes_exc'] = $invoiceRectificativa->TotalTaxesExc();
        $data['tax_rate'] = $invoiceRectificativa->TaxRate();
        $data['created'] = date('Y-m-d H:m:i');

        $id = parent::Create($this->entidad, $data);
        //  Validamos que haya insertado
        if(count($id) > 0){
            $id = $id['id'];
        }else{
            return false;
        }

        //  Validamos que haya insertado
        if(intval($id) > 0){
            //  Seteamos el ID y devolvemos la entidad 
            $invoiceRectificativa->SetId($id);
        }else{
            return false;
        }

        //  Establecemos la entidad en el modelo
        $this->invoiceRectificativa = $invoiceRectificativa;

        return $invoiceRectificativa;
    }

    /**
     * Actualiza la entidad
     */
    public function _Update()
    {
        $data = [];
        $data['idinvoice'] = $this->invoiceRectificativa->IdInvoice();
        $data['idadministrador'] = $this->invoiceRectificativa->IdAdministrador();
        $data['administrador'] = $this->invoiceRectificativa->Administrador();
        $data['idcomunidad'] = $this->invoiceRectificativa->IdComunidad();
        $data['comunidad'] = $this->invoiceRectificativa->Comunidad();        
        $data['email'] = $this->invoiceRectificativa->Email();        
        $data['concepto'] = $this->invoiceRectificativa->Concepto();
        $data['numero'] = $this->invoiceRectificativa->Numero();
        $data['nombrefichero'] = $this->invoiceRectificativa->NombreFichero();
        $data['total_taxes_inc'] = $this->invoiceRectificativa->TotalTaxesInc();
        $data['total_taxes_exc'] = $this->invoiceRectificativa->TotalTaxesExc();
        $data['tax_rate'] = $this->invoiceRectificativa->TaxRate();
        $data['updated'] = date('Y-m-d H:m:i');
        $data['id'] = $this->invoiceRectificativa->Id();

        return $this->Update($this->entidad, $data, $this->invoiceRectificativa->Id());
        
    }

    /**
     * Asigna el nombre del fichero a una factura rectificativa
     */
    public function AssignPDFFileToInvoice()
    {
        $sql = 'update ' . $this->_tablaFacturaRectificativa . " set nombrefichero = '" . $this->invoiceRectificativa->NombreFichero() . "' where id = " . $this->invoiceRectificativa->Id();
        $this->queryRaw($sql);
        return true;
        
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}