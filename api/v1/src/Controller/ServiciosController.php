<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\ServiciosModel;
use HappySoftware\Controller\HelperController;
use Fincatech\Controller\ComunidadController;

class ServiciosController extends FrontController{

    public $ComunidadController;

    private $serviciosModel;
    public $ServiciosModel;

    public function __construct($params = null)
    {
        //  $this->InitModel('Servicios', $params);
        $this->ServiciosModel = new ServiciosModel($params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->ServiciosModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        if(isset($datos['type']))
        {
            $result = 'ko';

            switch($datos['type'])
            {
                case 'bulk':
                    $this->processBulkUpdate($datos['servicesdata']);
                    $result = 'ok';
                    break;
                case 'single':
                    $this->processSingleUpdate($datos);
                    $result = 'ok';
                    break;
            }
            return HelperController::successResponse($result);
        }else{
            return HelperController::successResponse($this->ServiciosModel->Update($entidadPrincipal, $datos, $usuarioId)); 
        }
    }

    /** Comprueba si una comunidad ya tiene un servicio asociado */
    private function comunidadTieneServicio($idComunidad, $idServicio)
    {
        return ($this->ServiciosModel->getRepositorio()->ExisteRegistro('comunidadservicioscontratados', "idcomunidad = $idComunidad and idservicio = $idServicio"));
    }

    /** Construye el cuerpo que se va a enviar para actualizar o insertar
     * @param object $data Dato original que se va a procesar
     * @return object Devuelve un array asociativo con los datos ya procesados
     */
    private function constructServiceDataToSave($data)
    {

        $serviceData = [];

        $serviceData['idcomunidad'] = $data['idcomunidad'];
        $serviceData['id'] = $data['id'];
        $serviceData['idservicio'] = $data['idtiposervicio'];
        $serviceData['contratado'] = isset($data['contratado']) ? ($data['contratado'] === true ? 1 : 0) : 0;
        $serviceData['precio'] = isset($data['precio']) ? $data['precio'] : 0;
        $serviceData['preciocomunidad'] = isset($data['preciocomunidad']) ? $data['preciocomunidad'] : 0;
        $serviceData['mesfacturacion'] = isset($data['mesfacturacion']) ? ($data['mesfacturacion'] !== '' ? $data['mesfacturacion'] : 12) : 12;

        return $serviceData;

    }

    private function processSingleUpdate($data)
    {
        //  Mapeamos los datos de la comunidad
        $this->ServiciosModel->SetIdComunidad($data['Id Comunidad']);
        $servicios = Array('DPD', 'CAE','Certificados Digitales');

        for($iServicio = 0; $iServicio < count($servicios); $iServicio++)
        {
            $contratado = $data[$servicios[$iServicio] . ' Contratado'];
            $precio = $data[$servicios[$iServicio] . ' Precio'];
            $precioComunidad = $data[$servicios[$iServicio] . ' Precio Comunidad'];
            $mesFacturacion = $data[$servicios[$iServicio] . ' Mes Facturación'];
            // $mesFacturacion = $data['Mes Facturación'];

            $this->ServiciosModel->SetId($data['ID Interno ' . $servicios[$iServicio]]);
            $this->ServiciosModel->SetContratado($contratado);
            $this->ServiciosModel->SetPrecio( $precio );
            $this->ServiciosModel->SetPrecioComunidad( $precioComunidad );
            $this->ServiciosModel->SetMesFacturacion( $mesFacturacion );
            $this->ServiciosModel->UpdateServicioComunidad();
        }
    }

    private function processBulkUpdate($bulkData)
    {
        if(is_array($bulkData) && @count($bulkData) > 0)
        {
            //  Por cada uno de los servicios actualizamos la información
            foreach($bulkData as $servicio)
            {

                //  Si el id es 0 quiere decir que no está presente en la bbdd y por tanto hay que añadirlo
                    $servicioComunidad = $this->constructServiceDataToSave($servicio);

                //  Comprobamos si existe para actualizar o insertar

                if(($this->comunidadTieneServicio($servicioComunidad['idcomunidad'], $servicioComunidad['idservicio'])))
                {
                    //  Hay que recuperar el ID
                    $this->ServiciosModel->Update('comunidadservicioscontratados', $servicioComunidad, $servicioComunidad['id']);
                }else{
                    //  Hay que quitar el ID
                    unset($servicioComunidad['id']);
                    $this->ServiciosModel->Create('comunidadservicioscontratados', $servicioComunidad);
                }

            }

        }

        return 'ok';
    }

    public function getSchemaEntity()
    {
        return $this->ServiciosModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ServiciosModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ServiciosModel->Get($id);
    }

    public function List($params = null, $useLoggedUserId = false)
    {
       return $this->ServiciosModel->List($params, $useLoggedUserId);
    }

    /**
     * Genera un fichero excel con las comunidades que tiene un administrador así como el estado y precio de los servicios de las comunidades
     * @param int $idAdministrador. ID del administrador
     * @return string Fichero codificado en base64
     */
    public function ExportServiciosByAdministradorId($idAdministrador)
    {
        //  Instanciamos el controller de comunidad para recuperar las comunidades junto con sus servicios por el id del administrador que 
        //  se va a exportar
        $this->InitController('Comunidad');
        $data = $this->ComunidadController->ListComunidadesWithServicesByAdministradorId($idAdministrador);
        //  Metemos la información en un array asociativo por id de comunidad
        if(count($data) <= 0)
        {
            return HelperController::errorResponse('Error','Este administrador no tiene comunidades asociadas', 200);
        }else{
            $comunidadesExcel = [];
            //  La primera fila son las columnas que va a tener el excel
            $comunidadesExcel[] = Array('Id Comunidad', 'Código comunidad', 'Comunidad', 'Dirección', 'Localidad', 'Provincia','Cif','IBAN','Mes Facturación', 
                    'ID Interno CAE','CAE Contratado','CAE Precio','CAE Precio Comunidad', 'CAE Mes Facturación', 
                    'ID Interno DPD','DPD Contratado', 'DPD Precio', 'DPD Precio Comunidad', 'DPD Mes Facturación',
                    'ID Interno Certificados Digitales','Certificados Digitales Contratado', 'Certificados Digitales Precio', 'Certificados Digitales Precio Comunidad', 'Certificados Digitales Mes Facturación');
            for($x = 0; $x < count($data); $x++)
            {
                $comunidad = $data[$x];
                if(!isset($comunidadesExcel[$comunidad['idcomunidad']]))
                {
                    $comunidadesExcel[$comunidad['idcomunidad']] = [];
                    $infoExcel = [];
                    $infoExcel['idcomunidad'] = $comunidad['idcomunidad'];
                    $infoExcel['codigo'] = $comunidad['codigo'];
                    $infoExcel['comunidad'] = $comunidad['comunidad'];
                    $infoExcel['direccion'] = $comunidad['direccion'];
                    $infoExcel['localidad'] = $comunidad['localidad'];
                    $infoExcel['provincia'] = $comunidad['provincia'];
                    $infoExcel['cif'] = $comunidad['cif'];
                    $infoExcel['ibancomunidad'] = $comunidad['ibancomunidad'];
                    $infoExcel['mesfacturacion'] = $comunidad['comunidadmesfacturacion'];
                    $comunidadesExcel[$comunidad['idcomunidad']] = $infoExcel;
                }

                $servicio = null;
                $insertServicio = true;
                switch($comunidad['idservicio'])
                {
                    case 1: // CAE
                        $servicio = 'cae';
                        break;
                    case 2: // RGPD
                        $servicio = 'dpd';
                        break;
                    case 3:
                    case 4:
                        break;
                    case 5: //  Certificados digitales
                        $servicio = 'certificados_digitales';
                        break;
                }

                if(!is_null($servicio))
                {
                    //  Insertamos los precios de los servicios y el estado del mismo
                    $comunidadesExcel[$comunidad['idcomunidad']][$servicio.'_idservicio']= $comunidad['id'];
                    $comunidadesExcel[$comunidad['idcomunidad']][$servicio.'_servicio_contratado'] = $comunidad['servicio_contratado'];
                    $comunidadesExcel[$comunidad['idcomunidad']][$servicio.'_precio'] = $comunidad['precio'];
                    $comunidadesExcel[$comunidad['idcomunidad']][$servicio.'_preciocomunidad'] = $comunidad['preciocomunidad'];
                    $comunidadesExcel[$comunidad['idcomunidad']][$servicio.'_mesfacturacion'] = $comunidad['comunidadmesfacturacion'];
                }

            }
            
            global $appSettings;

            //  Generamos el fichero
            $nombreFichero = $appSettings['storage']['path'].'Fincatech_Exportacion_comunidades_' . date('d-M-Y') . '.xlsx';
            if(file_exists(ROOT_DIR . $nombreFichero)){
                unlink(ROOT_DIR . $nombreFichero);
            }

            //  Mapea la información para que al generar el fichero se almacene correctamente
            // $comunidadesExcel = $this->ProcessArrayDataToExcel($comunidadesExcel);

             \HappySoftware\Controller\Traits\ExcelGen::fromArray($comunidadesExcel, 'comunidades')->saveAs(ROOT_DIR . $nombreFichero);
            // return HelperController::successResponse($nombreFichero);

            //  Abrimos el fichero y lo codificamos en base64 para enviar de vuelta
            $contenido = file_get_contents(ROOT_DIR . $nombreFichero);

            // Codificar en base64
            $base64 = base64_encode($contenido);

            //  Eliminamos el fichero del servidor ya que no se va a generar ningún enlace de descarga ni a dejarlo físicamente
            unlink(ROOT_DIR . $nombreFichero);

            //  Devolvemos el resultado en formato base64
            return HelperController::successResponse($data[] = $base64, 200);
        }
    }


    private function ProcessArrayDataToExcel($datosComunidad)
    {
        //  Datos del fichero      
            $datosParseados = [];
        //  Cabecera del fichero
            $datosParseados[0] = [];
            $datosParseados[0]['col'] = 'ID Comunidad';
            $datosParseados[0]['col1'] = 'Código comunidad';
            $datosParseados[0]['col2'] = 'Nombre comunidad';
            $datosParseados[0]['col3'] = 'Dirección';
            $datosParseados[0]['col4'] = 'Localidad';
            $datosParseados[0]['col5'] = 'Provincia';
            $datosParseados[0]['col6'] = 'CIF';
            $datosParseados[0]['col7'] = 'IBAN Comunidad';
            $datosParseados[0]['col8'] = 'Mes Facturación';
        //  CAE
            $datosParseados[0]['col9'] = 'ID Cae Comunidad';
            $datosParseados[0]['col10'] = 'Cae Contratado';
            $datosParseados[0]['col11'] = 'Cae Precio';
            $datosParseados[0]['col12'] = 'Cae Precio Comunidad';
        //  DPD
            $datosParseados[0]['col13'] = 'ID DPD Comunidad';
            $datosParseados[0]['col14'] = 'DPD Contratado';
            $datosParseados[0]['col15'] = 'DPD Precio';        
            $datosParseados[0]['col16'] = 'DPD Precio Comunidad';        
        //  Certificados digitales

            $datosParseados[0]['col17'] = 'ID Certificados digitales Comunidad';
            $datosParseados[0]['col18'] = 'Certificados digitales Contratado';
            $datosParseados[0]['col19'] = 'Certificados digitales Precio';                  
            $datosParseados[0]['col20'] = 'Certificados digitales Precio Comunidad'; 
             $i = 1;
            foreach($datosComunidad as $comunidad)
            {
                    $datoComunidad = [];
                    $datoComunidad['col'] = (string)$comunidad['idcomunidad'];
                    $datoComunidad['col1'] = (string)$comunidad['codigo'];
                    $datoComunidad['col2'] = (string)$comunidad['comunidad'];
                    $datoComunidad['col3'] = (string)$comunidad['direccion'];
                    $datoComunidad['col4'] = (string)$comunidad['localidad'];
                    $datoComunidad['col5'] = is_null($comunidad['provincia']) ? (string)'N/D' : (string)$comunidad['provincia'];
                    $datoComunidad['col6'] = (string)$comunidad['cif'];
                    $datoComunidad['col7'] = (string)$comunidad['ibancomunidad'];
                    $datoComunidad['col8'] = (string)$comunidad['mesfacturacion'];
                //  CAE
                    $datoComunidad['col9'] = (string)$comunidad['cae_idservicio'];
                    $datoComunidad['col10'] = (string)$comunidad['cae_servicio_contratado'];
                    $datoComunidad['col11'] = (string)$comunidad['cae_precio'];
                    $datoComunidad['col12'] = (string)$comunidad['cae_preciocomunidad'];
                //  DPD
                    $datoComunidad['col13'] = (string)$comunidad['dpd_idservicio'];
                    $datoComunidad['col14'] = (string)$comunidad['dpd_servicio_contratado'];
                    $datoComunidad['col15'] = (string)$comunidad['dpd_precio'];
                    $datoComunidad['col16'] = (string)$comunidad['dpd_preciocomunidad'];
                //  Certificados digitales
                    $datoComunidad['col17'] = (string)$comunidad['certificados_digitales_idservicio'];
                    $datoComunidad['col18'] = (string)$comunidad['certificados_digitales_servicio_contratado'];
                    $datoComunidad['col19'] = (string)$comunidad['certificados_digitales_precio'];
                    $datoComunidad['col20'] = (string)$comunidad['certificados_digitales_preciocomunidad'];
                    $datosParseados[$i] = $datoComunidad;
                    $i++;
            }

        return $datosParseados;
    }

}