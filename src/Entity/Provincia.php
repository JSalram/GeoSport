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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=Spot::class, mappedBy="provincia")
     */
    private $spots;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coord;

    /**
     * Provincia constructor.
     * @param null $nombre
     * @param null $coord
     */
    public function __construct($nombre = null, $coord = null)
    {
        $this->nombre = $nombre;
        $this->coord = $coord;
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
            $spot->setProvincia($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getProvincia() === $this) {
                $spot->setProvincia(null);
            }
        }

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
}
