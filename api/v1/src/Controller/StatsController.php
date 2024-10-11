<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\SpaModel;

class StatsController extends FrontController{

    private $statsModel;
    public $StatsModel;

    public function __construct($params = null)
    {
       $this->InitModel('Stats', $params);
    }

    public function List($params = null)
    {
        $data = [];
        $data['empresas'] = number_format($this->StatsModel->TotalEmpresas(),0,',','.');
        $data['comunidades'] = number_format($this->StatsModel->TotalComunidades(),0,',','.');
        //  Nº de servicios contratados por las comunidades
        $serviciosContratadosTotales = $this->StatsModel->ServiciosContratadosComunidades();
        foreach($serviciosContratadosTotales as $key => &$value)
        {
            $value = number_format($value, 0,',','.');
        }
        $data['servicioscomunidades'] = $serviciosContratadosTotales;
        //  Servicios
        $data['servicios'] = number_format($this->StatsModel->TotalServiciosFacturados(),0,',','.');
        $data['administradores'] = number_format($this->StatsModel->TotalAdministradores(),0,',','.');
        $data['emailscertificados'] = number_format($this->StatsModel->TotalEmailsCertificados(),0,',','.');
        $data['emailsenviados'] = number_format($this->StatsModel->TotalEmailsEnviados(),0,',','.');
        return $data;
    }

}