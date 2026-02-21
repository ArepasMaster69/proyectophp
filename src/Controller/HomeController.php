<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    public function root(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Buscamos todos los posts en la base de datos, ordenados del más nuevo al más viejo
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['fecha' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'posts' => $posts, // Le pasamos la lista de posts al HTML
        ]);
    }
}