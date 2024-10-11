<?php

namespace Fincatech\Controller;

use DateTime;
use Fincatech\Model\ConfiguracionModel;
use HappySoftware\Controller\HelperController;
use HappySoftware\Database\DatabaseCore;
use stdClass;

class ConfiguracionController extends FrontController{

    public $ConfiguracionModel;
    private $configuracion;

    private $hasValues = false;
    public function HasValues(){
        return $this->hasValues;
    }
    private function SetHasValues($value){
        $this->hasValues = $value;
    }

    public function __construct($params = null)
    {
        //  $this->InitModel('Configuracion', $params);
        $this->ConfiguracionModel = new ConfiguracionModel($params);
        //  Recuperamos la configuración al iniciar el controller
        $this->LoadConfigurationValues();
    }

    /**
     * Carga todos los posibles valores con su par name -> value
     */
    private function LoadConfigurationValues()
    {
        $result = $this->ConfiguracionModel->configuracion;
        if(count($result)){
            $this->SetHasValues(true);
            //  Cargamos todos los parámetros de configuración en el modelo
            foreach($result as $configuration)
            {
                $this->ConfiguracionModel->SetValue($configuration['name'], $configuration['valor']);
            }
        }else{
            $this->SetHasValues(false);
        }
    }

    /**
     * Create user
     * @param string $entidadPrincipal. Entity Name
     * @param json $datos. JSON Object with values to create
     */
    public function Create($entidadPrincipal, $datos)
    {
        return HelperController::errorResponse('error','No tiene acceso a este método', 200);
     }

    /**
     * Actualiza la configuración global
     */
    public function Update($entidadPrincipal, $datos, $id)
    {
        //  Recuperamos todos los pares valor + clave
        foreach($datos as $key => $value)
        {

            $key = DatabaseCore::PrepareDBString($key);
            $value = DatabaseCore::PrepareDBString($value);

            if(!$this->ConfiguracionModel->Exists('name', "'" . $key . "'"))
            {

                $datos = [];
                $datos['name'] = $key;
                $datos['valor'] = $value;
                $this->ConfiguracionModel->Create('configuracion', $datos);

            }else{
                $this->ConfiguracionModel->SetValue($key, $value);
                $this->ConfiguracionModel->UpdateValue($key, $value);
            }
        }
        return 'ok';
    }

    public function getSchemaEntity()
    {
        return $this->ConfiguracionModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ConfiguracionModel->Delete($id);
    }

    public function Get($id)
    {
        $this->configuracion = $this->ConfiguracionModel->Get($id);
    }

    public function List($params = null)
    {
        $data = [];
        $data['Configuracion'] = [];
        $list = $this->ConfiguracionModel->List($params);
        if(is_array($list))
        {
            foreach($list['Configuracion'] as $itemConfiguracion){
                $data['Configuracion'][$itemConfiguracion['name']] = $itemConfiguracion['valor'];
            }
        }
        return $data;
    }

    public function GetValue($key)
    {
        $key = DatabaseCore::PrepareDBString($key);
        if($this->HasValues()){
            return $this->ConfiguracionModel->GetValue($key);
        }else{
            return null;
        }

    }

    /**
     * Actualiza un valor de forma individual en el repositorio
     */
    public function UpdateValue($key, $value)
    {
        $key = DatabaseCore::PrepareDBString($key);
        $value = DatabaseCore::PrepareDBString($value);
        $this->ConfiguracionModel->UpdateValue($key, $value);
    }

}