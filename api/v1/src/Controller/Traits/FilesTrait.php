<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de ficheros del sistema
*
*
**/
namespace HappySoftware\Controller\Traits;
use HappySoftware\Database\DatabaseCore;

trait FilesTrait{

    /** TODO: Recupera un fichero del histórico y lo asociad a la entidad deseada si existe */
    public function moveFileFromHistoricoToStorage($id, $entidad, $iddestino)
    {
        //  Se asigna el idfichero a la entidad sobre la que se desea reestablecer
        //  el fichero original

    }

    /** TODO: Mueve un fichero al histórico y que previamente exista */
    public function moveFileFromStorageToHistorico($id, $entidad)
    {
        //  Comprobamos primero que exista el fichero físico en la ruta del almacén

        //  Comprobamos que no exista ese fichero en la tabla de histórico

        //  Pasamos el fichero a la tabla de histórico indicando la entidad a la que pertenece
        //  y la fecha en la que se ha generado el registro

    }

    public function uploadFile($nombre, $base64string)
    {
        global $appSettings;

        $result = [];

        $ruta = ROOT_DIR . $appSettings['storage']['path'];

    //  TODO: Hay que pasar ficheros al histórico

        //  Generamos un nombre de fichero aleatorio basado en md5
            $fichero = md5(time());
            $extension = explode('.', $nombre);

            if(is_array($extension))
                $extension = strtolower($extension[count($extension)-1]);

            $fichero = $fichero.'.'.$extension;

        //  Si no existe el directorio del almacén, lo generamos
            if(!file_exists($ruta))
                mkdir($ruta, 0755);

            try{

                    file_put_contents($ruta . $fichero , base64_decode(explode(',',$base64string)[1]));

                //  Insertamos el registro en base de datos
                    $sqlFile = "insert into ficheroscomunes(nombre, nombrestorage, ubicacion, estado, created, usercreate) values (";
                    $sqlFile .= "'$nombre', '$fichero', '" . $ruta . "', null, now(), " . $this->getLoggedUserId() . ")";                     
                    $this->getRepositorio()->queryRaw( $sqlFile );
                    return ($this->getRepositorio()->getLastID("ficheroscomunes") - 1);

            }catch(Exception $ex){
                print_r($ex);
                die();
            }
    }

    /** Obtiene el fichero pasado por post y lo procesa devolviendo el id de la inserción
    *   en caso contrario, devuelve null
    */
    public function getFileInfoFromPostData($datos)
    {
        /*
            core.Files.Fichero.entidad = null;
            core.Files.Fichero.entidadId = null;
            core.Files.Fichero.nombre = null;
            core.Files.Fichero.base64 = null;
        */
        // print_r($datos['fichero']);
        $result = null;
        $file = $datos;

        if(isset($file['fichero']))
        {
            if(isset($file['fichero']['base64']))
            {
                if($file['fichero']['base64'] != '')
                    $result = $this->uploadFile($file['fichero']['nombre'], $file['fichero']['base64']);            
            }

        }

        return $result;

    }


}