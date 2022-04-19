<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Form\RegistroType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws LogicException
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        //Creamos un objeto $usuario de la clase Usuario y otro objeto
        //$form con el método createForm al que le pasamos el formulario
        //RegistroType y el objeto $usuario que acabamos de crear
        $usuario = new Usuarios();
        $form = $this->createForm(RegistroType::class, $usuario);
        $form->handleRequest($request);

        //Comprobación formulario
        if($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form['password']->getData();

            $encoded = $encoder->encodePassword($usuario, $plainPassword);
            $usuario->setPassword($encoded);

            //Guardando en la base de datos
            $entityManager = $this->getDoctrine()->getManager();
            //Guardando en la base de datos
            $entityManager->persist($usuario);
            $entityManager->flush();

            //Mensaje cuando se guarda
            $this->addFlash("exito", Usuarios::REGISTRO_CORRECTO);
            return $this->redirectToRoute('registro');
        }
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'Registro',
            //Le pasamos al template la vista del formulario que acabamos de crear
            'formulario' => $form->createView()
        ]);
    }
}
