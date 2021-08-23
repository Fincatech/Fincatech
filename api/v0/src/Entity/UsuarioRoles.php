<?php

namespace App\Entity;

use App\Controller\HelperController;

use App\Entity\Traits\HelperTrait;

use App\Repository\UsuarioRolesRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsuarioRolesRepository::class)
 */
class UsuarioRoles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $rol;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $usercreate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setROL(string $rol): self
    {
        $this->rol = $rol;

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
