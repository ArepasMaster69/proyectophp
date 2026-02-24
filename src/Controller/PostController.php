<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/comentario/{id}', name: 'crear_comentario', methods: ['POST'])]
    public function crearComentario(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $contenido = $request->request->get('contenido');

        if (!$contenido) {
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        $comentario = new Comment();
        $comentario->setContenido($contenido);
        $comentario->setFecha(new \DateTime());
        $comentario->setUsuario($this->getUser());
        $comentario->setPost($post);

        $em->persist($comentario);
        $em->flush();

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    // ====== NUEVO: ELIMINAR POST (SOLO ADMINS) ======
    #[Route('/post/eliminar/{id}', name: 'eliminar_post', methods: ['POST'])]
    public function eliminarPost(Post $post, EntityManagerInterface $em, Request $request): Response
    {
        // Esta línea bloquea a cualquiera que no sea ADMIN (rol 1)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($post);
        $em->flush();

        // Volver a la página anterior (el muro o el perfil)
        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_home'));
    }

    // ====== NUEVO: ELIMINAR COMENTARIO (SOLO ADMINS) ======
    #[Route('/comentario/eliminar/{id}', name: 'eliminar_comentario', methods: ['POST'])]
    public function eliminarComentario(Comment $comentario, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($comentario);
        $em->flush();

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_home'));
    }
}