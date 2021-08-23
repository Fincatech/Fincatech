<?php
namespace App\Controller;

use App\Controller\SecurityController;
use App\Controller\EntityHelperController;
use App\Controller\HelperController;

use App\Entity\UsuarioRoles;

use App\Repository\ComunidadRepository;
use App\Repository\UsuariosRepository;
use App\Repository\UsuarioRolesRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Polyfill\Intl\Idn\Info;

/**
 * Class UsuarioController
 * @package App\Controller
 *
 * @Route(path="/")
 */
class UsuarioController extends AbstractController
{
    
    private $repositorio;
    private $respositorioRoles;
    private $cabecera = ['Access-Control-Allow-Origin' => '*'];
    private $serializer;

    public function __construct(UsuariosRepository $usuarioRepository, UsuarioRolesRepository $usuarioRolRepository, SerializerInterface $serializer)
    {
        $this->repositorio = $usuarioRepository;
        $this->respositorioRoles = $usuarioRolRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("usuario/{idusuario}", name="get_one_usuario", methods={"GET"})
     */
    // public function get($idusuario): JsonResponse
    // {
    //     $comunidad = $this->repositorio->findOneBy(['id' => $idusuario]);

    //     $data = [
    //         'id' => $comunidad->getId(),
    //         // 'codigo' => $comunidad->getCodigo(),
    //         'nombre' => $comunidad->getNombre(),
    //         'direccion' => $comunidad->getDireccion(),
    //         'localidad' => $comunidad->getLocalidad(),
    //         'idlocalidad' => $comunidad->getIdlocalidad(),
    //         'idprovincia' => $comunidad->getIdprovincia(),
    //         'codpostal' => $comunidad->getCodpostal(),
    //         // 'presidente' => $comunidad->getPresidente(),
    //         'telefono' => $comunidad->getTelefono(),
    //         'emailcontacto' => $comunidad->getEmailcontacto(),
    //         'cif' => $comunidad->getCif(),
    //         'created'  => $comunidad->getCreated(),
    //         'usercreateid'  => $comunidad->getUsercreate(),
    //         'estado' => $comunidad->getEstado()
    //     ];

    //     return new JsonResponse($data, Response::HTTP_OK);

    // }

    /**
     * @Route("usuarios", name="get_all_usuarios", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $data = [];
        $usuarios = $this->repositorio->findAll();

        if(is_null($usuarios) || count($usuarios) == 0)
        {
            return HelperController::construirMensajeRespuestaOK("No hay usuarios dados de alta en el sistema");
        }

        //$serializer = $this->get('serializer');
        $response = $this->serializer->serialize($usuarios,'json', ['groups' => ['usuarios']]);

        foreach($usuarios as $usuario)
        {
            $data[] = [
                'id' => $usuario->getId(),
                'nombre' => $usuario->getNombre(),
                'cif' => $usuario->getCif(),
                'direccion' => $usuario->getDireccion(),
                'localidad' => $usuario->getLocalidad(),
                'idlocalidad' => $usuario->getIdlocalidad(),
                'idprovincia' => $usuario->getIdprovincia(),
                'codpostal' => $usuario->getCodpostal(),
                'telefono' => $usuario->getTelefono(),
                'emailcontacto' => $usuario->getEmailcontacto(),
                'idrol' => $usuario->getIdrol(),
                'salt' => $usuario->getSalt(),
                'token' => $usuario->getToken(),
                'estado' => $usuario->getEstado(),
                'created'  => $usuario->getCreated(),
                'updated'  => $usuario->getUpdated(),
                'usercreateid'  => $usuario->getUsercreate(),
                'rol' => $this->getRol($usuario->getIdrol())
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK, $this->cabecera);

    }

    public function getRol(int $idrol)
    {
 
        $infoRol = $this->respositorioRoles->findOneBy([ "id" => $idrol ]);

        if(!$infoRol)
        {
            die('no rol asociado');
        }else{
            $rolInfo = [
                'id' => $infoRol->getId(),
                'rol' => $infoRol->getRol(),
            ];
            return $rolInfo;
            //return $infoRol;
        }

    }

}