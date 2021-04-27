<?php

namespace App\Entity;

use App\Repository\DeporteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeporteRepository::class)
 */
class Deporte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=Spot::class, mappedBy="deporte", orphanRemoval=true)
     */
    private $spots;

    /**
     * Deporte constructor.
     * @param $nombre
     * @param $descripcion
     */
    public function __construct($nombre = null, $descripcion = null)
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->spots = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection|Spot[]
     */
    public function getSpots(): Collection
    {
        return $this->spots;
    }

    public function addSpot(Spot $spot): self
    {
        if (!$this->spots->contains($spot)) {
            $this->spots[] = $spot;
            $spot->setDeporte($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getDeporte() === $this) {
                $spot->setDeporte(null);
            }
        }

        return $this;
    }
}
