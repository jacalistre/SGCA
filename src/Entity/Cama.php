<?php

namespace App\Entity;

use App\Repository\CamaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CamaRepository::class)
 *
 */
class Cama
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;


    /**
     * @ORM\ManyToOne(targetEntity=Sala::class, inversedBy="camas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala;



    /**
     * @ORM\ManyToOne(targetEntity=Centro::class, inversedBy="camas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $centro;

    /**
     * @ORM\ManyToOne(targetEntity=EstadoCama::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $estado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    { $this->numero = $numero;

        return $this;
    }


    public function getSala(): ?Sala
    {
        return $this->sala;
    }

    public function setSala(?Sala $sala): self
    {
        $this->sala = $sala;

        return $this;
    }



    public function getCentro(): ?Centro
    {
        return $this->centro;
    }

    public function setCentro(?Centro $centro): self
    {
        $this->centro = $centro;

        return $this;
    }

    public function getEstado(): ?EstadoCama
    {
        return $this->estado;
    }

    public function setEstado(?EstadoCama $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function __toString()
    {
        return $this->numero."";
    }

}
