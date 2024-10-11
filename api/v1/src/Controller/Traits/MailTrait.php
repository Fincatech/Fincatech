<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de emails
*
*
**/
nameSpace HappySoftware\Controller\Traits;

use HappySoftware\Controller\HelperController;
use HappySoftware\Database\DatabaseCore;

trait MailTrait{


    private $emailCertificado = 'certificar@emailcert.mensatek.com';

    private $mensatek_api_user = '763297793EB0Z1644447600000000000';  // 763297793EB0Z1644447600000000000
    private $mensatek_api_token = '1AA94CAAF8C06EB56347C34C0F233C81'; // 1AA94CAAF8C06EB56347C34C0F233C81
    private $mensatek_api_url = 'https://api.mensatek.com/v7/';
    private $mensatek_api_endpoint_enviarmailcertificado = 'EnviarEMAILCERTIFICADO';
    private $mensatek_api_endpoint_recuperarpdf = 'GetCertificadoEMAILCERTIFICADO';
    private $mensatek_api_endpoint_recuperarpdfSMS = 'GetSMSCERTIFICADO';
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
        try{
            $curl = curl_init();
            curl_setopt_array($curl, 
                array(
                    CURLOPT_URL => $this->mensatek_api_url . $this->mensatek_api_endpoint_recuperarpdf,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'Idmensaje' => $Idmensaje, 
                    ),
                    CURLOPT_HTTPHEADER => array('Authorization: Basic ' . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token))  
                )
            );

            $response = curl_exec($curl);
            // $this->WriteToLog('emailcertificado_', 'MailTrait -> getPDFEmailCertificado', $response);
            curl_close($curl);
            return $response;
        }catch(\Exception $ex){
            die('Exception MailTrait (getPDFEmailCertificado): ' . $ex->getMessage());
        }
    }

    /**
     * Recupera el documento de acuse de recibo del sms certificado
     */
    public function getPDFSMSCertificado($IdMensaje, $telefono)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.mensatek.com/v7/GetSMSCERTIFICADO",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"Idmensaje\"\r\n\r\n" . $IdMensaje . "\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"Telefono\"\r\n\r\n" . $telefono . "\r\n-----011000010111000001101001--\r\n",
          CURLOPT_HTTPHEADER => [
            "Authorization: Basic : "  . base64_encode($this->mensatek_api_user . ':' . $this->mensatek_api_token),
            "Content-Type: multipart/form-data; boundary=---011000010111000001101001"
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);        
        if ($err) 
        {
            return false;
        }else{
            return $response;
        }
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
        try{

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

            //  Inicializamos el api de Mensatek
                $usuarioId = $this->getLoggedUserId();
                if($usuarioId > 0)
                {
                    $this->InitializeAPIEmailCertificado($usuarioId);
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
                    'Remitente' => $this->mensatek_remitente,
                    'Asunto' => $subject,
                    'Mensaje' => $body,
                    'Destinatarios' => json_encode($destinatariosEmail),
                    'Adjuntos' => json_encode($ficherosAdjuntos),
                    'Resp' => 'XML',
                    'Report' => 1
                );

            // print_r($cuerpoPeticion);
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
            $this->WriteToLog('emailcertificado', 'MailTrait -> SendEmailCertificadoAdjuntos', $response);
            return $response;
        }catch(\Exception $ex){
            die('Excepción en SendEmailCertificadoAdjuntos: ' . $ex->getMessage());
        }

    }

    /**
     * Envía un e-mail certificado simple desde el panel del administrador de fincas
     */
    public function SendEmailCertificado($subject, $destinatarios, $body, $usuarioId = null, $singleNameDestinatario = null, $ficheroAdjunto = null)
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

        $subject = is_null($ficheroAdjunto) ? $subject : $subject . ' [documento adjunto]';

        //  Componemos el cuerpo de la petición
            $cuerpoPeticion = array(
                'Remitente' => $this->mensatek_remitente,
                'Asunto' => $subject,
                'Mensaje' => $body,
                'Destinatarios' => json_encode($destinatariosEmail),
                'Resp' => 'XML',
                'Report' => 1
            );

        //  Si tiene fichero adjunto, lo incluimos en la petición
            if(!is_null($ficheroAdjunto))
            {
                //  Comprobamos si el nombre del fichero excede de los 20 caracteres que es el permitido por Mensatek
                $fichero['Nombre'] = 'documento.pdf';
                //  Procesamos el fichero decodificándolo primero para obtener el binario
                $contenidoDecodificado = base64_decode(explode(',', $ficheroAdjunto['base64'])[1]);
                //  Sobre el contenido binario aplicamos la eliminación de espacios y lo codificamos de vuelta en base64
                //  Esto se hace porque no se almacena en el servidor el contenido del fichero
                $fichero['Contenido'] = chunk_split(base64_encode( $contenidoDecodificado ));
                $cuerpoPeticion['Adjuntos'] = Array();
                array_push($cuerpoPeticion['Adjuntos'], $fichero);
                $cuerpoPeticion['Adjuntos'] = json_encode($cuerpoPeticion['Adjuntos']);
            }

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
        }else{
            return false;
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
    public function SendEmail($to, $name, $subject, $body, $save = true, $adjuntos = null)
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
            $destinatarios = explode(',',$to);
            foreach($destinatarios as $destinatario){
                $mail->addAddress($destinatario, $name);     //Add a recipient
            }

            // $mail->addAddress('oscar@happysoftware.es', 'tester');
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->AltBody = $body;

            //  Comprobamos si tiene adjuntos para poder enviarlos en el cuerpo de la petición
            if(!is_null($adjuntos) && is_array($adjuntos))
            {
                for($x = 0; $x < count($adjuntos); $x++)
                {
                    if(file_exists($adjuntos[$x]))
                    {
                        $mail->addAttachment($adjuntos[$x]);
                    }
                }
            }

            //  Si solo hay especificado un fichero adjunto
            if(!is_null($adjuntos) && !is_array($adjuntos))
            {
                if(file_exists($adjuntos)){
                    $mail->addAttachment($adjuntos);
                }
            }

            $mail->send();
            $this->registrarEnvioEmail($to, $subject, $body, $save);
            return true;
        } catch (\Exception $e) {
            //  Registramos el error y enviamos al admin y desarrollo la información del error
            // $this->SendErrorMail($mail->ErrorInfo, $subject, $body);
            // $this->AddToLog('log_envioemail','SendEmail', 'No se ha podido enviar el e-mail con asunto: ' . $subject);
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        } 
    }

    /**
     * Envía un e-mail a soporte con la información del error de por qué no se ha podido enviar un e-mail
     */
    private function SendErrorMail($error, $subject, $body)
    {
        $this->SendEmail('desarrollo@fincatech.es','Desarrollo','Error envío e-mail', 'Ha ocurrido el siguiente error al intentar enviar el e-mail:<br><br>' . $error . '<br><br>Asunto: ' . $subject . '<br><br>E-mail:<br><br>' . $body, false);
    }

    /**
     * Registra en bbdd el envío del e-mail
     * @param string $to. Destinatario
     * @param string $subject. Asunto del e-mail
     * @param string $body. Cuerpo del e-mail
     * @param bool $save (optional). Defaults: True. Si es true guarda el mensaje
     */
    private function registrarEnvioEmail($to, $subject, $body, $save = true)
    {
        //  Si no está marcado para guardar, devolvemos el control al método desde el que fue llamado
        if(!$save)
            return;

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