<?php

namespace App\Entity;

use App\Repository\ConsultorioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConsultorioRepository::class)
 *
 */
class Consultorio
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
     * @ORM\ManyToOne(targetEntity=AreaSalud::class, inversedBy="consultorios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity=Paciente::class, mappedBy="consultorio")
     */
    private $pacientes;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="consultorios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="consultorios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    public function __construct()
    {
        $this->pacientes = new ArrayCollection();
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

    public function getArea(): ?AreaSalud
    {
        return $this->area;
    }

    public function setArea(?AreaSalud $area): self
    {
        $this->area = $area;

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
            $paciente->setConsultorio($this);
        }

        return $this;
    }

    public function removePaciente(Paciente $paciente): self
    {
        if ($this->pacientes->removeElement($paciente)) {
            // set the owning side to null (unless already changed)
            if ($paciente->getConsultorio() === $this) {
                $paciente->setConsultorio(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
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

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

}
