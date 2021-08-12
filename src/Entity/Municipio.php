<?php

namespace App\Entity;

use App\Repository\MunicipioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MunicipioRepository::class)
 */
class Municipio
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
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="municipios")
     */
    private $provincia;

    /**
     * @ORM\OneToMany(targetEntity=AreaSalud::class, mappedBy="municipio")
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity=Paciente::class, mappedBy="municipio")
     */
    private $pacientes;

    /**
     * @ORM\OneToMany(targetEntity=Centro::class, mappedBy="municipio")
     */
    private $centros;

    /**
     * @ORM\OneToMany(targetEntity=Consultorio::class, mappedBy="municipio", orphanRemoval=true)
     */
    private $consultorios;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="municipio")
     */
    private $usuarios;

    public function __construct()
    {
        $this->area = new ArrayCollection();
        $this->pacientes = new ArrayCollection();
        $this->centros = new ArrayCollection();
        $this->consultorios = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * @return Collection|AreaSalud[]
     */
    public function getArea(): Collection
    {
        return $this->area;
    }

    public function addArea(AreaSalud $area): self
    {
        if (!$this->area->contains($area)) {
            $this->area[] = $area;
            $area->setMunicipio($this);
        }

        return $this;
    }

    public function removeArea(AreaSalud $area): self
    {
        if ($this->area->removeElement($area)) {
            // set the owning side to null (unless already changed)
            if ($area->getMunicipio() === $this) {
                $area->setMunicipio(null);
            }
        }

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
            $paciente->setMunicipio($this);
        }

        return $this;
    }

    public function removePaciente(Paciente $paciente): self
    {
        if ($this->pacientes->removeElement($paciente)) {
            // set the owning side to null (unless already changed)
            if ($paciente->getMunicipio() === $this) {
                $paciente->setMunicipio(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Centro[]
     */
    public function getCentros(): Collection
    {
        return $this->centros;
    }

    public function addCentro(Centro $centro): self
    {
        if (!$this->centros->contains($centro)) {
            $this->centros[] = $centro;
            $centro->setMunicipio($this);
        }

        return $this;
    }

    public function removeCentro(Centro $centro): self
    {
        if ($this->centros->removeElement($centro)) {
            // set the owning side to null (unless already changed)
            if ($centro->getMunicipio() === $this) {
                $centro->setMunicipio(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
       return $this->nombre;
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
            $consultorio->setMunicipio($this);
        }

        return $this;
    }

    public function removeConsultorio(Consultorio $consultorio): self
    {
        if ($this->consultorios->removeElement($consultorio)) {
            // set the owning side to null (unless already changed)
            if ($consultorio->getMunicipio() === $this) {
                $consultorio->setMunicipio(null);
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
            $usuario->setMunicipio($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getMunicipio() === $this) {
                $usuario->setMunicipio(null);
            }
        }

        return $this;
    }
    public function CentroJson(){
        $arr=[];
        foreach ($this->centros as $s){
            $arr[]=["nombre"=>$s->getNombre(),"id"=>$s->getId()];

        }

        return $arr;
    }

    public function AreasJson(){
        $arr=[];

        foreach ($this->area as $s){
            $arr[]=["nombre"=>$s->getNombre(),"id"=>$s->getId()];

        }

        return $arr;
    }
}
