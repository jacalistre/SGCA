<?php

namespace App\Entity;

use App\Repository\ProvinciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProvinciaRepository::class)
 */
class Provincia
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
     * @ORM\OneToMany(targetEntity=Municipio::class, mappedBy="provincia")
     */
    private $municipios;

    /**
     * @ORM\OneToMany(targetEntity=Paciente::class, mappedBy="provincia")
     */
    private $pacientes;

    /**
     * @ORM\OneToMany(targetEntity=Centro::class, mappedBy="provincia", orphanRemoval=true)
     */
    private $centros;

    /**
     * @ORM\OneToMany(targetEntity=AreaSalud::class, mappedBy="provincia", orphanRemoval=true)
     */
    private $areaSaluds;

    /**
     * @ORM\OneToMany(targetEntity=Consultorio::class, mappedBy="provincia", orphanRemoval=true)
     */
    private $consultorios;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="provincia")
     */
    private $usuarios;

    public function __construct()
    {
        $this->municipios = new ArrayCollection();
        $this->pacientes = new ArrayCollection();
        $this->centros = new ArrayCollection();
        $this->areaSaluds = new ArrayCollection();
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

    /**
     * @return Collection|Municipio[]
     */
    public function getMunicipios(): Collection
    {
        return $this->municipios;
    }

    public function addMunicipio(Municipio $municipio): self
    {
        if (!$this->municipios->contains($municipio)) {
            $this->municipios[] = $municipio;
            $municipio->setProvincia($this);
        }

        return $this;
    }

    public function removeMunicipio(Municipio $municipio): self
    {
        if ($this->municipios->removeElement($municipio)) {
            // set the owning side to null (unless already changed)
            if ($municipio->getProvincia() === $this) {
                $municipio->setProvincia(null);
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
            $paciente->setProvincia($this);
        }

        return $this;
    }

    public function removePaciente(Paciente $paciente): self
    {
        if ($this->pacientes->removeElement($paciente)) {
            // set the owning side to null (unless already changed)
            if ($paciente->getProvincia() === $this) {
                $paciente->setProvincia(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
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
            $centro->setProvincia($this);
        }

        return $this;
    }

    public function removeCentro(Centro $centro): self
    {
        if ($this->centros->removeElement($centro)) {
            // set the owning side to null (unless already changed)
            if ($centro->getProvincia() === $this) {
                $centro->setProvincia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AreaSalud[]
     */
    public function getAreaSaluds(): Collection
    {
        return $this->areaSaluds;
    }

    public function addAreaSalud(AreaSalud $areaSalud): self
    {
        if (!$this->areaSaluds->contains($areaSalud)) {
            $this->areaSaluds[] = $areaSalud;
            $areaSalud->setProvincia($this);
        }

        return $this;
    }

    public function removeAreaSalud(AreaSalud $areaSalud): self
    {
        if ($this->areaSaluds->removeElement($areaSalud)) {
            // set the owning side to null (unless already changed)
            if ($areaSalud->getProvincia() === $this) {
                $areaSalud->setProvincia(null);
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
            $consultorio->setProvincia($this);
        }

        return $this;
    }

    public function removeConsultorio(Consultorio $consultorio): self
    {
        if ($this->consultorios->removeElement($consultorio)) {
            // set the owning side to null (unless already changed)
            if ($consultorio->getProvincia() === $this) {
                $consultorio->setProvincia(null);
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
            $usuario->setProvincia($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getProvincia() === $this) {
                $usuario->setProvincia(null);
            }
        }

        return $this;
    }

    public function MunicipiosJson(){
        $arr=[];
        foreach ($this->municipios as $s){
            $arr[]=["nombre"=>$s->getNombre(),"id"=>$s->getId()];

        }

        return $arr;
    }


}
