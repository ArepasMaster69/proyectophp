<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Si el usuario ya está logueado, lo mandamos directo al muro
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Obtener el error de login si hay alguno (ej: "Invalid credentials")
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Mantener el último email introducido para que no tenga que escribirlo de nuevo
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Este método puede estar vacío, Symfony lo maneja solo.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}