<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    #[Route('/compte', name: 'app_account',methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modification-mot-de-passe', name: 'app_account_change_password',methods: ['GET','POST'])]
    public function changeUserPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /**
         * @var $user User
         */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $old_password = $form->get('old_password')->getData();

           if($passwordHasher->isPasswordValid($user,$old_password))
           {
               $new_password = $form->get('new_password')->getData();

               $hash_password = $passwordHasher->hashPassword($user,$new_password);

               $user->setPassword($hash_password);

               $this->em->flush();

               $this->addFlash('success','Votre mot de passe a bien été modifié.');

               return $this->redirectToRoute('app_account');
           }else{

               $this->addFlash('error','L\'ancien mot de passe n\'est pas correcte.');
           }

        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
