<?php

nameSpace HappySoftware\Controller\Traits;

use CURLFile;

trait SmsTrait{

    /**
     * Sube un fichero a la librería de Mensatek para poder enviar documentos
     */
    public function UploadFileToMensatekLibrary($fileName, $fileBase64)
    {
        //  El nombre del fichero temporal ha de ser un timestamp para evitar que coincida con otro
        $tempFileName = time() . '.pdf';

        //  Creamos el fichero de manera temporal en el servidor para enviarlo

        //  Enviamos la petición al WS
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mensatek.com/v7/CargarFichero',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
            'BASE64' => $fileBase64, 
            'Autoborrado=Si'
            ),
            CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));

        $response = curl_exec($curl);
        
        $resultadoEnvio = $this->procesarRespuestaLibreriaMensatek($response);
        $this->WriteToLog('sms','UpoloadFileToMensatekLibrary','Fichero: ' . $fileName);

        curl_close($curl);
        return $resultadoEnvio;

      
    }

    public function SendSMSContractCertified($phone_number, $smsText, $fileName, $fileBase64, $nombre_administrador = null, $cifAdministrador = null, $telefonoAdministrador = null)
    {

        global $appSettings;

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
        // $curlFileName = ROOT_DIR.$appSettings['storage']['path'] . $filePublicName;
        // $curlFile = new CURLFile(realpath($curlFileName), 'application/pdf', $filePublicName);
        $destinatariosSMS = [['Movil' => $phone_number, 'Variable_1' => 'Fincatech']];
        //  Enviamos el fichero a la biblioteca de Mensatek
        $mensatekFileId = $this->SendFileToMensatekLibrary($fileName, $fileBase64);
        //  Comprobamos que se haya subido correctamente al WS
        if($mensatekFileId === false)
        {
            //  Devolvemos false
            return false;
        }
        //  Tenemos que inyectar la ruta del fichero PDF que se va a firmar
        $smsText .= ' (BASE64:2:' . $fileBase64.')';
        // $smsText .= ' (FILE:2:' . $filePublicName.')';
        
        $cuerpoPeticion = array(
            'Remitente' => 'Fincatech',
            'Destinatarios' => json_encode($destinatariosSMS),
            'Mensaje' => $smsText,
            'Unicode' => 1,
            'Report' => 1,
            'Contacto' => $nombre_administrador,
            'TelContacto' => $telefonoAdministrador,
            'Cifcontacto' => $cifAdministrador,
            'TipoContrato' => '18',
            'Resp' => 'JSON');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.mensatek.com/v7/EnviarSMSCERTIFICADO");
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://api.mensatek.com/v7/EnviarSMSCERTIFICADO',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $cuerpoPeticion,
            CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));

        $response = curl_exec ($ch);

        //  Tenemos que recuperar el ID del mensaje para registrarlo en el sistema
        $resultadoEnvio = $this->procesarRespuestaSMSCertificado($response, $phone_number, $smsText, $nombre_administrador, $telefonoAdministrador,  $cifAdministrador);

        curl_close ($ch);
        $this->WriteToLog('sms','SendSMSCertificated',json_encode($cuerpoPeticion));
        return $resultadoEnvio;
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

        $destinatariosSMS = [['Movil' => $phone_number, 'Variable_1' => 'Fincatech']];
        $cuerpoPeticion = array(
            'Remitente' => 'Fincatech',
            'Destinatarios' => json_encode($destinatariosSMS),
            'Mensaje' => $smsText,
            'Unicode' => 1,
            'Report' => 1,
            'Contacto' => $nombre_administrador,
            'TelContacto' => $telefonoAdministrador,
            'Cifcontacto' => $cifAdministrador,
            'Resp' => 'JSON'
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://api.mensatek.com/v7/EnviarSMSCERTIFICADO',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $cuerpoPeticion,
            CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));  

        $response = curl_exec ($ch);

        //  Tenemos que recuperar el ID del mensaje para registrarlo en el sistema
        $resultadoEnvio = $this->procesarRespuestaSMSCertificado($response, $phone_number, $smsText, $nombre_administrador, $telefonoAdministrador,  $cifAdministrador);

        curl_close ($ch);
        $this->WriteToLog('sms','SendSMSCertificated',json_encode($cuerpoPeticion));
        return $resultadoEnvio;
    }

    /**
     * Procesa la respuesta recibida desde el servicio de Mensatek tras enviar un e-mail certificado
     */
    private function procesarRespuestaSMSCertificado($respuestaXML, $phone_number = null, $smsText = null, $nombre_administrador = null, $telefonoAdministrador = null,  $cifAdministrador = null)
    {

        //  Ha ocurrido algún tipo de error así que salimos por seguridad pero habrá que determinar
        if($respuestaXML == '-1' || strpos($respuestaXML, 'xml') < 0)
        {
            $this->WriteToLog('smscertificado', 'SMS Certificado -> procesarRespuestaSMSCertificado', $respuestaXML . ' - Error de autenticación');            
            return false;
        }

        $arrOutput = json_decode($respuestaXML, TRUE);
        
        //  Logueamos la respuesta de mensatek
        $this->WriteToLog('smscertificado', 'SMS Certificado -> procesarRespuestaSMSCertificado', $respuestaXML);

        //  Si la respuesta recibida no es un array indica directamente un error
        if(!is_array($arrOutput))
            return false;

        //  Si la respuesta ha sido satisfactoria, procesamos
        if(intval($arrOutput['Res']) >= 1)
        {

            $this->InitController('Mensaje');
            $mensatekId = $arrOutput['Msgid'];

            //  Guardamos el ID del mensaje enviado proporcionado por el proveedor Mensatek
            $this->MensajeController->SaveCertificado($mensatekId, -1, -1);            
            return $mensatekId;
        }else{
            return false;
        }
        return true;
    }

    /**
     * Procesa la respuesta recibida desde el servicio de Mensatek tras enviar un e-mail certificado
     */
    private function procesarRespuestaLibreriaMensatek($respuestaXML)
    {

        //  Ha ocurrido algún tipo de error así que salimos por seguridad pero habrá que determinar
        if($respuestaXML == '-1' || strpos($respuestaXML, 'xml') < 0)
        {
            $this->WriteToLog('mensatek', 'SMS Certificado -> procesarRespuestaLibreriaMensatek', $respuestaXML . ' - Error de autenticación');            
            return false;
        }

        $arrOutput = json_decode($respuestaXML, TRUE);
        
        //  Logueamos la respuesta de mensatek
        $this->WriteToLog('mensatek', 'SMS Certificado -> procesarRespuestaLibreriaMensatek', $respuestaXML);

        //  Si la respuesta recibida no es un array indica directamente un error
        if(!is_array($arrOutput))
            return false;

        //  Si la respuesta ha sido satisfactoria, procesamos
        if(intval($arrOutput['Res']) >= 1)
        {

            $this->InitController('Mensaje');
            $mensatekId = $arrOutput['Msgid'];

            //  Guardamos el ID del mensaje enviado proporcionado por el proveedor Mensatek
            $this->MensajeController->SaveCertificado($mensatekId, -1, -1);            
            return $mensatekId;
        }else{
            return false;
        }
        return true;
    }

}