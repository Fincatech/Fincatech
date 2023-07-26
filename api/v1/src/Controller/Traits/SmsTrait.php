<?php

nameSpace HappySoftware\Controller\Traits;

use CURLFile;

trait SmsTrait{

    public function SendSMSContractCertified($phone_number, $smsText, $filePublicName, $nombre_administrador = null, $cifAdministrador = null, $telefonoAdministrador = null)
    {

        global $appSettings;

        $account = 'info@fincatech.es';
        $password = '1758264';

        //  Nombre del administrador. Por defecto Fincatech
        //$nombre_administrador = is_null($nombre_administrador) ? 'Fincatech' : $nombre_administrador;
        $nombre_administrador = 'Fincatech';

        //  CIF del administrador. Por defecto Fincatech
       // $cifAdministrador = is_null($cifAdministrador) ? 'B-93594992' : $cifAdministrador;
        $cifAdministrador = 'B93594992';

        //  Teléfono del administrador. Por defecto Fincatech
        $telefonoAdministrador = is_null($cifAdministrador) ? '952792038' : $telefonoAdministrador;

        $telefonoAdministrador = str_replace('+','',$telefonoAdministrador);
        $telefonoAdministrador = str_replace(' ','',$telefonoAdministrador);
        $telefonoAdministrador = '952792038';

        /*
            --- TipoContrato -> 18. 
            Se debe adjuntar un PDF que será el contrato a firmar. La forma de adjuntar el PDF es añadir (FILE:2:ficherocontrato.pdf) en cualquier parte del texto. La forma de adjuntar archivos, en todo caso, está explicada en los anexos. En este modo de contratación, el destinatario deberá acceder a la visualización del PDF, hacer scroll hasta la última página del fichero y pulsar en el botón ‘aceptar’ que le aparecerá para poder cerrar el contrato.

        */
        $curlFileName = ROOT_DIR.$appSettings['storage']['path'] . $filePublicName;
        $curlFile = new CURLFile(realpath($curlFileName), 'application/pdf', $filePublicName);

        //  Tenemos que inyectar la ruta del fichero PDF que se va a firmar
        $smsText .= ' (FILE:2:' . $filePublicName.')';

        $postFields = array(
            'Correo' => $account,
            'Passwd' => $password,
            'Remitente' => 'Fincatech',
            'Destinatarios' => $phone_number,
            'Mensaje' => $smsText,
            'Contacto' => $nombre_administrador,
            'TelContacto' => $telefonoAdministrador,
            'CIFContacto' => $cifAdministrador,
            'Report' => 1,
            'Referencia' => $filePublicName,
            'file' => $curlFile,
            'TipoContrato' => '18',
            'Links' => '1',
            'Resp' => 'JSON');
        // $postFields = 'Correo='.$account.'&'.
        //             'Passwd='.$password.'&Remitente=Fincatech&'.
        //             'Destinatarios='.$phone_number.'&'.
        //             'Mensaje='.$smsText.'&'.
        //             'Contacto='.$nombre_administrador.'&'.
        //             'TelContacto='.$telefonoAdministrador.'&'.
        //             'CIFContacto='.$cifAdministrador.'&Report=1&'.
        //             'Referencia='.$filePublicName.'&'.
        //             'File=' . $curlFile .'&'.
        //             'TipoContrato=18&Links=1&Resp=JSON';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.mensatek.com/v6/EnviarSMSCERTIFICADO");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)"); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields );
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);          
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $sub=curl_exec ($ch);
        curl_close ($ch);
        $respuesta = json_decode($sub, true);
        $this->WriteToLog('sms','SendSMSContractCertified',json_encode($postFields));
        $this->WriteToLog('sms','SendSMSContractCertified - JSON Recibido',$sub);
        return true;
    }

    public function SendSMSCertificated($phone_number, $smsText, $nombre_administrador = null, $cifAdministrador = null, $telefonoAdministrador = null)
    {
        $account = 'info@fincatech.es';
        $password = '1758264';

        //  Nombre del administrador. Por defecto Fincatech
        //$nombre_administrador = is_null($nombre_administrador) ? 'Fincatech' : $nombre_administrador;
        $nombre_administrador = 'Fincatech';

        //  CIF del administrador. Por defecto Fincatech
       // $cifAdministrador = is_null($cifAdministrador) ? 'B-93594992' : $cifAdministrador;
        $cifAdministrador = 'B93594992';

        //  Teléfono del administrador. Por defecto Fincatech
        $telefonoAdministrador = is_null($cifAdministrador) ? '952792038' : $telefonoAdministrador;

        $telefonoAdministrador = str_replace('+','',$telefonoAdministrador);
        $telefonoAdministrador = str_replace(' ','',$telefonoAdministrador);
        $telefonoAdministrador = '952792038';

        $postFields = 'Correo='.$account.'&'.
                    'Passwd='.$password.'&'.
                    'Remitente=Fincatech&'.
                    'Destinatarios='.$phone_number.'&'.
                    'Mensaje='.$smsText.'&'.
                    'Contacto='.$nombre_administrador.'&'.
                    'TelContacto='.$telefonoAdministrador.'&'.
                    'CIFContacto='.$cifAdministrador.'&Resp=JSON';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.mensatek.com/v6/EnviarSMSCERTIFICADO");
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields );
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $sub=curl_exec ($ch);
        curl_close ($ch);
        $this->WriteToLog('sms','SendSMSCertificated',$postFields);
        return true;
    }

}