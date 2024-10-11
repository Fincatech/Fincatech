<?php

namespace Fincatech\Model;

use Fincatech\Entity\Remesa;
use HappySoftware\Controller\HelperController;
use HappySoftware\Model\Model;

class RemesaModel extends \HappySoftware\Model\Model{

    private $entidad = 'Remesa';
    private $tablasSchema = array("remesa");

    /**
     * @var \Fincatech\Entity\Remesa
     */
    public $remesa;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function Get($id)
    {

        global $appSettings;
        $pathRemesas = $appSettings['storage']['remesas'] . '/';

        $totalamount = 0;
        $data = parent::Get($id);
        //  Comprobamos si tiene información, si la tiene, rellenamos el modelo
        if(count($data[$this->entidad]) > 0)
        {
            //  Fecha de generación convertimos a spanish iso
            $created = $data[$this->entidad][0]['created'];
            $data[$this->entidad][0]['created'] = date('d/m/Y H:i', strtotime($created));

            //  Recibos
            $data[$this->entidad][0]['numerorecibos'] = count($data[$this->entidad][0]['remesadetalle']);
            //  Total de la remesa
            $sumatorio = array_sum(array_column($data[$this->entidad][0]['remesadetalle'], 'amount'));
            $data[$this->entidad][0]['totalamount'] = number_format($sumatorio, 2, ',','.') . '€';
            //  Ruta del fichero
            $remesaFile = HelperController::RootURL() . $pathRemesas . $data[$this->entidad][0]['referencia'] . '.xml';
            $data[$this->entidad][0]['remesafile'] = $remesaFile;
        }
        return $data;

    }

    private function RecibosRemesa(){
        $sql = "select sum(";
    }

    public function CreateRemesa(Remesa $remesa)
    {
        //  Construimos los datos que se van a almacenar
        $data = [];
        $data['referencia'] = $remesa->Referencia();
        $data['creationdate'] = $remesa->CreationDate();
        $data['creditorname'] = $remesa->CreditorName();
        $data['creditoraccountiban'] = $remesa->CreditorAccountIBAN();
        $data['creditoragentbic'] = $remesa->CreditorAgentBIC();
        $data['creditorid'] = $remesa->CreditorId();
        $data['customerid'] = $remesa->CustomerId();
        $data['customername'] = $remesa->CustomerName();

        //  Almacenamos en el repositorio
        $id = parent::Create($this->entidad, $data);
        if(count($id) > 0){
            $id = $id['id'];
        }else{
            return false;
        }
        //  Validamos que haya insertado
        if(intval($id) > 0){
            //  Seteamos el ID y devolvemos la entidad 
            $remesa->SetId($id);
        }else{
            return false;
        }

        return $remesa;
    }

    public function _Update($remesa){

    }

    /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = false)
    {
        $sql = "select * from remesa r
        left join (select idremesa, sum(amount) as totalremesa, count(idremesa) as totalrecibos from remesadetalle group by idremesa) as rd on rd.idremesa = r.id";
        return $this->query($sql);
    }

    public function Recibos($idRemesa)
    {
        $sql = "select * from remesadetalle where idremesa = " . $idRemesa;
        return $this->query($sql);
    }

}