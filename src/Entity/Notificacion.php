<?php

namespace App\Entity;

use App\Repository\NotificacionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificacionRepository::class)
 */
class Notificacion
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
    private $mensaje;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="notificacions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $origen;

    /**
     * @ORM\ManyToOne(targetEntity=Paciente::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $paciente;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_envio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_leido;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destino;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getOrigen(): ?Usuario
    {
        return $this->origen;
    }

    public function setOrigen(?Usuario $origen): self
    {
        $this->origen = $origen;

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

    public function getFechaEnvio(): ?\DateTimeInterface
    {
        return $this->fecha_envio;
    }

    public function setFechaEnvio(\DateTimeInterface $fecha_envio): self
    {
        $this->fecha_envio = $fecha_envio;

        return $this;
    }

    public function getFechaLeido(): ?\DateTimeInterface
    {
        return $this->fecha_leido;
    }

    public function setFechaLeido(?\DateTimeInterface $fecha_leido): self
    {
        $this->fecha_leido = $fecha_leido;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): self
    {
        $this->destino = $destino;

        return $this;
    }

    public function toArray(){
        return ["id"=>$this->id,"mensaje"=>$this->mensaje,"origen"=>$this->origen->getDataforMessage(),"fecha"=>$this->fecha_envio,"ingreso"=>($this->getPaciente()->getIngresoActual()==null?null:$this->getPaciente()->getIngresoActual()->getId()),"paciente"=>$this->getPaciente()->getNombre()." ".$this->getPaciente()->getApellidos()];
    }
}
