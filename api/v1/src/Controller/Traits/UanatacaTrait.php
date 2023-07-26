<?php

namespace HappySoftware\Controller\Traits;

trait UanatacaTrait{

    private $uanatacaEnv = 'dev';

    /**
     * Recupera una nueva scratchcard para poder generar una solicitud de certificado
     */
    public function GetFirstUnusedScratchCard()
    {

        global $appSettings;

        $hostAPI = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'];
        $url = $hostAPI.'scratchcards/get_first_unused/';

        $postFields = [
            'ra' => $appSettings['uanataca'][$this->uanatacaEnv]['autoridadregistroid']
        ];
        
        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        $ch = curl_init();

        // Por alguna extraña razón, no conecta al entorno de desarrollo que viene en la documentación de uanataca
        if($this->uanatacaEnv == 'dev')
        {
            $certSandboxFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'Bit4idCA.crt';
            curl_setopt($ch, CURLOPT_CAINFO, $certSandboxFile);
        }
     

        curl_setopt($ch, CURLOPT_SSLCERT,$certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'pem');  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);          
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $sub = curl_exec ($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($http_code != 200)
        {
            return 'Error: ' . $http_code;
        }

        $respuesta = json_decode($sub, true);

        //  Devolvemos el serial number
        return $respuesta['sn'];

        /* RESPUESTA 
            {
            "pk": 1193,
            "sn": "1256948",
            "secrets": "{\"erc\": \"6292998123\", \"enrollment_code\": \"_,463vt:\", \"pin\": \"08695572\", \"puk\": \"52351291\"}",
            "registration_authority": 121
            }
        */
    }

    /** Retrieve RAO ID list */
    public function GetIdentificationRaoList()
    {
        global $appSettings;
        $hostAPI = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'];

        $url = $hostAPI.'api/v1/rao/';
        
        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLCERT,$certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'pem');  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);          
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $sub = curl_exec ($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($http_code != 200)
        {
            return 'Error: ' . $http_code;
        }

        $respuesta = json_decode($sub, true);

        //  Devolvemos el serial number
        return $respuesta;
    }

    /** Get Identification RAO by ID */
    public function GetIdentificationRao($raoID)
    {
        global $appSettings;
        $hostAPI = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'];

        $url = $hostAPI.'api/v1/rao/' . $raoID;
        
        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLCERT,$certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'pem');  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);          
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        $sub = curl_exec ($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($http_code != 200)
        {
            return 'Error: ' . $http_code;
        }

        $respuesta = json_decode($sub, true);

        //  Devolvemos el serial number
        return $respuesta['pk'];  
    }

    /** Genera una solicitud de certificado en uanataca */
    public function UanatacaCreateCertificateRequest($representanteLegal, $comunidad, $documentos)
    {

        global $appSettings;

        $representanteDocumentoIdentificativo = $representanteLegal['documento'];
        //  Para el entorno de sandbox hay que poner el nombre como TEST
        $representanteNombre = $representanteLegal['nombre'];
        $representanteNombre = 'TEST';
        $representanteApellido = $representanteLegal['apellido'];
        $representanteApellido2 = $representanteLegal['apellido2'];
        $representanteEmail = $representanteLegal['email'];
        $representanteTelefono = $representanteLegal['telefono'];   
        //  Primero hay que hacer una llamada para recuperar un scratchcard para poder posteriormente
        //  crear un request sobre esa scratchcard
        $scratchcard = $this->GetFirstUnusedScratchCard();

        if($scratchcard === false)
        {
            //  Devolvemos un error
            return 'Error: Scratchcard not generated by API';
        }

        //  URL de la api
        $url = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'] . 'requests/';
        //  ID de la entidad registradora
        $registration_authority = $appSettings['uanataca'][$this->uanatacaEnv]['autoridadregistroid'];
        $raoID = $appSettings['uanataca'][$this->uanatacaEnv]['raoid'];

        //  Recuperamos los documentos que ha aportado para la comunidad
        //  Solo aquellos que estén aprobados. ID Estado = 6. El resto no se suben
        $representation_documents = [];
        $representation_documents[] = 'CIF/NIF Representante legal';
        for($xDocs = 0; $xDocs < count($documentos); $xDocs ++)
        {
            if($documentos[$xDocs]['idestado'] == '6')
            {
                switch($documentos[$xDocs]['requerimiento'])
                {
                    case 'CIF/NIF Representante legal':
                        $representation_documents[] = 'CIF/NIF Representante legal';
                        break;
                    case 'CIF de la comunidad':
                        $representation_documents[] = 'CIF Comunidad';
                        break;
                    case 'Copia de la primera hoja del libro de actas':
                        $representation_documents[] = $documentos[$xDocs]['requerimiento'];
                        break;
                    case 'Copia del acta donde se es elegido administrador':
                        $representation_documents[] = 'Acta nombramiento';
                        break;
                    case 'Declaración responsable y solicitud del certificado digital':
                        $representation_documents[] = 'Declaración responsable';
                        break;
                    case 'Copia del acta (Modelo acuerdo junta general)':
                        $representation_documents[] = 'Copia del acta (Modelo acuerdo junta general)';
                        break;
                    case 'Modelo autorización presidente':
                        $representation_documents[] = 'Acta acuerdo cesión';
                        break;
                }
            }

        }
        //$representation_documents = 'cif comunidad, cif/nif administrador, Acta nombramiento, acta cuerdo cesión';
        $representation_documents = implode(',',$representation_documents);

        //$this->GetIdentificationRaoList();
        //  Cuerpo de la petición
        $postFields = [
            "secure_element" => "0",
            "profile" => "REPESPJsoft",
            'validity_time' => $appSettings['uanataca']['validity_time'],
            "registration_authority" => $registration_authority,
            "scratchcard" => $scratchcard,
            "id_document_type" => "TIN",
            "id_document_country" => "ES",
            "serial_number" => $representanteDocumentoIdentificativo,           // NIF del administrador
            "given_name" => $representanteNombre,
            "surname_1" => $representanteApellido,
            "title" => 'Representante legal',
            "email" => $representanteEmail,                                     //  Email del titular del certificado
            "mobile_phone_number" => $representanteTelefono,                    //  Teléfono del administrador,
            'responsible_name' => $representanteNombre,                         //  Nombre del administrador,
            'responsible_first_surname' => $representanteApellido,              //  Apellido del administrador
            'organization_country' => 'ES',
            'organization_identifier' => $comunidad['cif'],         //'CIF comunidad',           //  CIF de la comunidad 
            'organization_name' => $comunidad['nombre'],            //  Nombre de la comunidad
            'organization_email' => 'info@fincatech.es',            //  Email de la comunidad
            'empowerment' => 'Total',
            'description' => 'Certificado comunidad de propietarios', // Fecha del día¿?
            'representation' => $representation_documents,
            'responsible_serial' => $representanteDocumentoIdentificativo,
            "country_name" => "ES",
        ];

        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields) );
        curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
        curl_setopt($ch, CURLOPT_CAINFO, $certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'pem');   
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'C3ba3Cm_');
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $response = curl_exec ($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        
        //  Error de conexión con cURL
        if($error){
            $result = [];
            $result['http_code'] = $http_code;
            $result['message'] = 'cURL Error: ' . $error;
            return $result;
        }

        //  Evaluamos si la creación ha sido satisfactoria
        $result = [];
        $result['http_code'] = $http_code;
        $responseJSON = json_decode($response, true);        
        switch($http_code){
            case 201: // Created
                $result['message'] = $responseJSON;
                break;
            default: // Otros
                $result['message'] = isset($responseJSON['detail']) ? $responseJSON['detail'] : $responseJSON['error'];
                break;
        }

        //  Si todo va bien, devolvemos el pk del certificado solicitado
        return $result;

    }

    /** 
     * Upload document to associated Certificate Request 
    */
    public function UploadDocumentToCertificateRequest($pkCertificate, $filePath, $documentType, $requirementName = '')
    {
        /*
            Endpoint: https://api.uanataca.com/api/v1/requests/{id}/pl_upload_document
            +   En el request hay que enviar el document type que es un enum: type
                    "document_front"
                    "document_rear"
                    "document_owner"
                    "extra_document"
            +   Ruta física del fichero: document
            +   Establecer el contentype a: Content-Type: multipart/form-data
        */

        global $appSettings;
        $fileUploaded = false;

        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        //  URL de la api
        $url = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'] . 'requests/' . $pkCertificate . '/pl_upload_document/';

        //  Seteamos el mime-type para el fichero según su extensión
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        //  Cuerpo de la petición
        $postFields = [
            "document" => new \CURLFile($filePath, $mime_type),
            "type" => $documentType,
        ];

        // Generar un límite único
        $boundary = uniqid();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data; boundary=' . $boundary));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields );
        curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
        curl_setopt($ch, CURLOPT_CAINFO, $certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'pem');   
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'C3ba3Cm_');
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $response = curl_exec ($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        unset($ch);
        //  Cerramos el buffer del fichero temporal
        // fclose($docToUpload);

        //  Error de conexión con cURL
        if($error){
            $result = [];
            $result['http_code'] = $http_code;
            $result['message'] = 'cURL Error: ' . $error;
            return $result;
        }

        //  Evaluamos si la creación ha sido satisfactoria
        $result = [];
        $result['http_code'] = $http_code;
        $responseJSON = json_decode($response, true);        

        $this->_logMessage .= '+ Documento adjuntado a la solicitud con PK: ' . $pkCertificate . PHP_EOL;

        switch($http_code){
            case 201: // Created
                $fileUploaded = true;
                $this->_logMessage .= '[OK] Fichero: ' . $filePath . ' - Requerimiento: ' . $requirementName . PHP_EOL;
                $result['message'] = $responseJSON;
                break;
            default: // Otros
                $fileUploaded = false;
            $this->_logMessage .= '[ERROR] Fichero: ' . $filePath . ' - Requerimiento: ' . $requirementName . PHP_EOL;
            $this->_logMessage .= 'Error: ' . $responseJSON['error'] . PHP_EOL;
                $result['message'] = isset($responseJSON['detail']) ? $responseJSON['detail'] : $responseJSON['error'];
                break;
        }
        
        //  Devolvemos el PK que es el ID de Uanataca para ese fichero
        return $result;        

    }

    /** Aproval request certificate 
     * @param int $pkCertificate Certificate Generated Id
     * @return 
    */
    public function RequestApproval($pkCertificateRequest){

        /*
            Endpoint: https://api.uanataca.com/api/v1/requests/{id}/pl_approve/
                "username": "string",
                "password": "string",
                "pin": "string",
                "rao_id": "string",
                "lang": "string"
        */

        global $appSettings;

        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        //  URL de la api
        $url = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'] . 'requests/' . $pkCertificateRequest . '/pl_approve/';
        //$this->GetIdentificationRaoList();
        //  Cuerpo de la petición
        $postFields = [
            "username" => $appSettings['uanataca']['credentials']['user'],
            "password" => $appSettings['uanataca']['credentials']['pass'],
            "pin" => $appSettings['uanataca']['credentials']['pin'],
            "rao_id" => $appSettings['uanataca'][$this->uanatacaEnv]['raoid'],
            "lang" => "ES",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields) );
        curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
        curl_setopt($ch, CURLOPT_CAINFO, $certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'pem');   
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'C3ba3Cm_');
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $response = curl_exec ($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        /*
        "{"secrets":
            {"puk":"51197927","enrollment_code":"X7bhQq2r","pin":"57874336","erc":"8151896766"},
            "request":
            {"pk":54365,"given_name":"TEST","surname_1":"Rodríguez","surname_2":null,"sex":null,"id_document_type":"TIN","id_document_country":"ES","serial_number":"08943954J","country_name":"ES","citizenship":null,"residence":"ES","organization_email":"info@fincatech.es","email":"ororodeveloper@gmail.com","title":"Administrador de Fincas / Representante legal","organization_name":"Begoña","organizational_unit_1":null,"organizational_unit_2":null,"organization_identifier":"H92246263","responsible_name":"TEST","responsible_first_surname":"Rodríguez","responsible_second_surname":null,"responsible_email":null,"responsible_serial":"08943954J","responsible_position":null,"subscriber_responsible_serial":null,"administrative_unit":null,"empowerment":"Total","representation":"CIF/NIF Representante legal,CIF Comunidad,Copia de la primera hoja del libro de actas,Acta nombramiento,Declaración responsable,Copia del acta (Mode"
        */
        //  Error de conexión con cURL
        if($error){
            $result = [];
            $result['http_code'] = $http_code;
            $result['message'] = 'cURL Error: ' . $error;
            return $result;
        }

        //  Evaluamos si la creación ha sido satisfactoria
        $result = [];
        $result['http_code'] = $http_code;
        $responseJSON = json_decode($response, true);        
        switch($http_code){
            case 200: // Created
                $result['message'] = $responseJSON;
                break;
            default: // Otros
                $result['message'] = isset($responseJSON['detail']) ? $responseJSON['detail'] : $responseJSON['error'];
                break;
        }

        //  Si todo va bien, devolvemos el pk del certificado solicitado
        return $result;
    }

    /**
     * Cancel certificate request 
     * @param string $pkRequestCertificate
     * @param string $errorMessage
     */
    public function CancelRequestCertificate($pkRequestCertificate)
    {
        // URL: https://api.uanataca.com/api/v1/requests/25139/cancel/

        global $appSettings;

        //  CERTIFICADO
        $certFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'cer.pem';
        //  LLAVE
        $keyFile = ROOT_DIR . '/api/v1/' . $appSettings['uanataca'][$this->uanatacaEnv]['certpath'] . 'key.pem';

        //  URL de la api
        $url = $appSettings['uanataca'][$this->uanatacaEnv]['apihost'] . 'requests/' . $pkRequestCertificate . '/cancel/';        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields) );
        curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
        curl_setopt($ch, CURLOPT_CAINFO, $certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'pem');   
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'C3ba3Cm_');
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $response = curl_exec ($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);        

        //  Error de conexión con cURL
        if($error){
            $result = [];
            $result['http_code'] = $http_code;
            $result['message'] = 'cURL Error: ' . $error;
            return $result;
        }
    }

}