<?php

namespace App\Entity;

use App\Controller\HelperController;

use App\Entity\EntityHelper;
use App\Entity\Usuario;
use App\Entity\Traits\HelperTrait;

use App\Repository\ComunidadRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="comunidades")
 * @ORM\Entity(repositoryClass="App\Repository\ComunidadRepository")
 */
class Comunidad extends EntityHelper
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $localidad;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idlocalidad;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idprovincia;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codpostal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $presidente;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailcontacto;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $cif;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="integer")
     */
    private $usercreate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userupdate;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="comunidades")
     * @ORM\JoinColumn(name="administrador_id", referencedColumnName="id")
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(?string $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getIdlocalidad(): ?int
    {
        return $this->idlocalidad;
    }

    public function setIdlocalidad(?int $idlocalidad): self
    {
        $this->idlocalidad = $idlocalidad;

        return $this;
    }

    public function getIdprovincia(): ?int
    {
        return $this->idprovincia;
    }

    public function setIdprovincia(?int $idprovincia): self
    {
        $this->idprovincia = $idprovincia;

        return $this;
    }

    public function getCodpostal(): ?string
    {
        return $this->codpostal;
    }

    public function setCodpostal(?string $codpostal): self
    {
        $this->codpostal = $codpostal;

        return $this;
    }

    public function getPresidente(): ?string
    {
        return $this->presidente;
    }

    public function setPresidente(?string $presidente): self
    {
        $this->presidente = $presidente;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getEmailcontacto(): ?string
    {
        return $this->emailcontacto;
    }

    public function setEmailcontacto(?string $emailcontacto): self
    {
        $this->emailcontacto = $emailcontacto;

        return $this;
    }

    public function getCif(): ?string
    {
        return $this->cif;
    }

    public function setCif(?string $cif): self
    {
        $this->cif = $cif;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created = null): self
    {
        $this->created = (is_null($created) ? HelperTrait::getFechaActual() : $created );
        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated = null): self
    {
        $this->updated = (is_null($updated) ? HelperTrait::getFechaActual() : $updated );
        return $this;
    }

    public function getUsercreate(): ?int
    {
        return $this->usercreate;
    }

    public function setUsercreate(?int $idusuario): self
    {
        $this->usercreate = (is_null($idusuario) ? HelperController::getIdUsuarioActual() : $idusuario);
        return $this;
    }

    public function getUserupdate(): ?int
    {
        return $this->userupdate;
    }

    public function setUserupdate(?int $idusuario): self
    {
        $this->userupdate = (is_null($idusuario) ? HelperController::getIdUsuarioActual() : $idusuario);
        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getDocumentosGenerales(int $comunidadId)
    {
        //  Recuperamos los documentos principales de la comunidad y su estado

    }

    /**
     * @return Usuario
     */
    public function getAdministrador(): ?Usuario
    {
        return $this->usuario;
    }

    public function setAdministrador(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

}
