<?php

namespace Fincatech\Controller;

use Fincatech\Model\UsuarioModel;
use Fincatech\Controller\UsuarioController;
use HappySoftware\Controller\HelperController;
use Happysoftware\Controller\Traits;
use PHPUnit\TextUI\Help;

class AdministradorController extends FrontController{

    private $usuarioModel;
    public $AdministradorModel;
    public $ComunidadController;
    public $UsuarioController;

    public function __construct($params = null)
    {
        parent::__construct();
        $this->InitModel('Administrador', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        //  Tenemos que generar un salt aleatorio y una pass por defecto
        
        //$datos['salt'] = md5(time());
        $datos['salt'] = '';

        //  Implementarlo en el formulario de administrador
        if( $datos['password'] === '')
        {
            $datos['password'] = md5('123456');
        }else{
            $datos['password'] = md5( $datos['password'] );
        }

        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        //$datos['email'] = $datos['emailcontacto'];

        //  Inicializamos el controller de usuario para poder darlo de alta
        $this->InitController('Usuario', $datos);

        //  Creamos el usuario
        $result = $this->UsuarioController->Create($entidadPrincipal, $datos);
        return $result;
        //return $this->AdministradorModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        if( trim($datos['password']) === '')
        {
            unset($datos['password']);
        }else{
            $datos['password'] = md5( $datos['password'] );
        }        
        $this->InitController('Usuario', $datos);
        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        // $datos['email'] = $datos['emailcontacto'];
        $result = $this->UsuarioController->Update($entidadPrincipal, $datos, $usuarioId);
        return $result;
        // return $this->AdministradorModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->AdministradorModel->getSchema();
    }

    public function Delete($id)
    {
        $this->InitController('Usuario');
        return $this->UsuarioController->Delete($id);
        // return $this->AdministradorModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->AdministradorModel->Get($id);
    }

    /** Devuelve el listado de administradores */
    public function List($params = null)
    {
        $this->InitController('Usuario', $params);
        $this->InitModel('Usuario', $params);
        $datos = $this->UsuarioController->ListAdministradoresFincas($params);
        //  Por cada uno de los administradores recuperamos el total de comunidades que tiene activas
        for($x = 0; $x < count($datos['Usuario']); $x++)
        {
            $datos['Usuario'][$x]['numerocomunidades'] = $this->AdministradorModel->GetNumeroComunidades($datos['Usuario'][$x]['id']);
        }
        return $datos;
    }

    public function GetExcelPrefacturacion($idAdministrador, $data)
    {
            global $appSettings;

        //  Instanciamos el controller de comunidades
            $this->InitController('Comunidad', null);

        //  Introducimos el valor del id de administrador para acotar la búsqueda
            $params = [];
            $params['administradorId'] = $idAdministrador;
            $params['servicios'] = true;
            $params['fechaDesde'] = $data['fechaDesde'];
            $params['fechaHasta'] = $data['fechaHasta'];

            //$comunidadesAdministrador = $this->ComunidadController->ComunidadModel->ListComunidadesByAdministradorId($idAdministrador);
            $comunidadesAdministrador = $this->ComunidadController->List($params);
            if(count($comunidadesAdministrador['Comunidad']) > 0)
            {
                $listadoComunidades = [];
                $listadoComunidades = $comunidadesAdministrador['Comunidad'];
                if(count($listadoComunidades) > 0)
                {
                    usort($listadoComunidades, fn($a, $b) => $a['codigo'] <=> $b['codigo']);//sort($listadoComunidades)
                }
                $comunidadesAdministrador['Comunidad'] = $listadoComunidades;
                $nombreFichero = $appSettings['storage']['path'].'Fincatech_prefacturacion_'.str_replace(' ','_', $data['nombreAdministrador']) . '_' . date('d-M-Y') . '.xlsx';
                $datosComunidades = $this->AdministradorModel->ProcessArrayDataToExcel($comunidadesAdministrador['Comunidad']);

                if(file_exists(ROOT_DIR . $nombreFichero))
                {
                    unlink(ROOT_DIR . $nombreFichero);
                }

                \HappySoftware\Controller\Traits\ExcelGen::fromArray($datosComunidades, 'pre-facturacion')->saveAs(ROOT_DIR . $nombreFichero);

                $path = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
                return HelperController::successResponse($path.$nombreFichero);

            }else{

                return HelperController::errorResponse('error','El administrador seleccionado no tiene comunidades o bien las tiene en estado de baja o histórico.', 200);
            }

    }

    /**
     * Genera un listado en Excel de aquellos administradores que tienen contratado el servicio DPD para alguna de sus comunidades
     */
    public function ExcelExportAdministradoresDPD()
    {
        $administradoresDPD = $this->AdministradorModel->AdministradoresDPD();
        if(count($administradoresDPD) > 0)
        {

            //  Inyectamos los nombres de las columnas
            $administradoresExcel = [];
            $administradoresExcel[] = Array('RAZÓN SOCIAL', 'CIF/NIF','DIRECCIÓN','LOCALIDAD', 'PROVINCIA','TELÉFONO','EMAIL');
            $administradoresExcel = array_merge($administradoresExcel, $administradoresDPD);

            global $appSettings;

            $nombreFichero = $appSettings['storage']['path'].'Fincatech_Administradores_DPD_'. date('d-M-Y') . '.xlsx';

            if(file_exists(ROOT_DIR . $nombreFichero))
            {
                unlink(ROOT_DIR . $nombreFichero);
            }

            \HappySoftware\Controller\Traits\ExcelGen::fromArray($administradoresExcel, 'administradores')->saveAs(ROOT_DIR . $nombreFichero);

            //  Abrimos el fichero y lo codificamos en base64 para enviar de vuelta
            $contenido = file_get_contents(ROOT_DIR . $nombreFichero);

            // Codificar en base64
            $base64 = base64_encode($contenido);

            //  Eliminamos el fichero del servidor ya que no se va a generar ningún enlace de descarga ni a dejarlo físicamente
            unlink(ROOT_DIR . $nombreFichero);

            //  Devolvemos el resultado en formato base64
            return HelperController::successResponse($data[] = $base64, 200);

        }else{
            return HelperController::errorResponse('error','No hay administradores con el servicio DPD contratado', 200);
        }
    }

    public function SaveDocumentAdministrador($administradorId, $documentoFrontalId, $documentoTraseroId)
    {
        //  Recuperamos el documento asociado
        $documento = $this->AdministradorModel->DocumentoIdentificativo($administradorId);
        if(count($documento) == 0)
        {
            //  Si no lo tiene subido lanzamos una inserción
            $this->AdministradorModel->InsertDocumentoIdentificativo($administradorId, $documentoFrontalId, $documentoTraseroId);
        }else{
            //  Eliminamos físicamente los archivos del almacén
            $this->DeleteFile($documento[0]['frontid']);
            $this->DeleteFile($documento[0]['rearid']);
            //  Eliminamos los ficheros de bbdd
            $this->AdministradorModel->UpdateDocumentoIdentificativo($administradorId, $documentoFrontalId, $documentoTraseroId);
        }

    }

    public function getRepositorio()
    {
        $result = parent::GetHelperModel()->getRepositorio();
        return $result;
    }

}