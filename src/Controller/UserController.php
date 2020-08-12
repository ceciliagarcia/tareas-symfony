<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegisterType;

class UserController extends AbstractController
{
   
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        //creamos el formulario
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        //rellenar objeto con el form
        $form->handleRequest($request);

        //comprobar si se envia
        if($form->isSubmitted() && $form->isValid()){

            //modificar objeto para guardarlo
            $user->setRole('ROLE_USER');
            $user->setCreatedAt(new \Datetime('now'));

            // Cifrar contraseña
			$encoded = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($encoded);

            //guardar usuario
            $em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			
			return $this->redirectToRoute('tasks');
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function login(AuthenticationUtils $autenticationUtils){
		$error = $autenticationUtils->getLastAuthenticationError();
		
		$lastUsername = $autenticationUtils->getLastUsername();
		
		return $this->render('user/login.html.twig', array(
			'error' => $error,
			'last_username' => $lastUsername
		));
	}
}
