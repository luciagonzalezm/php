<?php

namespace App\Controller;

use App\Entity\Asociados;
use App\Form\AsociadoType;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AsociadoController extends AbstractController
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
     * @Route("/asociados", name="asociados")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $asociado = new Asociados();
        $form = $this->createForm(AsociadoType::class, $asociado);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $asociados = $entityManager->getRepository(Asociados::class)->findAll();

        //Comprobación formulario
        if ($form->isSubmitted() && $form->isValid()){
            $imagesFile = $form->get('logo')->getData();

            //Comprobación imagen
            if ($imagesFile) {
                $originalFilename = pathinfo($imagesFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagesFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imagesFile->move(
                        $this->getParameter('asociados_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Extensión de imagen no soportada.');
                }
                $asociado->setLogo($newFilename);
            }else {
                $this->addFlash("error", 'Error en la subida del fichero.');
                return $this->redirectToRoute('asociados');
            }

            $entityManager = $this->getDoctrine()->getManager();
            //Guardando en la base de datos
            $entityManager->persist($asociado);
            $entityManager->flush();

            //Mensaje log
            $message = "Se ha guardado un nuevo asociado: " . $asociado->getNombre() . " -> " . $asociado->getLogo();
            $this->logger->log(Logger::INFO, $message);

            //Mensaje cuando se guarda
            $this->addFlash("exito", Asociados::ASOCIADO_GUARDADO);
            return $this->redirectToRoute('asociados');
        }

        return $this->render('asociado/index.html.twig', [
            'controller_name' => 'Asociados',
            'formulario' => $form->createView(),
            'asociados' => $asociados
        ]);
    }
}
