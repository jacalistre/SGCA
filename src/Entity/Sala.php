<?php

namespace App\Entity;

use App\Repository\SalaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalaRepository::class)
 */
class Sala
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=Cama::class, mappedBy="sala", orphanRemoval=true)
     */
    private $camas;

    /**
     * @ORM\ManyToOne(targetEntity=Centro::class, inversedBy="salas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $centro;

    /**
     * @ORM\OneToMany(targetEntity=Ingreso::class, mappedBy="sala")
     */
    private $ingresos;

    public function __construct()
    {
        $this->camas = new ArrayCollection();
        $this->ingresos = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection|Cama[]
     */
    public function getCamas(): Collection
    {
        return $this->camas;
    }

    public function addCama(Cama $cama): self
    {
        if (!$this->camas->contains($cama)) {
            $this->camas[] = $cama;
            $cama->setSala($this);
        }

        return $this;
    }

    public function removeCama(Cama $cama): self
    {
        if ($this->camas->removeElement($cama)) {
            // set the owning side to null (unless already changed)
            if ($cama->getSala() === $this) {
                $cama->setSala(null);
            }
        }

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

    public function __toString()
    {
return $this->nombre;
    }

    /**
     * @return Collection|Ingreso[]
     */
    public function getIngresos(): Collection
    {
        return $this->ingresos;
    }

    public function addIngreso(Ingreso $ingreso): self
    {
        if (!$this->ingresos->contains($ingreso)) {
            $this->ingresos[] = $ingreso;
            $ingreso->setSala($this);
        }

        return $this;
    }

    public function removeIngreso(Ingreso $ingreso): self
    {
        if ($this->ingresos->removeElement($ingreso)) {
            // set the owning side to null (unless already changed)
            if ($ingreso->getSala() === $this) {
                $ingreso->setSala(null);
            }
        }

        return $this;
    }
    public function CamaJson(){
        $arr=[];
        foreach ($this->camas as $s){
            if($s->getEstado()->getTipo()!="Bloqueo") {
                $arr[] = ["nombre" => $s->getNumero(), "id" => $s->getId()];
            }
        }

        return $arr;
    }
    public function CamasSinOcupar(){
        $arr=[];
        foreach ($this->camas as $s){
            if($s->getEstado()->getTipo()!="Bloqueo") {
                $arr[] = $s;
            }
        }
        return $arr;
    }


}
