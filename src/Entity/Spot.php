<?php

namespace App\Entity;

use App\Repository\SpotRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpotRepository::class)
 */
class Spot
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
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provincia;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coord;

    /**
     * @ORM\OneToMany(targetEntity=Valoracion::class, mappedBy="spot", orphanRemoval=true)
     */
    private $valoraciones;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $notaMedia;

    /**
     * @ORM\ManyToOne(targetEntity=Deporte::class, inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deporte;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $aprobado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $revision;

    /**
     * Spot constructor.
     * @param null $nombre
     * @param null $deporte
     * @param null $provincia
     * @param null $coord
     * @param null $user
     */
    public function __construct($nombre = null, $deporte = null, $provincia = null, $coord = null, $user = null)
    {
        $this->nombre = $nombre;
        $this->deporte = $deporte;
        $this->provincia = $provincia;
        $this->coord = $coord;
        $this->user = $user;

        // Inicializaciones por defecto
        $this->notaMedia = 0;
        $this->fecha = new DateTime();
        $this->valoraciones = new ArrayCollection();
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

    public function getFecha(): ?DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getCoord(): ?string
    {
        return $this->coord;
    }

    public function setCoord(?string $coord): self
    {
        $this->coord = $coord;

        return $this;
    }

    /**
     * @return Collection|Valoracion[]
     */
    public function getValoraciones(): Collection
    {
        return $this->valoraciones;
    }

    public function addValoracion(Valoracion $valoracion): self
    {
        if (!$this->valoraciones->contains($valoracion)) {
            $this->valoraciones[] = $valoracion;
            $valoracion->setSpot($this);

            $this->updateNotaMedia();
        }

        return $this;
    }

    public function removeValoracion(Valoracion $valoracion): self
    {
        if ($this->valoraciones->removeElement($valoracion)) {
            // set the owning side to null (unless already changed)
            if ($valoracion->getSpot() === $this) {
                $valoracion->setSpot(null);
            }
            $this->updateNotaMedia();
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNotaMedia(): ?float
    {
        return $this->notaMedia;
    }

    public function updateNotaMedia(): self
    {
        $this->notaMedia = $this->calcularNotaMedia();

        return $this;
    }

    /**
     * @return int
     */
    private function calcularNotaMedia(): int
    {
        // Media de las notas
        $media = 0;
        if (count($this->valoraciones) > 0) {
            foreach ($this->valoraciones as $valoracion) {
                $media += $valoracion->getNota();
            }
            $media /= count($this->valoraciones);
        }
        return $media;
    }

    public function getDeporte(): ?Deporte
    {
        return $this->deporte;
    }

    public function setDeporte(?Deporte $deporte): self
    {
        $this->deporte = $deporte;

        return $this;
    }

    public function getAprobado(): ?bool
    {
        return $this->aprobado;
    }

    public function setAprobado(?bool $aprobado): self
    {
        $this->aprobado = $aprobado;

        return $this;
    }

    public function getRevision(): ?string
    {
        return $this->revision;
    }

    public function setRevision(?string $revision): self
    {
        $this->revision = $revision;

        return $this;
    }
}
