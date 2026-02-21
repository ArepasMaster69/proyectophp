<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registro', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Si el usuario ya está logueado, no tiene sentido que se registre
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Si nos envían el formulario (POST)
        if ($request->isMethod('POST')) {
            $user = new User();
            
            // Recogemos los datos del formulario HTML
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $nombre = $request->request->get('nombre');

            // Rellenamos el usuario (sin el username)
            $user->setEmail($email);
            $user->setNombre($nombre);
            
            // ¡ENCRIPTAMOS LA CONTRASEÑA!
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            // Guardamos en la Base de Datos
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigir al login para que entre con su nueva cuenta
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig');
    }
}