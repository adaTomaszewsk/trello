<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class AuthController extends AbstractController{
    #[Route('/registration', name: 'registration_auth')]
    public function index(): Response
    {

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('plainPassword', ButtonType::class)
            ->getForm();

        return $this->render('auth/index.html.twig', [
            'form' => $form,
        ]);
    }
}
