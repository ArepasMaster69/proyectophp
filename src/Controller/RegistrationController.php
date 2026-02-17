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
        // Si ya nos envían datos (POST)
        if ($request->isMethod('POST')) {
            $user = new User();
            
            // Recogemos los datos del formulario HTML
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $nombre = $request->request->get('nombre');
            $username = $request->request->get('username');

            // Rellenamos el usuario
            $user->setEmail($email);
            $user->setNombre($nombre);
            $user->setUsername($username);
            
            // ¡ENCRIPTAMOS LA CONTRASEÑA! (La magia)
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            // Guardamos en BD
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigir al login
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig');
    }
}