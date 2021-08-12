<?php

namespace App\Entity;

use App\Repository\PruebaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PruebaRepository::class)
 */
class Prueba
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)false
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $resultado;

    /**
     * @ORM\ManyToOne(targetEntity=Ingreso::class, inversedBy="pruebas",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $ingreso;

    /**
     * @ORM\ManyToOne(targetEntity=Paciente::class, inversedBy="pruebas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paciente;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_notificacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getResultado(): ?string
    {
        return $this->resultado;
    }

    public function setResultado(string $resultado): self
    {
        $this->resultado = $resultado;

        return $this;
    }

    public function getIngreso(): ?Ingreso
    {
        return $this->ingreso;
    }

    public function setIngreso(?Ingreso $ingreso): self
    {
        $this->ingreso = $ingreso;

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

    public function getFechaNotificacion(): ?\DateTimeInterface
    {
        return $this->fecha_notificacion;
    }

    public function setFechaNotificacion(?\DateTimeInterface $fecha_notificacion): self
    {
        $this->fecha_notificacion = $fecha_notificacion;

        return $this;
    }
}
