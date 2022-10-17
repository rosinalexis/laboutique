<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,EntityManagerInterface $em): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterFormType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
