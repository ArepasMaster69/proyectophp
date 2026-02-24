<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExplorarController extends AbstractController
{
    // Mostrar la lista de usuarios y el buscador
    #[Route('/explorar', name: 'app_explorar')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $termino = $request->query->get('q', '');

        if ($termino) {
            $usuarios = $userRepository->searchByName($termino);
        } else {
            $usuarios = $userRepository->findAllOrderedByName();
        }

        return $this->render('explorar/index.html.twig', [
            'usuarios' => $usuarios,
            'termino' => $termino
        ]);
    }

    // Ruta invisible Seguir/Dejar de seguir
    #[Route('/seguir/{id}', name: 'app_seguir', methods: ['POST'])]
    public function seguir(User $usuarioASeguir, EntityManagerInterface $em, Request $request): Response
    {
        $yo = $this->getUser();
        if (!$yo) return $this->redirectToRoute('app_login');

        if ($yo !== $usuarioASeguir) {
            if ($yo->getSeguidos()->contains($usuarioASeguir)) {
                $yo->removeSeguido($usuarioASeguir); // Dejar de seguir
            } else {
                $yo->addSeguido($usuarioASeguir); // Seguir
            }
            $em->flush();
        }

        // Volver a la página desde la que se pulsó el botón
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_explorar'));
    }
}