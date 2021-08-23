<?php
namespace App\Controller;

use App\Controller\HelperController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RenderViewController
 * @package App\Controller
 *
 * @Route(path="/")
 */
class RenderViewController
{
    /** KernelInterface $appKernel */
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * @Route("getview", name="getview_post", methods={"POST"})
     * @param $request
     */
    public function renderView(Request $request): Response
    {

       $requestData = json_decode($request->getContent(), true);

       $viewFolder = $requestData['viewfolder'];
       $view = $requestData['view'];
       $datos = $requestData['entidad'];
       $paginacion = (!isset($requestData['paginacion']) ? false : $requestData['paginacion']);

        $vistaRenderizado = $this->appKernel->getProjectDir() . '/templates/';

        $htmlOutput = '';

        ob_start();
            include_once($vistaRenderizado . $viewFolder . "/" . $view . ".php");
            $htmlOutput = ob_get_contents();
        ob_end_clean();

        //  Si tiene paginación la incluimos
        if($paginacion)
        {
            ob_start();
                include_once($vistaRenderizado . "comunes/paginacion.php");
                $htmlOutput .= ob_get_contents();
            ob_end_clean();
        }


        return new Response($htmlOutput, Response::HTTP_OK);
    }

}