<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Aquí le decimos que este repositorio es el ayudante exclusivo de la entidad "Post"
        parent::__construct($registry, Post::class);
    }
    
    // Más adelante, si queremos hacer búsquedas complejas (ej: "buscar posts por fecha"), 
    // escribiremos esas funciones aquí. Por ahora, con esto basta.
}