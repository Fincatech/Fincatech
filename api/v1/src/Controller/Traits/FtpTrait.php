<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de servicios FTP
*
*
**/
namespace HappySoftware\Controller\Traits;
use HappySoftware\Database\DatabaseCore;
use ZipArchive;

trait FtpTrait{

    public $ftpURL;
    public $ftpUser = 'home400351608.1and1-data.host';
    public $ftpPassword = 'e)EJ;2O!p94S';
    public $ftpPort = 22;
    public $ftpInitialDir = '';
    public $sFTP = false;

    private $ftpConnID;
    private $ftpSFTP;
    private $ftpConectado = false;

    private $sFTPHandler;
    private $ftpsError = ''; // Mensaje de error

    public function FTPError()
    {
        return $this->ftpsError;
    }

    /**
     * Inicializa la conexión con el servidor
     */
    private function InitFTPConnection()
    {
        global $appSettings;

        try{

            if($this->ftpConectado){
                return;
            }

            $ftpData            = $appSettings['ftp_servers']['facturacion'];

            //  Inicializamos el posible mensaje de error
            $this->ftpsError = '';
            //  Establecemos los datos del servidor que se va a utilizar
            $this->ftpURL       = $ftpData['url'];
            $this->ftpPort      =  $ftpData['port'];
            $this->ftpUser      =  $ftpData['user'];
            $this->ftpPassword  =  $ftpData['password'];

            //  Comprueba si está configurado para usar SSL o conexión no segura
            if($ftpData['use_ssl']){
                // $this->ftpConnID = ftp_ssl_connect($this->ftpURL, 22, 10);
                $this->ftpConnID = ssh2_connect($this->ftpURL, 22);
                $this->sFTP = true;
            }else{
                $this->ftpConnID = ftp_connect($this->ftpURL, $this->ftpPort);
                $this->sFTP = false;
            }

            if($this->ftpConnID === false){
                $this->ftpConectado = false;
                $this->ftpsError = 'No se ha podido conectar con el servidor';
                return;
            }

            if($ftpData['use_ssl'] == false){
                $logged = @ftp_login($this->ftpConnID, $this->ftpUser, $this->ftpPassword);
            }else{
                $logged = ssh2_auth_password($this->ftpConnID, $this->ftpUser, $this->ftpPassword);
            }


            //  Intentamos realizar el login con los datos correspondientes
            if(!$this->ftpConnID || !$logged){
                $this->ftpConectado = false;
                $this->ftpsError = 'Error en conexión al servidor FTP';
            }else{
                $this->ftpConectado = true;
            }
        }catch(\Throwable $ex){
            $error = $ex->getMessage();
            $a = 0;
        }
    }

    public function FTPCheckDir($path)
    {
        // Verificar si el directorio remoto existe
        if (!ftp_chdir($this->ftpConnID, $path)) {
            // El directorio no existe, intentamos crearlo
            if (!$this->ftp_mkdir_recursive($path)) {
                $this->ftpsError = 'El directorio de destino no ha podido ser creado';
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * Envía un fichero al servidor FTP
     * @param string Path + fichero de origen
     * @param string Path + fichero de destino
     * @return bool Resultado del envío
     */
    public function FTPSendFile($sourceFile, $destination)
    {
        try{
            $result = false;
            //  Abrimos la conexión al servidor
            $this->InitFTPConnection();
            //  Validamos si está conectado
            if(!$this->ftpConectado){
                return $this->ftpsError;
            }

            //  Comprobamos si existe el directorio de destino, para si no, crearlo
            if($this->sFTP == false)
            {
                $dir = dirname($destination);
                if(!$this->FTPCheckDir($dir)){
                    return $this->ftpsError;
                }    
            }

            //  Comprobamos si existe el fichero de origen
            if(!file_exists($sourceFile)){
                $this->ftpsError = 'No existe el fichero';
                return $this->ftpsError;
            }

            if($this->sFTP == true)
            {
                $this->sFTPHandler = ssh2_sftp($this->ftpConnID);
                $result = $this->sFTPSendFile($sourceFile, $destination);
            }else{

                // Subir el archivo
                if (ftp_put($this->ftpConnID, $destination, $sourceFile, FTP_BINARY)) {
                    $result = true;
                }else{
                    $this->ftpsError = 'El fichero no ha podido subirse al servidor';
                    $result = false;
                }

            }

            //  Cierre de conexión FTP
            if($this->ftpConectado && $this->sFTP == false){
                ftp_close($this->ftpConnID);
                $this->ftpConectado = false;
            }

            //  Cierre de conexión SFTP
            if($this->ftpConectado && $this->sFTP == true){
                // ssh2_disconnect($this->ftpConnID);
                // $this->ftpConectado = false;
            }

        }catch(\Throwable $ex){
            $this->ftpsError = $ex->getMessage();
            $result = $this->ftpsError;
        }
        
        return $result;
    }


    /**
     * Valida y crea si no existe un directorio en el servidor remoto
     */
    private function CheckAndCreateDir($dirName)
    {
        // $dir = dirname($dirName);
        // $sftp = $this->sFTPHandler;
        // $sftpDir = @opendir("ssh2.sftp://$sftp$dir");
        // if($sftpDir === false){
        //     $dirCreation = ssh2_sftp_mkdir($sftp, $dir, 0777, true);
        // }
    }

    private function sFTPSendFile($sourceFile, $destination)
    {
        $result = false;
        $stream = file_get_contents($sourceFile);
        if(!$stream){
            $this->ftpsError = 'El fichero no existe';
        }else{
            //  Forzamos la creación del directorio si no existe previamente
            $this->CheckAndCreateDir($destination);
            if(file_put_contents('ssh2.sftp://'.$this->sFTPHandler.$destination, $stream) !== false){
                $result = true;
            };
        }
        return $result;
    }

    /**
     * Función para crear directorios de forma recursiva en FTP
     */
    public function ftp_mkdir_recursive($remote_dir) {
        $dirs = explode('/', $remote_dir);
        $dir = '';
        
        foreach ($dirs as $part) {
            if (empty($part)) continue;
            $dir .= '/' . $part;
            if (!@ftp_chdir($this->ftpConnID, $dir)) {
                // El directorio no existe, intentamos crearlo
                if (!ftp_mkdir($this->ftpConnID, $dir)) {
                    return false;
                }
            }
        }
        
        return ftp_chdir($this->ftpConnID, $remote_dir);
    }


}