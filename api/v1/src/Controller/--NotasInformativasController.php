<?php

namespace Fincatech\Controller;


use Fincatech\Model\NotasinformativasModel;

class NotasinformativasController extends FrontController{

    private $notasInformativasModel;
    public $NotasinformativasModel; 
    public $UsuarioController;

    public function __construct($params = null)
    {
        $this->InitModel('Notasinformativas', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        $idNotaInformativa = $this->NotasinformativasModel->Create($entidadPrincipal, $datos);
        $this->EnviarEmailNotaInformativa($idNotaInformativa['id'], false);
        return $idNotaInformativa;
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->NotasinformativasModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->NotasinformativasModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->NotasinformativasModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->NotasinformativasModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->NotasinformativasModel->List($params, false);
    }

    /** Envía un e-mail al master con la información de la comunidad que ha dado de baja */    
    public function EnviarEmailNotaInformativa($idNotaInformativa, $debug = false)
    {

        $notaInformativa = $this->Get($idNotaInformativa);

        $ficheroNotaInformativa = null;
        $ficheroCodificado = '#';
        $contenidoNota = $notaInformativa['notasinformativas'][0]['descripcion'];
        $tituloNota = $notaInformativa['notasinformativas'][0]['titulo'];

        if(isset($notaInformativa['notasinformativas'][0]['ficheroscomunes']))
        {
            if(count($notaInformativa['notasinformativas'][0]['ficheroscomunes']) > 0)
            {
                $ficheroNotaInformativa  = $notaInformativa['notasinformativas'][0]['ficheroscomunes'][0]['nombre'];
                $ficheroCodificado  = $notaInformativa['notasinformativas'][0]['ficheroscomunes'][0]['nombrestorage'];
            }

        }

        $filterParams = [];
        $filterParams['filterfield'] = 'estado';
        $filterParams['filtervalue'] = "'A' and rolid=5 ";
        $filterParams['filteroperator'] = '=';

        $this->InitController('Usuario');

        //$usuarios = $this->UsuarioController->List($filterParams);
        $usuarios = $this->UsuarioController->ListAdministradoresFincas();

        //  Recuperamos todos los e-mails de administradores que estén activos en el sistema
        if(count($usuarios['Usuario']) > 0)
        {

            $body = $this->GetTemplateEmailNotaInformativa();
            $emailsEnviados = 1;
            $numeroUsuarios = count($usuarios['Usuario']); 
            for($x = 0; $x < $numeroUsuarios; $x++)
            {
                $bodyToSend = $body;        

                $administrador = $this->UsuarioController->Get($usuarios['Usuario'][$x]['id']);
                $administradorNombre = $administrador['Usuario'][0]['nombre'];
                $emailAdministrador = $administrador['Usuario'][0]['email'];
                // $emailAdministrador = 'oscar@happysoftware.es';

                $bodyToSend = str_replace('[@administrador@]', $administradorNombre, $bodyToSend);
                $bodyToSend = str_replace('[@notainformativa@]', $ficheroCodificado, $bodyToSend);
                $bodyToSend = str_replace('[@nombre_notainformativa@]', $ficheroNotaInformativa, $bodyToSend);
                $bodyToSend = str_replace('[@contenido_notainformativa@]', $contenidoNota, $bodyToSend);

                //  Si no estamos en modo debug enviamos el e-mail
                if(!$debug){
                    $this->SendEmail($emailAdministrador, $administradorNombre, 'Fincatech - Nueva Nota Informativa disponible [ '.$tituloNota.' ]', $bodyToSend, true);
                }else{
                    echo $administradorNombre . ' - ' . $emailAdministrador . '<br>';
                }
                //  Para evitar que pueda parar el servidor el envío, le metemos 3 segundos de delay cada 3 envíos
                // if($emailsEnviados == 3)
                // {
                //     $emailsEnviados = 0;
                //     sleep(3);
                // }else{
                //     $emailsEnviados++;
                // }
            }


        }else{
            return;
        }

    }
    
    /** Recupera el template de nota informativa */
    private function GetTemplateEmailNotaInformativa(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/nota_informativa.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

}