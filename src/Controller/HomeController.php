<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user) { return $this->redirectToRoute('app_login'); }

        // 1. GUARDAR LA PUBLICACIÓN NUEVA
        if ($request->isMethod('POST')) {
            $contenido = $request->request->get('contenido');
            
            if (!empty($contenido)) {
                $post = new Post();
                $post->setContenido($contenido);
                $post->setUsuario($user);
                $post->setFecha(new \DateTime());
                $post->setVisible(true);
                $post->setEditado(false);

                // ---  GUARDAR LA IMAGEN ---
                /** @var UploadedFile $imagenFile */
                $imagenFile = $request->files->get('imagen');
                
                if ($imagenFile) {
                    $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // Esto quita espacios y caracteres raros del nombre del archivo
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

                    try {
                        // Movemos el archivo a la carpeta public/uploads
                        $imagenFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );
                        // Guardamos solo el nombre en la base de datos
                        $post->setImagen($newFilename);
                    } catch (FileException $e) {
                        // Si falla la subida de la imagen, podrías manejar el error aquí
                    }
                }

                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('app_home'); 
            }
        }

        // 2. BUSCAR SOLO MIS POSTS Y LOS DE LA GENTE QUE SIGO
        $autoresPermitidos = $user->getSeguidos()->toArray();
        $autoresPermitidos[] = $user; 
        $posts = $em->getRepository(Post::class)->findBy(
            ['usuario' => $autoresPermitidos],
            ['fecha' => 'DESC']
        );

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}