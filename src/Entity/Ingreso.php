<?php

namespace App\Entity;

use App\Repository\IngresoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngresoRepository::class)
 */
class Ingreso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_entrada;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_alta;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_confirmacion;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="ingresos")
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Centro::class, inversedBy="ingresos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $centro;

    /**
     * @ORM\OneToMany(targetEntity=Prueba::class, mappedBy="ingreso", orphanRemoval=true,cascade={"persist"})
     */
    private $pruebas;

    /**
     * @ORM\ManyToOne(targetEntity=Paciente::class, inversedBy="ingresos",cascade={"persist"})
     */
    private $paciente;



    /**
     * @ORM\ManyToOne(targetEntity=Sala::class, inversedBy="ingresos")
     */
    private $sala;

    /**
     * @ORM\ManyToOne(targetEntity=Cama::class)
     */
    private $cama;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_transportado;

    public function __construct()
    {
        $this->pruebas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaEntrada(): ?\DateTimeInterface
    {
        return $this->fecha_entrada;
    }

    public function setFechaEntrada(?\DateTimeInterface $fecha_entrada): self
    {
        $this->fecha_entrada = $fecha_entrada;

        return $this;
    }

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fecha_alta;
    }

    public function setFechaAlta(?\DateTimeInterface $fecha_alta): self
    {
        $this->fecha_alta = $fecha_alta;

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

    public function getFechaConfirmacion(): ?\DateTimeInterface
    {
        return $this->fecha_confirmacion;
    }

    public function setFechaConfirmacion(\DateTimeInterface $fecha_confirmacion): self
    {
        $this->fecha_confirmacion = $fecha_confirmacion;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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
            $prueba->setIngreso($this);
        }

        return $this;
    }

    public function removePrueba(Prueba $prueba): self
    {
        if ($this->pruebas->removeElement($prueba)) {
            // set the owning side to null (unless already changed)
            if ($prueba->getIngreso() === $this) {
                $prueba->setIngreso(null);
            }
        }

        return $this;
    }

    public function getPaciente(): ?Paciente
    {
        return $this->paciente;
    }

    public function setPaciente(?Paciente $paciente): self
    {
        $this->paciente = $paciente;

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

    public function getCama(): ?Cama
    {
        return $this->cama;
    }

    public function setCama(?Cama $cama): self
    {
        $this->cama = $cama;

        return $this;
    }

    public function getFechaTransportado(): ?\DateTimeInterface
    {
        return $this->fecha_transportado;
    }

    public function setFechaTransportado(?\DateTimeInterface $fecha_transportado): self
    {
        $this->fecha_transportado = $fecha_transportado;

        return $this;
    }

}
