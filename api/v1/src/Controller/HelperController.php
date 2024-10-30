<?php

namespace HappySoftware\Controller;

use Firebase\JWT\JWT;
use HappySoftware\Controller\Traits;
use HappySoftware\Controller\Traits\LogTrait;
use DI\ContainerBuilder;

class HelperController
{

    use \HappySoftware\Controller\Traits\SecurityTrait;
    use LogTrait;

    /**
     * Convert any value to Float
     * @return float Value converted
     */
    public static function ConvertToFloat($value)
    {
        //  Reemplazamos las posibles commas a . antes del parseo
        $value = str_replace(',','.', $value);
        return (float)$value;
    }

    /**
     * Genera un nombre válido para el fichero XML de sepa
     */
    public static function GenerateXMLFileName(string $nombreAdministrador, $month, $year)
    {
        $tmpFile = 'FINCA_SEPA_' . HelperController::GenerarLinkRewrite($nombreAdministrador);
        $tmpFile .= '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . $year . '.xml';
        return $tmpFile;        
    }

    /**
     * Recupera la información del usuario autenticado
     */
    public static function getLoggedUserInfo()
    {
        $userData = null;
        if(isset( $_COOKIE['FINCATECHTOKEN'] ) )
        {
            $tokenJWT = $_COOKIE['FINCATECHTOKEN'];
            $userData = get_object_vars(JWT::decode($tokenJWT, JWT_SECRET_KEY, array('HS512')))['userData'];
        }else{
            $userData = [];
            $userData['id'] = null;
            $userData['login'] = null;
            $userData['nombre'] = null;
            $userData['email'] = null;
            $userData['role'] = null;
            $userData['accesscae'] = null;
            $userData['accessrgpd'] = null;
        }

        return $userData; //['user'];//['userdata'];

    } 

    /**
     * Devuelve el directorio raíz del proyecto
     */
    public static function RootDir()
    {
        //  Obtenemos el raíz del proyecto
        $basePath = $_SERVER['DOCUMENT_ROOT'] . dirname(dirname(dirname(dirname($_SERVER['PHP_SELF']))));
        return $basePath;
    }

    /**
     * Devuelve la URL principal
     * @return string URL principal de la aplicación
     */
    public static function RootURL()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        // Obtener el nombre del servidor y el puerto (si es diferente al puerto estándar)
        $host = $_SERVER['HTTP_HOST']; // Puede incluir el puerto si es diferente del estándar
        
        // Obtener la carpeta base del proyecto (si está en una subcarpeta)
        $scriptName = $_SERVER['SCRIPT_NAME'];

        //  Obtenemos el directorio principal si es que lo hubiese        
        $basePath = dirname($scriptName);
        $basePath = str_replace('/api/v1/public', '', $basePath);
        
        // Construir la URL base completa
        $baseUrl = $scheme . '://' . $host . $basePath;
        
        return $baseUrl;

    }

    /**
     * Envía respuesta inmediata desde el WS
     */
    public static function sendProgressResponse($progressTotal, $progress, $message, $inProgressStatus = true)
    {

        //  Calculamos el porcentaje sobre el total
        $progress = $progress * 100 / $progressTotal;
        $progress = number_format($progress, 2, '.','');
        $data = array(
            'data' => $inProgressStatus ? 'progress' : 'completed',
            'progress' => $progress,
            'progresstotal' => $progressTotal,
            'message' => $message
        );
        
        // Convertir datos a JSON
        echo "data: " . json_encode($data) . "\n\n";        
        ob_flush();
        flush();
    }

    public static function successResponse($data, $codeResponse = 200)
    {

        // self::getLoggedUserInfo();
        // HelperController::getLoggedUserInfo();
        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['user'] = self::getLoggedUserInfo();
        $responseData['status']['response'] = 'ok';
        $responseData['status']['code'] = $codeResponse;

        if(isset($responseData['data']['total']))
        {
          //  $responseData['draw'] = 1; // Draw count para prevenir XSS ataques
            $responseData['recordsTotal'] = $responseData['data']['total'];
            $responseData['recordsFiltered'] = $responseData['data']['total'];
        }

        return json_encode($responseData);
    }

    public static function errorResponse($data, $mensaje, $codeResponse = 400)
    {
        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['status']['response'] = 'error';
        $responseData['status']['error'] = $mensaje;
        $responseData['status']['code'] = $codeResponse;
        return json_encode($responseData);
    }

    /** Genera un password aleatorio de n caracteres */
    public static function GenerateRandomPassword($longitud = 8)
    {

        $comb = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $shfl = str_shuffle($comb);
        $pwd = substr($shfl, 0, $longitud);
        return $pwd;
    }

    /** Devuelve la fecha actual basándonos en el timezone de españa */
    public static function DateNow($mysqlFormat = false)
    {
        date_default_timezone_set('Europe/Madrid');
        if($mysqlFormat)
        {
            return date('Y-m-d H:i:s');
        }else{
            return date('d-m-Y H:i:s');
        }
            
    }

    public function addToLog($logName,$functionName,$texto)
    {
        $this->WriteToLog($logName,$functionName,$texto);
    }

    public static function customAlphanumericSort($a, $b) {
            // Extraer partes alfabéticas y numéricas de $a
            preg_match('/(\D+)?(\d+)?/', $a['codigo'], $matchesA);
            $alphaA = isset($matchesA[1]) ? trim($matchesA[1]) : '';
            $numA = isset($matchesA[2]) ? (int)$matchesA[2] : 0;

            // Extraer partes alfabéticas y numéricas de $b
            preg_match('/(\D+)?(\d+)?/', $b['codigo'], $matchesB);
            $alphaB = isset($matchesB[1]) ? trim($matchesB[1]) : '';
            $numB = isset($matchesB[2]) ? (int)$matchesB[2] : 0;

            // Comparar la parte alfabética primero
            $alphaComparison = strcmp($alphaA, $alphaB);
            if ($alphaComparison !== 0) {
                return $alphaComparison;
            }

            // Si las partes alfabéticas son iguales, comparar las partes numéricas
            return $numA <=> $numB;        
    }

    /**
     * Devuelve el nombre en español del mes
     * @param int $month (Optional). Mes del que se desea obtener el nombre
     * @return string Nombre en español del mes
     */
    public static function StringMonth($month = null)
    {
        if(is_null($month))
            $month = date('m');

        $months = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
            ];        
        return ucfirst($months[intval($month)]);
    }

    /**
     * Devuelve el código BIC según el IBAN de un banco
     * @param string $iban IBAN Completo del banco
     * @return string|null Código BIC o Null si no existe
     */
    public static function GetBICFromIBAN($iban)
    {
        
        $result = null;

        //  Array con los códigos de entidad 
        $bicIBAN = [
            "0019" => "DEUTESBBXXX",
            "0030" => "ESPCESMMXXX",
            "0049" => "BSCHESMMXXX",
            "0073" => "OPENESMMXXX",
            "0075" => "POPUESMMXXX",
            "0078" => "BAPUES22XXX",
            "0081" => "BSABESBBXXX",
            "0082" => "CASTES2SXXX",
            "0083" => "RENBESMMXXX",
            "0085" => "BDERESMMXXX",
            "0086" => "NORTESMMXXX",
            "0128" => "BKBKESMMXXX",
            "0138" => "BKOAES22XXX",
            "0182" => "BBVAESMMXXX",
            "0216" => "POHIESMMXXX",
            "0232" => "INVLESMMXXX",
            "0237" => "CSURES2CXXX",
            "2010" => "CECAESMM010",
            "2038" => "CAHMESMMXXX",
            "2048" => "CECAESMM048",
            "2080" => "CAGLESMMVIG",
            "2085" => "CAZRES2ZXXX",
            "2095" => "BASKES2BXXX",
            "2096" => "CSPAES2LXXX",
            "2100" => "CAIXESBBXXX",
            "2103" => "UCJAES2MXXX",
            "2108" => "CSPAES2L108",
            "3001" => "BCOEESMM001",
            "3005" => "BCOEESMM005",
            "3008" => "BCOEESMM008",
            "3016" => "BCOEESMM016",
            "3017" => "F42001255",
            "3023" => "BCOEESMM023",
            "3035" => "CLPEES2M",
            "3058" => "CCRIES2A",
            "3060" => "BCOEESMM060",
            "3067" => "BCOEESMM067",
            "3081" => "BCOEESMM081",
            "3085" => "BCOEESMM085",
            "3118" => "CCRIES2A118",
            "3140" => "BCOEESMM140",
            "3150" => "BCOEESMM150",
            "3159" => "BCOEESMM159",
            "3183" => "CASDESBB",
            "3187" => "BCOEESMM187",
            "3191" => "BCOEESMM191",
            "6719" => "UNAXES22XXX",
        ];

        //  Formateamos el número iban para asegurarnos que siempre viene la misma información
        $iban = HelperController::NormalizeIBAN($iban);

        //  Extraemos los 4 dígitos que corresponden a la entidad bancaria
        if(strlen($iban) == 24)
        {
            $codigoEntidad = substr($iban, 4,4);
            if (array_key_exists($codigoEntidad, $bicIBAN)) {
                $result = $bicIBAN[$codigoEntidad];
            }            
        }

        return $result;

    }

    /**
     * Crea un debtorMandate único
     * @param string $invoiceNumero. Número de la factura con la serie
     * @param int $idAdministrador. ID del administrador
     * @param int $idComunidad. ID de la comunidad
     * @return string DebtorMandate generado
     */
    public static function GenerateDebtorMandate(string $invoiceNumero, int $idAdministrador, int $idComunidad)
    {
        //  Tamaño máximo según especificación: 35 caracteres
        //  Composición:
        //  ID Administrador + ID Comunidad + Número de factura
        //  ID Administrador    -> 6 caracteres
        //  ID Comunidad        -> 6 caracteres
        //  Número Factura      -> 10 caracteres
        $sComunidad = str_pad($idComunidad, 6, '0', STR_PAD_LEFT);
        $sAdministrador = str_pad($idAdministrador, 6, '0', STR_PAD_LEFT);
        
        return $sAdministrador . $sComunidad . $invoiceNumero;

    }

    /**
     * Normaliza el código IBAN quitándole espacios y guiones
     */
    public static function NormalizeIBAN($iban)
    {
        $iban = trim($iban);
        //  Quitamos espacios
        $iban = str_replace(' ','', $iban);
        //  Quitamos posibles guiones
        $iban = str_replace('-','', $iban);
        //  Transformamos a mayúsculas
        $iban = strtoupper($iban);
        return $iban;
    }

    /**
     * Valida IBAN de banco de todos los países
     * @param string $iban IBAN que se va a comprobar
     * @return bool Resultado de la validación
     */
    public static function ValidateIban($iban) {
        $iban = strtolower(str_replace(' ', '', $iban));
        
        $countryCode = substr($iban, 0, 2);
        $checkDigits = substr($iban, 2, 2);
        $bban = substr($iban, 4);
        
        $ibanLengths = [
            'ad' => 24, 'ae' => 23, 'al' => 28, 'at' => 20, 'az' => 28, 'ba' => 20, 'be' => 16, 
            'bg' => 22, 'bh' => 22, 'br' => 29, 'ch' => 21, 'cr' => 22, 'cy' => 28, 'cz' => 24, 
            'de' => 22, 'dk' => 18, 'do' => 28, 'ee' => 20, 'es' => 24, 'fi' => 18, 'fo' => 18, 
            'fr' => 27, 'gb' => 22, 'ge' => 22, 'gi' => 23, 'gl' => 18, 'gr' => 27, 'gt' => 28, 
            'hr' => 21, 'hu' => 28, 'ie' => 22, 'il' => 23, 'is' => 26, 'it' => 27, 'jo' => 30, 
            'kw' => 30, 'kz' => 20, 'lb' => 28, 'li' => 21, 'lt' => 20, 'lu' => 20, 'lv' => 21, 
            'mc' => 27, 'md' => 24, 'me' => 22, 'mk' => 19, 'mr' => 27, 'mt' => 31, 'mu' => 30, 
            'nl' => 18, 'no' => 15, 'pk' => 24, 'pl' => 28, 'ps' => 29, 'pt' => 25, 'qa' => 29, 
            'ro' => 24, 'rs' => 22, 'sa' => 24, 'se' => 24, 'si' => 19, 'sk' => 24, 'sm' => 27, 
            'tn' => 24, 'tr' => 26, 'ua' => 29, 'vg' => 24, 'xk' => 20
        ];
        
        if (!isset($ibanLengths[$countryCode])) {
            return false; // Invalid country code
        }
        
        if (strlen($iban) != $ibanLengths[$countryCode]) {
            return false; // Invalid IBAN length
        }
        
        // Move the four initial characters to the end of the string
        $movedIban = $bban . $countryCode . $checkDigits;
        
        // Replace letters with numbers (A = 10, B = 11, ..., Z = 35)
        $ibanNumeric = '';
        foreach (str_split($movedIban) as $char) {
            if (ctype_alpha($char)) {
                $ibanNumeric .= ord($char) - 87; // ord('a') is 97, ord('b') is 98, ..., so we subtract 87 to get 10, 11, ...
            } else {
                $ibanNumeric .= $char;
            }
        }
        
        // Perform the mod 97 operation
        $checksum = intval(substr($ibanNumeric, 0, 1));
        for ($i = 1, $len = strlen($ibanNumeric); $i < $len; $i++) {
            $checksum = ($checksum * 10 + intval($ibanNumeric[$i])) % 97;
        }
        
        return $checksum === 1;
    }

    /**
     * Redondea un número hacia arriba
     */
    public static function Redondeo($number, $decimals = 3)
    {
        $factor = pow(10, $decimals);
        return number_format(floor($number * $factor) / $factor, $decimals, '.', '');        
        // return floor($number * 100) / 100;
    }

    /** Extraido del core de Prestashop: classes/Tools.php */
    public static function GenerarLinkRewrite($str) {
        static $array_str = [];
        static $allow_accented_chars = null;
        static $has_mb_strtolower = null;
    
        if ($has_mb_strtolower === null) {
            $has_mb_strtolower = function_exists('mb_strtolower');
        }
    
        if (!is_string($str)) {
            return false;
        }
    
        if (isset($array_str[$str])) {
            return $array_str[$str];
        }
    
        if ($str == '') {
            return '';
        }
    
        if ($allow_accented_chars === null) {
            $allow_accented_chars = false;
        }
    
        $return_str = trim($str);
    
        if ($has_mb_strtolower) {
            $return_str = mb_strtolower($return_str, 'utf-8');
        }
        if (!$allow_accented_chars) {
            $return_str = HelperController::ReplaceAccentedChars($return_str);
        }
    
        // Remove all non-whitelist chars.
        if ($allow_accented_chars) {
            $return_str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\p{L}]/u', '', $return_str);
        } else {
            $return_str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $return_str);
        }
    
        $return_str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $return_str);
        $return_str = str_replace([' ', '/'], '-', $return_str);
    
        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.
        if (!$has_mb_strtolower) {
            $return_str = HelperController::Strtolower($return_str);
        }
    
        $array_str[$str] = $return_str;
    
        return $return_str;
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     *
     * @return string
     */
    public static function ReplaceAccentedChars($str)
    {
        /* One source among others:
            http://www.tachyonsoft.com/uc0000.htm
            http://www.tachyonsoft.com/uc0001.htm
            http://www.tachyonsoft.com/uc0004.htm
        */
        $patterns = [
            /* Lowercase */
            /* a  */ '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}\x{00C0}-\x{00C3}\x{1EA0}-\x{1EB7}]/u',
            /* b  */ '/[\x{0431}]/u',
            /* c  */ '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
            /* d  */ '/[\x{010F}\x{0111}\x{0434}\x{0110}\x{00F0}]/u',
            /* e  */ '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}\x{00C8}-\x{00CA}\x{1EB8}-\x{1EC7}]/u',
            /* f  */ '/[\x{0444}]/u',
            /* g  */ '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
            /* h  */ '/[\x{0125}\x{0127}]/u',
            /* i  */ '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}\x{00CC}\x{00CD}\x{1EC8}-\x{1ECB}\x{0128}]/u',
            /* j  */ '/[\x{0135}\x{0439}]/u',
            /* k  */ '/[\x{0137}\x{0138}\x{043A}]/u',
            /* l  */ '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
            /* m  */ '/[\x{043C}]/u',
            /* n  */ '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
            /* o  */ '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}\x{00D2}-\x{00D5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u',
            /* p  */ '/[\x{043F}]/u',
            /* r  */ '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
            /* s  */ '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
            /* ss */ '/[\x{00DF}]/u',
            /* t  */ '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
            /* u  */ '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}\x{00D9}-\x{00DA}\x{0168}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u',
            /* v  */ '/[\x{0432}]/u',
            /* w  */ '/[\x{0175}]/u',
            /* y  */ '/[\x{00FF}\x{0177}\x{00FD}\x{044B}\x{1EF2}-\x{1EF9}\x{00DD}]/u',
            /* z  */ '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
            /* ae */ '/[\x{00E6}]/u',
            /* ch */ '/[\x{0447}]/u',
            /* kh */ '/[\x{0445}]/u',
            /* oe */ '/[\x{0153}]/u',
            /* sh */ '/[\x{0448}]/u',
            /* shh*/ '/[\x{0449}]/u',
            /* ya */ '/[\x{044F}]/u',
            /* ye */ '/[\x{0454}]/u',
            /* yi */ '/[\x{0457}]/u',
            /* yo */ '/[\x{0451}]/u',
            /* yu */ '/[\x{044E}]/u',
            /* zh */ '/[\x{0436}]/u',

            /* Uppercase */
            /* A  */ '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
            /* B  */ '/[\x{0411}]/u',
            /* C  */ '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
            /* D  */ '/[\x{010E}\x{0110}\x{0414}\x{00D0}]/u',
            /* E  */ '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
            /* F  */ '/[\x{0424}]/u',
            /* G  */ '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
            /* H  */ '/[\x{0124}\x{0126}]/u',
            /* I  */ '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
            /* J  */ '/[\x{0134}\x{0419}]/u',
            /* K  */ '/[\x{0136}\x{041A}]/u',
            /* L  */ '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
            /* M  */ '/[\x{041C}]/u',
            /* N  */ '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
            /* O  */ '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
            /* P  */ '/[\x{041F}]/u',
            /* R  */ '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
            /* S  */ '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
            /* T  */ '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
            /* U  */ '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
            /* V  */ '/[\x{0412}]/u',
            /* W  */ '/[\x{0174}]/u',
            /* Y  */ '/[\x{0176}\x{042B}]/u',
            /* Z  */ '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
            /* AE */ '/[\x{00C6}]/u',
            /* CH */ '/[\x{0427}]/u',
            /* KH */ '/[\x{0425}]/u',
            /* OE */ '/[\x{0152}]/u',
            /* SH */ '/[\x{0428}]/u',
            /* SHH*/ '/[\x{0429}]/u',
            /* YA */ '/[\x{042F}]/u',
            /* YE */ '/[\x{0404}]/u',
            /* YI */ '/[\x{0407}]/u',
            /* YO */ '/[\x{0401}]/u',
            /* YU */ '/[\x{042E}]/u',
            /* ZH */ '/[\x{0416}]/u',
        ];

        // ö to oe
        // å to aa
        // ä to ae

        $replacements = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH',
        ];

        return preg_replace($patterns, $replacements, $str);
    }

    public static function Strtolower($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($str, 'utf-8');
        }

        return strtolower($str);
    }

    /**
     * Convierte un archivo de imagen a base 64
     */    
    public static function ConvertImageToBase64($image){

        $data = file_get_contents($image);
        $base64 = base64_encode($data);
        $data_uri = "data:image/png;base64,".$base64;
      
        return $data_uri;
      
    }

    /**
     * Valida un CIF/NIF
     */    
    public static function ValidarNIFCIF($dni)
    {

        $cif = strtoupper(trim($dni));
        for ($i = 0; $i < 9; $i ++){
          $num[$i] = substr($cif, $i, 1);
        }
        // Si no tiene un formato valido devuelve error
        if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)){
          return false;
        }
        // Comprobacion de NIFs estandar
        if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)){
          if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)){
            return true;
          }else{
            return false;
          }
        }
        // Algoritmo para comprobacion de codigos tipo CIF
        $suma = $num[2] + $num[4] + $num[6];
        for ($i = 1; $i < 8; $i += 2){
          $suma += (int)substr((2 * $num[$i]),0,1) + (int)substr((2 * $num[$i]), 1, 1);
        }
        $n = 10 - substr($suma, strlen($suma) - 1, 1);
        // Comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
        if (preg_match('/^[KLM]{1}/', $cif)){
          if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1)){
            return true;
          }else{
            return false;
          }
        }
        // Comprobacion de CIFs
        if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)){
          if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)){
            return true;
          }else{
            return false;
          }
        }
        // Comprobacion de NIEs
        // T
        if (preg_match('/^[T]{1}/', $cif)){
          if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $cif)){
            return true;
          }else{
            return false;
          }
        }
        // XYZ
        if (preg_match('/^[XYZ]{1}/', $cif)){
          if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1)){
            return true;
          }else{
            return false;
          }
        }
        // Si todavía no se ha verificado devuelve error
        return false;
    }

}