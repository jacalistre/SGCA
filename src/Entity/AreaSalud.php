<?php

namespace App\Entity;

use App\Repository\AreaSaludRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AreaSaludRepository::class)
 * @UniqueEntity("nombre",message="Ya existe un area con ese nombre")
 */
class AreaSalud
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
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="areaSaluds")
     */
    private $municipio;

    /**
     * @ORM\OneToMany(targetEntity=Paciente::class, mappedBy="area")
     */
    private $pacientes;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="area")
     */
    private $usuarios;

    /**
     * @ORM\OneToMany(targetEntity=Consultorio::class, mappedBy="area")
     */
    private $consultorios;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="areaSaluds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    public function __construct()
    {
        $this->pacientes = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->consultorios = new ArrayCollection();
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
     * @return Collection|Paciente[]
     */
    public function getPacientes(): Collection
    {
        return $this->pacientes;
    }

    public function addPaciente(Paciente $paciente): self
    {
        if (!$this->pacientes->contains($paciente)) {
            $this->pacientes[] = $paciente;
            $paciente->setArea($this);
        }

        return $this;
    }

    public function removePaciente(Paciente $paciente): self
    {
        if ($this->pacientes->removeElement($paciente)) {
            // set the owning side to null (unless already changed)
            if ($paciente->getArea() === $this) {
                $paciente->setArea(null);
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
            $usuario->setArea($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getArea() === $this) {
                $usuario->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Consultorio[]
     */
    public function getConsultorios(): Collection
    {
        return $this->consultorios;
    }

    public function addConsultorio(Consultorio $consultorio): self
    {
        if (!$this->consultorios->contains($consultorio)) {
            $this->consultorios[] = $consultorio;
            $consultorio->setArea($this);
        }

        return $this;
    }

    public function removeConsultorio(Consultorio $consultorio): self
    {
        if ($this->consultorios->removeElement($consultorio)) {
            // set the owning side to null (unless already changed)
            if ($consultorio->getArea() === $this) {
                $consultorio->setArea(null);
            }
        }

        return $this;
    }

    public function __toString()
    {return $this->nombre.($this->descripcion!=null && $this->descripcion!=""?"(".$this->descripcion.")":"");
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
    public function ConsultorioJson(){
        $arr=[];
        foreach ($this->consultorios as $s){
            $arr[]=["nombre"=>$s->getNombre(),"id"=>$s->getId()];

        }

        return $arr;
    }

}
