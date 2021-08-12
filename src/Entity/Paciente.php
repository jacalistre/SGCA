<?php

namespace App\Entity;

use App\Repository\PacienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PacienteRepository::class)
 *
 */
class Paciente
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
     * @ORM\Column(type="string", length=255)
     */
    private $apellidos;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $carnet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pasaporte;

    /**
     * @ORM\Column(type="float")
     */
    private $edad;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sexo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccion_ci;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion_res;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $epidemiologia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sintomatologia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riesgo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $transportable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vacuna;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dosis;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hta;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dm;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $epoc;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ab;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $obeso;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ci;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vih;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $trastornos;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inmunodeprimido;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cancer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $otros;

    /**
     * @ORM\Column(type="integer")
     */
    private $fc;

    /**
     * @ORM\Column(type="integer")
     */
    private $fr;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $ta;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $saturacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="pacientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    /**
     * @ORM\ManyToOne(targetEntity=AreaSalud::class, inversedBy="pacientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity=Ingreso::class, mappedBy="paciente",cascade={"persist"})
     */
    private $ingresos;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="pacientes",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $transporte_sanitario;

    /**
     * @ORM\ManyToOne(targetEntity=Consultorio::class, inversedBy="pacientes")
     */
    private $consultorio;

    /**
     * @ORM\OneToMany(targetEntity=Prueba::class, mappedBy="paciente", orphanRemoval=true)
     */
    private $pruebas;

    /**
     * @ORM\OneToOne(targetEntity=Paciente::class, cascade={"persist", "remove"})
     */
    private $acompanante;

    public function __construct()
    {
        $this->ingresos = new ArrayCollection();
        $this->pruebas = new ArrayCollection();
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getCarnet(): ?string
    {
        return $this->carnet;
    }

    public function setCarnet(?string $carnet): self
    {
        $this->carnet = $carnet;

        return $this;
    }

    public function getPasaporte(): ?string
    {
        return $this->pasaporte;
    }

    public function setPasaporte(?string $pasaporte): self
    {
        $this->pasaporte = $pasaporte;

        return $this;
    }

    public function getEdad(): ?float
    {
        return $this->edad;
    }

    public function setEdad(?float $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getSexo(): ?bool
    {
        return $this->sexo;
    }

    public function setSexo(?bool $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDireccionCi(): ?string
    {
        return $this->direccion_ci;
    }

    public function setDireccionCi(?string $direccion_ci): self
    {
        $this->direccion_ci = $direccion_ci;

        return $this;
    }

    public function getDireccionRes(): ?string
    {
        return $this->direccion_res;
    }

    public function setDireccionRes(?string $direccion_res): self
    {
        $this->direccion_res = $direccion_res;

        return $this;
    }

    public function getEpidemiologia(): ?string
    {
        return $this->epidemiologia;
    }

    public function setEpidemiologia(?string $epidemiologia): self
    {
        $this->epidemiologia = $epidemiologia;

        return $this;
    }

    public function getSintomatologia(): ?string
    {
        return $this->sintomatologia;
    }

    public function setSintomatologia(?string $sintomatologia): self
    {
        $this->sintomatologia = $sintomatologia;

        return $this;
    }

    public function getRiesgo(): ?string
    {
        return $this->riesgo;
    }

    public function setRiesgo(?string $riesgo): self
    {
        $this->riesgo = $riesgo;

        return $this;
    }

    public function getTransportable(): ?string
    {
        return $this->transportable;
    }

    public function setTransportable(?string $transportable): self
    {
        $this->transportable = $transportable;

        return $this;
    }

    public function getVacuna(): ?string
    {
        return $this->vacuna;
    }

    public function setVacuna(?string $vacuna): self
    {
        $this->vacuna = $vacuna;

        return $this;
    }

    public function getDosis(): ?string
    {
        return $this->dosis;
    }

    public function setDosis(?string $dosis): self
    {
        $this->dosis = $dosis;

        return $this;
    }

    public function getHta(): ?bool
    {
        return $this->hta;
    }

    public function setHta(?bool $hta): self
    {
        $this->hta = $hta;

        return $this;
    }

    public function getDm(): ?bool
    {
        return $this->dm;
    }

    public function setDm(?bool $dm): self
    {
        $this->dm = $dm;

        return $this;
    }

    public function getEpoc(): ?bool
    {
        return $this->epoc;
    }

    public function setEpoc(?bool $epoc): self
    {
        $this->epoc = $epoc;

        return $this;
    }

    public function getAb(): ?bool
    {
        return $this->ab;
    }

    public function setAb(?bool $ab): self
    {
        $this->ab = $ab;

        return $this;
    }

    public function getObeso(): ?bool
    {
        return $this->obeso;
    }

    public function setObeso(?bool $obeso): self
    {
        $this->obeso = $obeso;

        return $this;
    }

    public function getCi(): ?bool
    {
        return $this->ci;
    }

    public function setCi(?bool $ci): self
    {
        $this->ci = $ci;

        return $this;
    }

    public function getVih(): ?bool
    {
        return $this->vih;
    }

    public function setVih(?bool $vih): self
    {
        $this->vih = $vih;

        return $this;
    }

    public function getTrastornos(): ?bool
    {
        return $this->trastornos;
    }

    public function setTrastornos(?bool $trastornos): self
    {
        $this->trastornos = $trastornos;

        return $this;
    }

    public function getInmunodeprimido(): ?bool
    {
        return $this->inmunodeprimido;
    }

    public function setInmunodeprimido(?bool $inmunodeprimido): self
    {
        $this->inmunodeprimido = $inmunodeprimido;

        return $this;
    }

    public function getCancer(): ?bool
    {
        return $this->cancer;
    }

    public function setCancer(?bool $cancer): self
    {
        $this->cancer = $cancer;

        return $this;
    }

    public function getOtros(): ?bool
    {
        return $this->otros;
    }

    public function setOtros(?bool $otros): self
    {
        $this->otros = $otros;

        return $this;
    }

    public function getFc(): ?int
    {
        return $this->fc;
    }

    public function setFc(?int $fc): self
    {
        $this->fc = $fc;

        return $this;
    }

    public function getFr(): ?int
    {
        return $this->fr;
    }

    public function setFr(?int $fr): self
    {
        $this->fr = $fr;

        return $this;
    }

    public function getTa(): ?string
    {
        return $this->ta;
    }

    public function setTa(?string $ta): self
    {
        $this->ta = $ta;

        return $this;
    }

    public function getSaturacion(): ?int
    {
        return $this->saturacion;
    }

    public function setSaturacion(?int $saturacion): self
    {
        $this->saturacion = $saturacion;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

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
            $ingreso->setPaciente($this);
        }

        return $this;
    }

    public function removeIngreso(Ingreso $ingreso): self
    {
        if ($this->ingresos->removeElement($ingreso)) {
            // set the owning side to null (unless already changed)
            if ($ingreso->getPaciente() === $this) {
                $ingreso->setPaciente(null);
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

    public function getTransporteSanitario(): ?bool
    {
        return $this->transporte_sanitario;
    }

    public function setTransporteSanitario(?bool $transporte_sanitario): self
    {
        $this->transporte_sanitario = $transporte_sanitario;

        return $this;
    }

    public function getConsultorio(): ?Consultorio
    {
        return $this->consultorio;
    }

    public function setConsultorio(?Consultorio $consultorio): self
    {
        $this->consultorio = $consultorio;

        return $this;
    }

    public function __toString()
    {
        return $this->nombre . " " . $this->apellidos;
    }

    /**
     * @return Collection|Prueba[]
     */
    public function getPruebas(): Collection
    {
        return $this->pruebas;
    }

    public function addPrueba(Prueba $prueba): self
    {
        if (!$this->pruebas->contains($prueba)) {
            $this->pruebas[] = $prueba;
            $prueba->setPaciente($this);
        }

        return $this;
    }

    public function removePrueba(Prueba $prueba): self
    {
        if ($this->pruebas->removeElement($prueba)) {
            // set the owning side to null (unless already changed)
            if ($prueba->getPaciente() === $this) {
                $prueba->setPaciente(null);
            }
        }

        return $this;
    }

    public function getAcompanante(): ?self
    {
        return $this->acompanante;
    }

    public function setAcompanante(?self $acompanante): self
    {
        $this->acompanante = $acompanante;

        return $this;
    }
    public function isRemision(){
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Pendiente Remision") {
                return true;
            }
        }
        return false;
    }
    public function isPendienteHospital(){
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Pendiente Hospital") {
                return true;
            }
        }
        return false;
    }
    public function isPendienteIngreso()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Pendiente") {
                return true;
            }
        }
        return false;
    }

    public function isIngresado()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Ingresado") {
                return true;
            }
        }
        return false;
    }

    public function isAlta()
    {
        $c = 0;
        foreach ($this->ingresos as $i) {

            if ($i->getEstado() == "Alta") {
                $c++;
            }
        }
        return $c == $this->ingresos->count() && $c!=0 ;
    }

    public function isPendienteUbic()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Pendiente ubicacion") {
                return true;
            }
        }
        return false;
    }

    public function isFallecido()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Fallecido") {
                return true;
            }
        }
        return false;
    }

    public function getColorEstado()
    { $c=0;

        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Fallecido") {
                return "circulo-negro";
            }
            if ($i->getEstado() == "Pendiente ubicacion" ) {
                return "circulo-naranja";
            }
            if ($i->getEstado() == "Alta") {
             $c++;
            }
            if ($i->getEstado() == "Ingresado") {
                return "circulo-rojo";
            }
            if ($i->getEstado() == "Pendiente") {
                return "circulo-amarillo";
            }
            if ($i->getEstado() == "Pendiente Hospital") {
                return "circulo-gris";
            }
            if ($i->getEstado() == "Pendiente Remision") {
                return "circulo-azul";
            }

        }
        return ($c==$this->ingresos->count() && $this->ingresos->count()!=0)?"circulo-verde":"fa fa-heartbeat";
    }
    /**
     * @return Ingreso
     */
    public function getIngresoActual()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() != "Alta" && $i->getEstado() != "Fallecido") {
                return $i;
            }
        }
        return null;
    }

    public function getNonimalSex()
    {
        if ($this->sexo) {
            return "Masculino";
        }
        return "Femenino";
    }
    public function getNominalColor(){
        if($this->color=="B")
            return "Blanca";
        if($this->color=="N")
            return "Negra";

        return "Mestiza";
    }

    public function fueTransportado()
    {
        foreach ($this->ingresos as $i) {
            if ($i->getEstado() == "Alta" &&  $i->getFechaTransportado()==null) {
               return $i;
            }
        }
        return null;
    }


}
