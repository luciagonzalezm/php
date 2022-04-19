<?php

namespace App\Controller;

use App\Entity\Asociados;
use App\Entity\Imagenes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //Consultas imágenes y asocociados
        $categoria1 = $entityManager->getRepository(Imagenes::class)->findBy(['categoria' => 1]);
        $categoria2 = $entityManager->getRepository(Imagenes::class)->findBy(['categoria' => 2]);
        $categoria3 = $entityManager->getRepository(Imagenes::class)->findBy(['categoria' => 3]);
        $asociados = $entityManager->getRepository(Asociados::class)->findAll();
        shuffle($asociados);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'Home',
            'categoria1' => $categoria1,
            'categoria2' => $categoria2,
            'categoria3' => $categoria3,
            'asociados' => $asociados
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('dashboard/about.html.twig', [
            'controller_name' => 'About',
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(): Response
    {
        return $this->render('dashboard/blog.html.twig', [
            'controller_name' => 'Blog',
        ]);
    }

    /**
     * @Route("/post", name="post")
     */
    public function post(): Response
    {
        return $this->render('dashboard/single_post.html.twig', [
            'controller_name' => 'Post',
        ]);
    }
}

