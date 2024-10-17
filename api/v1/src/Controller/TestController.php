<?php

namespace Fincatech\Controller;

use phpseclib3\Net\SFTP;

class TestController extends FrontController{


    public function __construct($params = null)
    {
    }

    public function Test()
    {
        if (extension_loaded('ssh2')) {
            echo "La extensión SSH2 está habilitada.";
        } else {
            echo "La extensión SSH2 NO está habilitada.";
        };
        die();
        // $path = ROOTDIR;
        // $resultado = $this->FTPSendFile(ROOTDIR . 'test.txt',  '/zip/'. 'test.txt');
        // if($resultado === true)
        // {
        //     return 'subido';
        // }else{
        //     return 'Error: ' . $resultado;
        // }
        // Datos de conexión
        $ftp_server = "home400351608.1and1-data.host"; // Reemplaza con tu servidor FTP
        $sftpPort = 22;
        $ftp_user = "acc652662948"; // Reemplaza con tu usuario FTP
        $ftp_pass = "e)EJ;2O!p94S"; // Reemplaza con tu contraseña FTP

        try {
            $dataFile = ROOTDIR . 'test.txt';
            $sftpServer = "home400351608.1and1-data.host";
            $sftpUsername = "acc652662948";
            $sftpPassword ="e)EJ;2O!p94S";
            $sftpPort = "22";
            $sftpRemoteDir = '/zip';
            // $ch = curl_init('sftp://' . $sftpServer . ':' . $sftpPort . $sftpRemoteDir . '/' . basename($dataFile));
            $ch = curl_init('sftp://' . $sftpServer . $sftpRemoteDir . '/');

// die('sftp://' . $sftpServer . ':' . $sftpPort . $sftpRemoteDir . '/' . basename($dataFile));

            $fh = fopen($dataFile, 'r');
           

            if ($fh) {
                curl_setopt($ch, CURLOPT_USERPWD, $sftpUsername . ':' . $sftpPassword);
                curl_setopt($ch, CURLOPT_UPLOAD, true);
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
                curl_setopt($ch, CURLOPT_INFILE, $fh);
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dataFile));
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $verbose = fopen('php://temp', 'w+');
                curl_setopt($ch, CURLOPT_STDERR, $verbose);
                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);
                if ($response){
                    echo "Success";
                } else {
                    $msg = "Failure<br>" ;
                    rewind($verbose);
                    $verboseLog = stream_get_contents($verbose);
                    $msg .= "Verbose information:<br>" . $verboseLog . "<br>";
                    return $msg;
                }
            }else{
                echo 'No se ha podido abrir el fichero';
            }

        }catch (\Exception $e) {
            echo "error exception".$e->getMessage();
        }
            die('llega');

    }

}