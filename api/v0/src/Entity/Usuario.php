<?php

namespace App\Entity;

use App\Controller\HelperController;

use App\Entity\EntityHelper;
use App\Entity\Traits\HelperTrait;

use App\Repository\UsuariosRepository;
use App\Repository\UsuarioRolesRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass=UsuariosRepository::class)
 */
class Usuario extends EntityHelper
{

    use Traits\HelperTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $cif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailcontacto;

    /**
     * @ORM\Column(type="integer")
     */
    private $idrol;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $estado;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Comunidad", mappedBy="usuario")
     */
    private $comunidades;

    public function __construct(UsuarioRolesRepository $roles)
    {
        $this->comunidades = new ArrayCollection();
        $this->rol = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCif(): ?string
    {
        return $this->cif;
    }

    public function setCif(string $cif): self
    {
        $this->cif = $cif;

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

    public function setEmailcontacto(string $emailcontacto): self
    {
        $this->emailcontacto = $emailcontacto;

        return $this;
    }

    public function getIdrol(): ?int
    {
        return $this->idrol;
    }

    public function setIdrol(int $idrol): self
    {
        $this->idrol = $idrol;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

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

    public function setUpdated(?\DateTimeInterface $updated = null): self
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

}
