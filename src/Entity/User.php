<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'usuarios')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'password_hash', length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'foto_perfil', length: 250, nullable: true)]
    private ?string $fotoPerfil = null;

    #[ORM\Column(type: 'smallint', options: ['default' => 0])]
    private ?int $rol = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $biografia = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaNacimiento = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ciudad = null;

    //  SISTEMA DE SEGUIDORES 
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'seguidores')]
    #[ORM\JoinTable(name: 'user_follows')]
    #[ORM\JoinColumn(name: 'user_source', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_target', referencedColumnName: 'id')]
    private Collection $seguidos;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'seguidos')]
    private Collection $seguidores;

    public function __construct()
    {
        $this->seguidos = new ArrayCollection();
        $this->seguidores = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array {
        $roles = $this->rol === 1 ? ['ROLE_ADMIN', 'ROLE_USER'] : ['ROLE_USER'];
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getNombre(): ?string { return $this->nombre; }
    public function setNombre(string $nombre): static { $this->nombre = $nombre; return $this; }

    public function getFotoPerfil(): ?string { return $this->fotoPerfil; }
    public function setFotoPerfil(?string $fotoPerfil): static { $this->fotoPerfil = $fotoPerfil; return $this; }

    public function getBiografia(): ?string { return $this->biografia; }
    public function setBiografia(?string $biografia): static { $this->biografia = $biografia; return $this; }

    public function getFechaNacimiento(): ?\DateTimeInterface { return $this->fechaNacimiento; }
    public function setFechaNacimiento(?\DateTimeInterface $fechaNacimiento): static { $this->fechaNacimiento = $fechaNacimiento; return $this; }

    public function getCiudad(): ?string { return $this->ciudad; }
    public function setCiudad(?string $ciudad): static { $this->ciudad = $ciudad; return $this; }

    //  FUNCIONES DE SEGUIDORES 
    public function getSeguidos(): Collection { return $this->seguidos; }
    public function addSeguido(self $seguido): static {
        if (!$this->seguidos->contains($seguido)) {
            $this->seguidos->add($seguido);
        }
        return $this;
    }
    public function removeSeguido(self $seguido): static {
        $this->seguidos->removeElement($seguido);
        return $this;
    }

    public function getSeguidores(): Collection { return $this->seguidores; }

    public function eraseCredentials(): void {}
}