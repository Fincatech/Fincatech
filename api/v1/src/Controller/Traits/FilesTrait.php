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

    public function WriteToFile($path, $fileName, $content)
    {



    } 

    /**
     * Elimina un fichero del sistema y de la bbdd
     * @param int $fileId ID del fichero
     */
    public function DeleteFile($fileId)
    {
        //  Recuperamos la información del fichero
        $fichero = $this->GetFileInfoById($fileId);
        if(count($fichero) > 0)
        {
            global $appSettings;
            $ruta = ROOT_DIR . $appSettings['storage']['path'];
            //  Nombre fichero 
            $nombreFichero = $fichero[''];
            //  Eliminamos en bbdd el registro
            $sql = "delete from ficheroscomunes where id = $fileId";
            $this->getRepositorio()->queryRaw( $sql );
            //  Eliminamos el fichero físicamente 
            unlink($ruta . $nombreFichero);
        }
    }

    public function GetFileInfoById($fileId)
    {
        $sqlFile = "select * from ficheroscomunes where id = $fileId";
        $result = $this->getRepositorio()->queryRaw( $sqlFile );
        $result = mysqli_fetch_assoc($result);
        return $result;
    }

    /**
     * Sube un fichero al hosting y le asigna un ID
     */
    public function uploadFile($nombre, $base64string, $privateStorage = false)
    {
        global $appSettings;

        $result = [];

        $ruta = ROOT_DIR;

        if($privateStorage){
            $ruta .= $appSettings['storage']['private'];
        }else{
            $ruta .= $appSettings['storage']['path'];
        }

        $ruta .=  '/';

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
                    $nombre = str_replace("'","`", $nombre);
                //  Insertamos el registro en base de datos
                    $sqlFile = "insert into ficheroscomunes(nombre, nombrestorage, ubicacion, estado, created, usercreate) values (";
                    $sqlFile .= "'$nombre', '$fichero', '" . $ruta . "', null, now(), " . $this->getLoggedUserId() . ")";                     
                    $this->getRepositorio()->queryRaw( $sqlFile );
                    return ($this->getRepositorio()->getLastID("ficheroscomunes") - 1);

            }catch(\Exception $ex){
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

    /**
     * Guarda un fichero ZIP en la carpeta de certificados de Mensatek
     */
    public function saveZipFile($fileContent)
    {

        global $appSettings;

        $ruta = ROOT_DIR . $appSettings['storage']['certificados'];
        
        if(!file_exists($ruta))
            mkdir($ruta);

        $fileName = time() . '.zip';
        $fhFichero = fopen($ruta . '/'. $fileName ,"w");
        fwrite($fhFichero, $fileContent);
        fclose($fhFichero);

        return $fileName;

    }

    /**
     * Comprueba las carpetas de almacenamiento de ficheros
     */
    public function checkFolders()
    {
        global $appSettings;

        $ruta = ROOT_DIR . $appSettings['storage']['certificados'];
        
        if(!file_exists($ruta))
            mkdir($ruta);   

        $ruta = ROOT_DIR . $appSettings['storage']['log'];
        
        if(!file_exists($ruta))
            mkdir($ruta);   

    }

    /** Devuelve un fichero existente en formato base64 */
    public function GetFileBase64Formated($path)
    {

        // Lee el contenido del archivo en una variable
        $contenido = file_get_contents($path);

        // Codifica el contenido en base64
        return base64_encode($contenido);
    }

}