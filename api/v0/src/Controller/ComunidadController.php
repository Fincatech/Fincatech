<?php
namespace App\Controller;

use App\Controller\EntityHelperController;
use App\Controller\HelperController;
use App\Entity\Usuarios;
use App\Repository\ComunidadRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ComunidadController
 * @package App\Controller
 *
 * @Route(path="/")
 */
class ComunidadController
{
    private $comunidadRepository;
    private $cabecera = ['Access-Control-Allow-Origin' => '*'];

    public function __construct(ComunidadRepository $comunidadRepository)
    {
        $this->comunidadRepository = $comunidadRepository;
    }

    /**
     * @Route("comunidad", name="add_comunidad", methods={"POST"})
     */
    public function insert(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        //  Recorremos el json para mapearlo y lanzar el update

        //$nombre = $data['nombre'];

        if (empty($data['codigo'])) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        //  Guardamos la comunidad
        $this->comunidadRepository->save($data);

        return new JsonResponse(['status' => 'La comunidad se ha creado correctamente'], Response::HTTP_CREATED);
    }

    /**
     * @Route("comunidad/{idcomunidad}", name="update_comunidad", methods={"PUT"})
     */
    public function update($idcomunidad, Request $request): JsonResponse
    {
        $comunidad = $this->comunidadRepository->findOneBy(['id' => $idcomunidad]);
        $data = json_decode($request->getContent(), true);

        $respuesta = null;

        //  Si no se ha encontrado
        if(is_null($comunidad))
        {
            $respuesta = HelperController::construirMensajeRespuestaError("La comunidad no se ha encontrado");
        }else{
            $respuesta = HelperController::construirMensajeRespuestaOK("Los datos de la comunidad se han actualizado correctamente");
        }

        $updatedComunidad = $this->comunidadRepository->update($comunidad);

        return $respuesta; //new JsonResponse(['status' => 'Los datos de la comunidad se han actualizado correctamente'], Response::HTTP_OK);
    }

    /**
     * @Route("comunidad/{idcomunidad}", name="delete_comunidad", methods={"DELETE"})
     */
    public function delete($idcomunidad): JsonResponse
    {
        $comunidad = $this->comunidadRepository->findOneBy(['id' => $idcomunidad]);

        if(is_null($comunidad))
        {
            $respuesta = HelperController::construirMensajeRespuestaError("No se ha encontrado la comunidad");
        }else{
            $this->comunidadRepository->remove($comunidad);
            $respuesta = HelperController::construirMensajeRespuestaOK("La comunidad y sus datos asociados ha sido eliminada correctamente");
        }
        
        return $respuesta;

    }

    /**
     * @Route("comunidad/{idcomunidad}", name="get_one_comunidad", methods={"GET"})
     */
    public function get($idcomunidad): JsonResponse
    {

        $comunidad = $this->comunidadRepository->findOneBy(['id' => $idcomunidad]);

        $data = [
            'id' => $comunidad->getId(),
            'codigo' => $comunidad->getCodigo(),
            'nombre' => $comunidad->getNombre(),
            'direccion' => $comunidad->getDireccion(),
            'localidad' => $comunidad->getLocalidad(),
            'idlocalidad' => $comunidad->getIdlocalidad(),
            'idprovincia' => $comunidad->getIdprovincia(),
            'codpostal' => $comunidad->getCodpostal(),
            'presidente' => $comunidad->getPresidente(),
            'telefono' => $comunidad->getTelefono(),
            'emailcontacto' => $comunidad->getEmailcontacto(),
            'cif' => $comunidad->getCif(),
            'created'  => $comunidad->getCreated(),
            'usercreateid'  => $comunidad->getUsercreate(),
            'estado' => $comunidad->getEstado(),
            'administrador' => $comunidad->getSerializedEntity($comunidad->getAdministrador()),
            'documentos' => $this->getEstadisticasDocumentos()
        ];

        return new JsonResponse($data, Response::HTTP_OK);

    }

    /**
     * @Route("comunidades", name="get_all_comunidades", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $comunidades = $this->comunidadRepository->findAll();
        $data = [];
        
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];        
        $serializer = new Serializer($normalizers, $encoders);
        
        if(is_null($comunidades) || count($comunidades) == 0)
        {
            return HelperController::construirMensajeRespuestaOK("No hay comunidades insertadas");
        }

        foreach($comunidades as $comunidad)
        {
            $data[] = [
                'id' => $comunidad->getId(),
                'codigo' => $comunidad->getCodigo(),
                'nombre' => $comunidad->getNombre(),
                'direccion' => $comunidad->getDireccion(),
                'localidad' => $comunidad->getLocalidad(),
                'idlocalidad' => $comunidad->getIdlocalidad(),
                'idprovincia' => $comunidad->getIdprovincia(),
                'codpostal' => $comunidad->getCodpostal(),
                'presidente' => $comunidad->getPresidente(),
                'telefono' => $comunidad->getTelefono(),
                'emailcontacto' => $comunidad->getEmailcontacto(),
                'cif' => $comunidad->getCif(),
                'created'  => $comunidad->getCreated(),
                'usercreateid'  => $comunidad->getUsercreate(),
                'estado' => $comunidad->getEstado(),
                'administrador' => $comunidad->getSerializedEntity($comunidad->getAdministrador()),
                'documentos' => $this->getEstadisticasDocumentos()
            ];
        }

        return HelperController::construirMensajeRespuestaDatos($data);

    }

    /**
     * Obtiene la información de los documentos pendientes
     */
    private function getEstadisticasDocumentos()
    {

    }

}