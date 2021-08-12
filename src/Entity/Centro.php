<?php

namespace App\Entity;

use App\Repository\CentroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CentroRepository::class)
 * @UniqueEntity("nombre",message="Ya existe un centro con ese nombre")
 */
class Centro
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
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="centros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    /**
     * @ORM\OneToMany(targetEntity=Ingreso::class, mappedBy="centro", orphanRemoval=true)
     */
    private $ingresos;

    /**
     * @ORM\OneToMany(targetEntity=Sala::class, mappedBy="centro", orphanRemoval=true)
     */
    private $salas;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="centro")
     */
    private $usuarios;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="centros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\OneToMany(targetEntity=Cama::class, mappedBy="centro", orphanRemoval=true)
     */
    private $camas;

    public function __construct()
    {
        $this->ingresos = new ArrayCollection();
        $this->salas = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->camas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
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
            $ingreso->setCentro($this);
        }

        return $this;
    }

    public function removeIngreso(Ingreso $ingreso): self
    {
        if ($this->ingresos->removeElement($ingreso)) {
            // set the owning side to null (unless already changed)
            if ($ingreso->getCentro() === $this) {
                $ingreso->setCentro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sala[]
     */
    public function getSalas(): Collection
    {
        return $this->salas;
    }

    public function addSala(Sala $sala): self
    {
        if (!$this->salas->contains($sala)) {
            $this->salas[] = $sala;
            $sala->setCentro($this);
        }

        return $this;
    }

    public function removeSala(Sala $sala): self
    {
        if ($this->salas->removeElement($sala)) {
            // set the owning side to null (unless already changed)
            if ($sala->getCentro() === $this) {
                $sala->setCentro(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setCentro($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getCentro() === $this) {
                $usuario->setCentro(null);
            }
        }

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getCantCamas(){
        $total=0;
        foreach ($this->salas as $s){
            $c=$s->getCamas();
            $total+=$c!=null?$c->count():0;
        }
        return $total;
    }

    public function __toString()
    {
      return $this->nombre;

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
            $cama->setCentro($this);
        }

        return $this;
    }

    public function removeCama(Cama $cama): self
    {
        if ($this->camas->removeElement($cama)) {
            // set the owning side to null (unless already changed)
            if ($cama->getCentro() === $this) {
                $cama->setCentro(null);
            }
        }

        return $this;
    }
    public function SalaJson(){
        $arr=[];
        foreach ($this->salas as $s){
          $arr[]=["nombre"=>$s->getNombre(),"id"=>$s->getId()];

        }

        return $arr;
    }





}
