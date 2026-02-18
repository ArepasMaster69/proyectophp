<?php
// src/Controller/PerfilController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PerfilController extends AbstractController
{
    #[Route('/perfil/editar', name: 'app_perfil_editar', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editar(
        Request $request, 
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response 
    {
        $user = $this->getUser();
        
        // Cambiar foto
        if ($request->isMethod('POST') && $request->request->has('cambiarfoto')) {
            /** @var UploadedFile|null $fotoFile */
            $fotoFile = $request->files->get('fotoperfil');
            
            if ($fotoFile && $fotoFile->isValid()) {
                $nombreArchivo = $user->getUserIdentifier() . '.jpg';
                $rutaCompleta = $this->getParameter('kernel.project_dir') . '/public/IMAGES/' . $nombreArchivo;
                
                try {
                    $fotoFile->move(dirname($rutaCompleta), basename($rutaCompleta));
                    $userRepository->updateFotoPerfil($user->getId(), '/IMAGES/' . $nombreArchivo);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error al subir la imagen');
                }
            }
        }
        
        // Eliminar foto
        if ($request->isMethod('POST') && $request->request->has('eliminarfoto')) {
            $userRepository->updateFotoPerfil($user->getId(), null);
        }
        
        // Actualizar datos perfil
        if ($request->isMethod('POST') && $request->request->has('guardarperfil')) {
            $nuevoNombre = trim($request->request->get('nick', ''));
            $biografia = trim($request->request->get('biografia', ''));
            $fechaNac = $request->request->get('fechanacimiento') ? new \DateTime($request->request->get('fechanacimiento')) : null;
            $ciudad = trim($request->request->get('ciudad', ''));
            
            $userRepository->updatePerfil($user->getId(), $nuevoNombre, $biografia, $fechaNac, $ciudad);
            
            $this->addFlash('success', 'Perfil actualizado correctamente');
            return $this->redirectToRoute('app_perfil_editar');
        }
        
        return $this->render('perfil/editar.html.twig', [
            'user' => $user,
        ]);
    }
}
