<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Entity\Imagenes;
use App\Form\ImagenGaleriaType;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImagenGaleriaController extends AbstractController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $appLogger)
    {
        $this->logger = $appLogger;
    }
    
    /**
     * @Route("/imagenes-galeria", name="imagenes_galeria")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $imagen = new Imagenes();
        $form = $this->createForm(ImagenGaleriaType::class, $imagen);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $imagenes = $entityManager->getRepository(Imagenes::class)->mostrarTodasImagenes();

        //Comprobación formulario
        if ($form->isSubmitted() && $form->isValid()){
            $imagesFile = $form->get('nombre')->getData();

            //Comprobación imagen
            if ($imagesFile) {
                $originalFilename = pathinfo($imagesFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagesFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imagesFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Extensión de imagen no soportada.');
                }
                $imagen->setNombre($newFilename);
            }else {
                $this->addFlash("error", 'Error en la subida del fichero.');
                return $this->redirectToRoute('imagenes_galeria');
            }

            //Actualizando numImagenes categorias
            $categoria = $imagen->getCategoria();
            $categoria->setNumImagenes($categoria->getNumImagenes() + 1);

            //Guardando en la base de datos
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($imagen);
            $entityManager->persist($categoria);
            $entityManager->flush();

            //Mensaje log
            $message = "Se ha guardado una nueva imagen: " . $imagen->getNombre();
            $this->logger->log(Logger::INFO, $message);

            //Mensaje cuando se guarda
            $this->addFlash("exito", Imagenes::IMAGEN_GUARDADA);
            return $this->redirectToRoute('imagenes_galeria');
        }

        return $this->render('imagen_galeria/index.html.twig', [
            'controller_name' => 'Galeria',
            'formulario' => $form->createView(),
            'imagenes' => $imagenes
        ]);
    }

    /**
     * @Route("/imagenes-galeria/{id}", name="imagenes-galeria")
     * @param $id
     * @return Response
     */
    public function show($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $imagenes = $entityManager->getRepository(Imagenes::class)->findBy(['id' => $id]);
        return $this->render('imagen_galeria/show-imagen-galeria.html.twig', [
            'imagenes' => $imagenes
        ]);

    }
}
