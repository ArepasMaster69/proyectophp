<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    // Relación: Muchos Posts pertenecen a un Usuario
    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    // Relación: Un Post tiene muchos Comentarios
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comentario::class, orphanRemoval: true)]
    private Collection $comentarios;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->fecha = new \DateTime(); // Se pone la fecha actual automáticamente al crear
    }

    public function getId(): ?int { return $this->id; }

    public function getContenido(): ?string { return $this->contenido; }
    public function setContenido(string $contenido): static { $this->contenido = $contenido; return $this; }

    public function getFecha(): ?\DateTimeInterface { return $this->fecha; }
    public function setFecha(\DateTimeInterface $fecha): static { $this->fecha = $fecha; return $this; }

    public function getFoto(): ?string { return $this->foto; }
    public function setFoto(?string $foto): static { $this->foto = $foto; return $this; }

    public function getUsuario(): ?User { return $this->usuario; }
    public function setUsuario(?User $usuario): static { $this->usuario = $usuario; return $this; }

    public function getComentarios(): Collection { return $this->comentarios; }
}