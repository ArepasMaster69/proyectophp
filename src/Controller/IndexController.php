<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Si el usuario ya ha iniciado sesión, lo mandamos directo al Muro
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Si no ha iniciado sesión, lo mandamos a la pantalla de Login
        return $this->redirectToRoute('app_login');
    }
}