<?php

namespace App\Controller;

use App\Entity\Mensajes;
use App\Form\ContactoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MensajeController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $mensaje = new Mensajes();
        $form = $this->createForm(ContactoType::class, $mensaje);
        $form->handleRequest($request);

        //Comprobación formulario
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mensaje);
            $entityManager->flush();

            //Mensaje email
            $message = (new \Swift_Message($mensaje->getAsunto()))
                ->setFrom($mensaje->getEmail())
                ->setTo('lgonzalezm59@informatica.iesvalledeljerteplasencia.es')
                ->setDate($mensaje->getFecha())
                ->setSubject($mensaje->getAsunto())
                ->setBody(
                    'Nombre: ' . $mensaje->getNombre() . ' ' . $mensaje->getApellidos() . "\n" . 'Email: ' . $mensaje->getEmail() . "\n" . 'Mensaje: ' . $mensaje->getTexto()
                )
            ;

            $mailer->send($message);

            //Mensaje cuando se guarda
            $this->addFlash("exito", Mensajes::MENSAJE_GUARDADO);
            return $this->redirectToRoute('contact');
        }
        return $this->render('mensaje/index.html.twig', [
            'controller_name' => 'Contacto',
            'formulario' => $form->createView()
        ]);
    }
}
