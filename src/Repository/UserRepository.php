<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * ¡ESTA ES LA MAGIA! 
     * Esta función busca al usuario por email O por nombre cuando intenta hacer login.
     */
    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this->getEntityManager()->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.email = :query
            OR u.nombre = :query'
        )
        ->setParameter('query', $identifier)
        ->getOneOrNullResult();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // ===== MÉTODOS PARA HOME.PHP =====
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // ===== MÉTODOS PARA USUARIOS.PHP =====
    public function searchByName(string $termino): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nombre LIKE :termino')
            ->setParameter('termino', $termino . '%')
            ->orderBy('u.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // ===== MÉTODOS PARA EDITAR PERFIL =====
    public function updateFotoPerfil(int $userId, ?string $rutaImagen): void
    {
        $entityManager = $this->getEntityManager();
        $user = $this->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        
        $user->setFotoPerfil($rutaImagen);
        $entityManager->flush();
    }

    public function updatePerfil(int $userId, string $nombre, ?string $biografia, ?\DateTimeInterface $fechaNacimiento, ?string $ciudad): void
    {
        $entityManager = $this->getEntityManager();
        $user = $this->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        
        $user->setNombre($nombre);
        $user->setBiografia($biografia);
        $user->setFechaNacimiento($fechaNacimiento);
        $user->setCiudad($ciudad);
        
        $entityManager->flush();
    }
}