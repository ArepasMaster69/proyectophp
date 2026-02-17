<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'usuarios')] // OJO: Tu tabla se llama 'usuarios' en el SQL
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    // En tu SQL la columna se llama 'password_hash', pero Symfony prefiere usar $password internamente.
    // Con name: 'password_hash' hacemos el puente.
    #[ORM\Column(name: 'password_hash', length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(name: 'foto_perfil', length: 250, nullable: true)]
    private ?string $fotoPerfil = null;

    // Tu SQL usa un número (tinyint) para el rol (1 o 0).
    #[ORM\Column(type: 'smallint', options: ['default' => 0])]
    private ?int $rol = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Identificador visual del usuario (el email en este caso)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Symfony necesita este método obligatoriamente.
     * Convertimos tu columna 'rol' (número) al formato que quiere Symfony (array).
     */
    public function getRoles(): array
    {
        // Si en tu SQL rol es 1, es ADMIN, si no, es USER.
        $roles = $this->rol === 1 ? ['ROLE_ADMIN', 'ROLE_USER'] : ['ROLE_USER'];
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        // Este método es necesario por la interfaz, aunque tu DB use un entero.
        // Por ahora no lo usaremos activamente para guardar.
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function eraseCredentials(): void
    {
        // Si guardaras datos sensibles temporales, se borrarían aquí.
    }
}