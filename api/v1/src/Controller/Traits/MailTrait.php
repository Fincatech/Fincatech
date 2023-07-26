<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de emails
*
*
**/
nameSpace HappySoftware\Controller\Traits;
use HappySoftware\Database\DatabaseCore;

trait MailTrait{


    private $emailCertificado = 'certificar@emailcert.mensatek.com';

    private $mensatek_api_user = '763297793EB0Z1644447600000000000';  // 763297793EB0Z1644447600000000000
    private $mensatek_api_token = '1AA94CAAF8C06EB56347C34C0F233C81'; // 1AA94CAAF8C06EB56347C34C0F233C81
    private $mensatek_api_url = 'https://api.mensatek.com/v7/';
    private $mensatek_api_endpoint_enviarmailcertificado = 'EnviarEMAILCERTIFICADO';
    private $mensatek_api_endpoint_recuperarpdf = 'GetCertificadoEMAILCERTIFICADO';
    private $mensatek_remitente = 'info@fincatech.es';

    public function SendPlainEmail($to, $name, $subject, $body)
    {

    }

    /**
     * Incluye un e-mail en la blacklist de e-mails no válidos
     * @param string $email Dirección de correo electrónico que se va a incluir en la blacklist
     */
    public function IncludeEmailIntoBlackList($email)
    {
        $sql = "insert into emailblacklist(email, created) values('" . DatabaseCore::PrepareDBString($email) . "', now())";
        $this->GetHelperModel()->queryRaw($sql);
    }

    /**
     * Devuelve la blacklist de e-mails no válidos
     */
    public function EmailBlackList()
    {
        $sql ="select email from emailblacklist";
        $blackList = $this->GetHelperModel()->query( $sql );
        return $blackList;
    }

    public function GetTemplateEmail($nombreTemplate){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/'.$nombreTemplate.'.html';
        ob_start();
            include($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

    /** Inicializa el api de Mensatek */
    public function InitializeAPIEmailCertificado( $usuarioId )
    {
        $apiUserMensatek = null;
        $apiTokenMensatek = null;

        if(!is_null($usuarioId))
        {
            $this->InitController('Usuario');
            $usuario = $this->UsuarioController->Get($usuarioId);
            if(count($usuario['Usuario']) > 0)
            {
                $apiUserMensatek = $usuario['Usuario'][0]['apiuser'] != '' ? $usuario['Usuario'][0]['apiuser'] : null;
                $apiTokenMensatek = $usuario['Usuario'][0]['apitoken'] != '' ? $usuario['Usuario'][0]['apitoken'] : null;
            }
        }

        if( !is_null($apiUserMensatek) && !is_null($apiTokenMensatek) )
        {
            $this->mensatek_api_user = $apiUserMensatek;
            $this->mensatek_api_token = $apiTokenMensatek;
            $this->mensatek_remitente = $usuario['Usuario'][0]['email'];
        }
    }

    /**
     * Obtiene el PDF del e-mail que se ha enviado y lo almacena en el sistema de Fincatech
     */
    public function getPDFEmailCertificado($Idmensaje)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->mensatek_api_url . $this->mensatek_api_endpoint_recuperarpdf,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',CURLOPT_POSTFIELDS => array(
          'IDMENSAJE' => $Idmensaje, 
        ),
        CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));
        $response = curl_exec($curl);
        // $this->WriteToLog('emailcertificado_', 'MailTrait -> getPDFEmailCertificado', $response);
        curl_close($curl);
        return $response;
    }

    public function SendHTMLEmail($to, $name, $subject, $templateFile, $data)
    {
        $body = '';

        //  Comprobamos que el template exista

        //  Si existe lo recuperamos y cargamos la información

        //  Generado el html ya con datos, enviamos el e-mail
        $this->SendEmail($to, $name, $subject, $body);
    }

    public function SendEmailCertificadoAdjuntos($subject, $destinatarios, $body, $adjuntos = null)
    {

        //  Comprobamos si tiene adjuntos para poder enviarlos en el cuerpo de la petición
            if(!is_null($adjuntos))
            {
                $ficherosAdjuntos = [];
                for($x = 0; $x < count($adjuntos); $x++)
                {
                    if(file_exists($adjuntos[$x]['ubicacion']))
                    {
                        $ficheroContent = file_get_contents($adjuntos[$x]['ubicacion']);

                        $fichero = [];

                        // TODO: Recuperamos la extensión original del fichero para ponerla en el cuerpo
                        $extensionFichero = '.pdf';

                        $extension = explode('.', $adjuntos[$x]['nombre'] );
                        if(is_array($extension) && @count($extension) >= 1)
                        {
                            $extensionFichero = '.'.$extension[count($extension)-1];
                        }

                        $fichero['Nombre'] = 'doc_cae_'.($x+1) . $extensionFichero;//$adjuntos[$x]['nombre'];
                        $fichero['Contenido'] = chunk_split(base64_encode($ficheroContent));
                        $ficherosAdjuntos[] = $fichero;
                    }
                }
            }

        //  Comprobamos si algún e-mail está en la blacklist
            $blackList = $this->EmailBlackList();
            $emailNotSents = array();

        //  Procesamos los destinatarios
            $destinatariosEmail = [];
            for($x = 0; $x < count($destinatarios); $x++)
            {
                $destinatario = [];
                $destinatario['Nombre'] = $destinatarios[$x]['nombre'];
                $destinatario['Email'] = $destinatarios[$x]['email'];
                $destinatariosEmail[] = $destinatario;
            }

        //  Componemos el cuerpo de la petición
            $cuerpoPeticion = array(
                'Remitente' => 'info@fincatech.es',
                'Asunto' => $subject,
                'Mensaje' => $body,
                'Destinatarios' => json_encode($destinatariosEmail),
                'Adjuntos' => json_encode($ficherosAdjuntos),
                'Resp' => 'XML',
                'Report' => 1
            );

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->mensatek_api_url . $this->mensatek_api_endpoint_enviarmailcertificado,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $cuerpoPeticion,
        CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));

        $response = curl_exec($curl);
        // $infoSent = curl_getinfo($curl);

        // $this->WriteToLog('emailcertificado', 'MailTrait -> CURL Sent Header', json_encode($infoSent));
        //$this->WriteToLog('emailcertificado', 'MailTrait -> CURL Body Sent', json_encode($cuerpoPeticion));
        curl_close($curl);

        // print_r($cuerpoPeticion);
        //  Pintamos la respuesta en el log
        $this->WriteToLog('emailcertificado', 'MailTrait -> SendEmailCertificadoAdjuntos', $response);
        return $response;

    }

    /**
     * Envía un e-mail certificado simple desde el panel del administrador de fincas
     */
    public function SendEmailCertificado($subject, $destinatarios, $body, $usuarioId = null, $singleNameDestinatario = null)
    {
        //  Inicializamos el api de Mensatek
        $this->InitializeAPIEmailCertificado($usuarioId);

        //  Procesamos los destinatarios
        if(is_array($destinatarios)){
            $destinatariosEmail = [];
            for($x = 0; $x < count($destinatarios); $x++)
            {
                $destinatario = [];
                $destinatario['Nombre'] = substr($destinatarios[$x],0,20); // Está limitado a 20 caracteres
                $destinatario['Email'] = $destinatarios[$x];
                $destinatariosEmail[] = $destinatario;
            }
        }

        //  Componemos el cuerpo de la petición
            $cuerpoPeticion = array(
                'Remitente' => $this->mensatek_remitente,
                'Asunto' => $subject,
                'Mensaje' => $body,
                'Destinatarios' => json_encode($destinatariosEmail),
                'Resp' => 'XML',
                'Report' => 1
            );

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->mensatek_api_url . $this->mensatek_api_endpoint_enviarmailcertificado,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $cuerpoPeticion,
        CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token)),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        //  Pintamos la respuesta en el log
        $this->WriteToLog('emailcertificado', 'MailTrait -> SendEmailCertificado', $response);
        //  Procesamos la respuesta recibida por Mensatek
        $resultadoEnvio = $this->procesarRespuestaEmailCertificado($response, $subject, $body, $singleNameDestinatario);

        if($resultadoEnvio === true)
        {
            return 'ok';
        }else{
            return 'ko';
        }

    }

    /**
     * Procesa la respuesta recibida desde el servicio de Mensatek tras enviar un e-mail certificado
     */
    private function procesarRespuestaEmailCertificado($respuestaXML, $subject = null, $body = null, $singleNameDestinatario = null)
    {

        //  Ha ocurrido algún tipo de error así que salimos por seguridad pero habrá que determinar
        if($respuestaXML == '-1' || strpos($respuestaXML, 'xml') < 0)
        {
            $this->WriteToLog('emailcertificado', 'Emails Certificados -> procesarRespuestaEmailCertificado', $respuestaXML . ' - Error de autenticación');            
            return 'ko';
        }

        $xml = simplexml_load_string($respuestaXML);
        $objJsonDocument = json_encode($xml);
        $arrOutput = json_decode($objJsonDocument, TRUE);
        
        //  Logueamos la respuesta de mensatek
        $this->WriteToLog('emailcertificado', 'Emails Certificados -> procesarRespuestaEmailCertificado', $respuestaXML);

        libxml_use_internal_errors(true);

        if ($xml === false) {
            // oh no
            $errors = libxml_get_errors();
            // do something with them
            print_r($errors);
            // really you'll want to loop over them and handle them as necessary for your needs
            return 'ko';
        }

        if(!is_array($arrOutput))
        {
            return 'ko';
        }

        //  Si la respuesta ha sido satisfactoria, procesamos
        if(intval($arrOutput['Res']) >= 1)
        {

            $this->InitController('Mensaje');

        //  Hay que recorrer cada destinatario para obtener el ID de mensaje y el fichero en formato PDF para almacenarlo en el sistema
            for($x = 0; $x < count($arrOutput['Destinatarios']); $x++)
            {
                    // $nombre = $arrOutput['Destinatarios']['item'.$x]['NOMBRE'];
                    $email =  $arrOutput['Destinatarios']['item'.$x]['EMAIL'];
                    $idMensaje =  $arrOutput['Destinatarios']['item'.$x]['IDMENSAJE'];
                
                //  Recuperamos el ID de la empresa desde el array de empresas
                    $idEmpresa = -1;
                    $idComunidad = -1; 

                    if(floatval($idMensaje) > 0)
                    {
                        //  Guardamos el ID del mensaje enviado proporcionado por el proveedor Mensatek
                        $this->MensajeController->SaveCertificado($idMensaje, $idComunidad, $idEmpresa);
                        //  Guardamos el mensaje enviado por cada uno de los destinatarios
                        if(!is_null($subject) && !is_null($body))
                        {
                            $this->MensajeController->Insert($email, $subject, $body, $idMensaje, $singleNameDestinatario);
                        }
                    }
            }
        }
        return true;
    }

    /** Envía un e-mail certificado a través de IONOS */
    public function SendEmailCertificadoIONOS($to, $name, $subject, $body, $save = true)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        // $mail = new \PHPMailer\PHPMailer\PHPMailer();

        try {
            //Server settings
            // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.ionos.es';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'info@fincatech.es';                     //SMTP username
            // $mail->Password   = 'hXn*k@9Kr$XqQaR3';                               //SMTP password
            $mail->Password   = 'Cr@t7Am@+1964*(/)?&2=&28';                               //SMTP password
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('info@fincatech.es', 'Fincatech');
            // $mail->addAddress($to, $name);     //Add a recipient
            $mail->addAddress($this->emailCertificado);

            // $mail->addAddress('oscar@happysoftware.es', 'tester');
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
           // $mail->Subject = $subject;
            $mail->Subject = $to.';'.$subject;
            $mail->Body    = $body;
            //$mail->AltBody = $body;

            $mail->send();
            //$this->registrarEnvioEmail($to, $subject, $body, $save);
            return true;
        } catch (\Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } 
    }

    /** Envía un e-mail mediante la cuenta de ionos */
    public function SendEmail($to, $name, $subject, $body, $save = true)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        // $mail = new \PHPMailer\PHPMailer\PHPMailer();

        try {
            //Server settings
            // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.ionos.es';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@fincatech.es';                     //SMTP username
            $mail->Password   = 'hXn*k@9Kr$XqQaR3';                               //SMTP password
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('no-reply@fincatech.es', 'Fincatech');
            $mail->addAddress($to, $name);     //Add a recipient
            // $mail->addAddress('oscar@happysoftware.es', 'tester');
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->AltBody = $body;

            $mail->send();
            $this->registrarEnvioEmail($to, $subject, $body, $save);
            return true;
        } catch (\Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } 
    }

    private function registrarEnvioEmail($to, $subject, $body, $save = true)
    {
        //  Inicializamos el controller de Mensajes
        $params = [];
        $params['email'] = $to;
        $params['subject'] = $subject;
        $params['body'] = $body;
        $this->InitController('Mensaje', $params);
        if($save)
            $this->MensajeController->_Save();
    }

    public function SendTestEmail()
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        // $mail = new \PHPMailer\PHPMailer\PHPMailer();

        try {
            //Server settings
            // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.ionos.es';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@fincatech.es';                     //SMTP username
            $mail->Password   = 'hXn*k@9Kr$XqQaR3';                               //SMTP password
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('no-reply@fincatech.es', 'Fincatech');
            $mail->addAddress('hola@happysoftware.es', 'Óscar Rodríguez');     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return 'Message has been sent';
        } catch (\Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}