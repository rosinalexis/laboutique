<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterFormType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var $user User
             */
            $user = $form->getData();

            $user->setPassword($passwordHasher->hashPassword($user,$user->getPassword()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success','Voter inscription est validé.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
