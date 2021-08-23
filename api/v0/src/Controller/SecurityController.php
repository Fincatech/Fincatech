<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("checklogin", name="checklogin")
     */
    public function checkLogin()
    {
        //  Comprobamos si el usuario
    }

    //  Comprueba que el usuario esté autenticado
    public function usuarioAutenticado()
    {

    }

    // /**
    //  * @Route("/security", name="security")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('security/index.html.twig', [
    //         'controller_name' => 'SecurityController',
    //     ]);
    // }
}
