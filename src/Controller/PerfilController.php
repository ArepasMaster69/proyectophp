<?php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PerfilController extends AbstractController
{
    // === 1. LA VISTA DE TU PERFIL (La que hicimos nosotros) ===
    #[Route('/perfil', name: 'app_perfil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $posts = $entityManager->getRepository(Post::class)->findBy(['usuario' => $user], ['fecha' => 'DESC']);

        return $this->render('perfil/index.html.twig', [
            'usuario' => $user,
            'posts'   => $posts,
        ]);
    }

    // === 2. LA VISTA DE EDITAR (La de tu compañero) ===
    #[Route('/perfil/editar', name: 'app_perfil_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, UserRepository $userRepository): Response 
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');
        
        // Guardar cambios del perfil
        if ($request->isMethod('POST') && $request->request->has('guardarperfil')) {
            $nuevoNombre = trim($request->request->get('nick', ''));
            $biografia = trim($request->request->get('biografia', ''));
            $fechaNac = $request->request->get('fechanacimiento') ? new \DateTime($request->request->get('fechanacimiento')) : null;
            $ciudad = trim($request->request->get('ciudad', ''));
            
            $userRepository->updatePerfil($user->getId(), $nuevoNombre, $biografia, $fechaNac, $ciudad);
            
            $this->addFlash('success', 'Perfil actualizado correctamente');
            return $this->redirectToRoute('app_perfil');
        }
        
        return $this->render('perfil/editar.html.twig', [
            'user' => $user,
        ]);
    }
}