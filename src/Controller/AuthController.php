<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class AuthController extends AbstractController{

    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/registration', name: 'app_registration')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {

        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword(),
            );
            
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/index.html.twig', [
            'form' => $form,
        ]);
    }

}
