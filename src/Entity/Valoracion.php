<?php

namespace App\Entity;

use App\Repository\ValoracionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ValoracionRepository::class)
 */
class Valoracion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nota;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comentario;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="valoraciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Spot::class, inversedBy="valoraciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $spot;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * Valoracion constructor.
     * @param $nota
     * @param $comentario
     * @param $usuario
     * @param $spot
     */
    public function __construct($nota = null, $comentario = null, $usuario = null, $spot = null)
    {
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->usuario = $usuario;
        $this->spot = $spot;
        $this->fecha = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNota(): ?int
    {
        return $this->nota;
    }

    public function setNota(int $nota): self
    {
        $this->nota = $nota;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getSpot(): ?Spot
    {
        return $this->spot;
    }

    public function setSpot(?Spot $spot): self
    {
        $this->spot = $spot;

        return $this;
    }

    public function getFecha(): ?DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
}
