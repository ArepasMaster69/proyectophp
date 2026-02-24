<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'posts')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    // Mantenemos nuestra variable $fecha para que no explote el Muro
    #[ORM\Column(name: 'fecha_publicacion', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    // === NUEVOS CAMPOS DE TU COMPAÑERO ===
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(name: 'archivo_adjunto', length: 255, nullable: true)]
    private ?string $archivoAdjunto = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $visible = true;

    #[ORM\Column(type: 'boolean')]
    private ?bool $editado = false;
    // =====================================

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'usuario_id', nullable: false)]
    private ?User $usuario = null;

    // Cambiado para conectar con su entidad Comment
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comentarios;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->fecha = new \DateTime(); 
    }

    // --- GETTERS Y SETTERS ---
    public function getId(): ?int { return $this->id; }

    public function getContenido(): ?string { return $this->contenido; }
    public function setContenido(string $contenido): static { $this->contenido = $contenido; return $this; }

    public function getFecha(): ?\DateTimeInterface { return $this->fecha; }
    public function setFecha(\DateTimeInterface $fecha): static { $this->fecha = $fecha; return $this; }

    // Setters de tu compañero
    public function getImagen(): ?string { return $this->imagen; }
    public function setImagen(?string $imagen): static { $this->imagen = $imagen; return $this; }

    public function getArchivoAdjunto(): ?string { return $this->archivoAdjunto; }
    public function setArchivoAdjunto(?string $archivo): static { $this->archivoAdjunto = $archivo; return $this; }

    public function isVisible(): ?bool { return $this->visible; }
    public function setVisible(bool $visible): static { $this->visible = $visible; return $this; }

    public function isEditado(): ?bool { return $this->editado; }
    public function setEditado(bool $editado): static { $this->editado = $editado; return $this; }

    public function getUsuario(): ?User { return $this->usuario; }
    public function setUsuario(?User $usuario): static { $this->usuario = $usuario; return $this; }

    public function getComentarios(): Collection { return $this->comentarios; }
}