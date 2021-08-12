<?php

namespace App\Entity;

use App\Repository\ConfiguracionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfiguracionRepository::class)
 */
class Configuracion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rotacion_evolutivo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRotacionEvolutivo(): ?int
    {
        return $this->rotacion_evolutivo;
    }

    public function setRotacionEvolutivo(int $rotacion_evolutivo): self
    {
        $this->rotacion_evolutivo = $rotacion_evolutivo;

        return $this;
    }
}
