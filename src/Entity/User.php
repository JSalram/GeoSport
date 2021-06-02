<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    const FOTO_DEFAULT = "https://www.worldfuturecouncil.org/wp-content/uploads/2020/06/blank-profile-picture-973460_1280-1.png";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Valoracion::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $valoraciones;

    /**
     * @ORM\OneToMany(targetEntity=Spot::class, mappedBy="user")
     */
    private $spots;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ultimoAcceso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * User constructor.
     * @param null $email
     * @param null $username
     * @param null $password
     */
    public function __construct($email = null, $username = null, $password = null)
    {
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->ultimoAcceso = new DateTime();
        $this->valoraciones = new ArrayCollection();
        $this->spots = new ArrayCollection();
        $this->foto = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
            $valoracion->setUser($this);
        }

        return $this;
    }

    public function removeValoracion(Valoracion $valoracion): self
    {
        if ($this->valoraciones->removeElement($valoracion)) {
            // set the owning side to null (unless already changed)
            if ($valoracion->getUser() === $this) {
                $valoracion->setUser(null);
            }
        }

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
            $spot->setUser($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getUser() === $this) {
                $spot->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUltimoAcceso(): ?\DateTimeInterface
    {
        return $this->ultimoAcceso;
    }

    public function setUltimoAcceso(?\DateTimeInterface $ultimoAcceso): self
    {
        $this->ultimoAcceso = $ultimoAcceso;

        return $this;
    }

    public function getFoto(): ?string
    {
        if ($this->foto === null) {
            return self::FOTO_DEFAULT;
        } else {
            return 'img/user/' . $this->foto;
        }
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function removeFoto(): self
    {
        $this->foto = null;

        return $this;
    }

    public function hasFoto(): bool
    {
        return $this->foto !== null;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
