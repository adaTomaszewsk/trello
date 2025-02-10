<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController{

    #[Route('/login', name:'app_login')]
    public function login(AuthenticationUtils $auth) :Response
    {
        $error = $auth->getLastAuthenticationError();
        $lastUsername = $auth->getLastUsername();


        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name:'app_logout')]
    public function logout(Security $security) :Response 
    {
        $security->logout();
        
        return $this->redirectToRoute('app_login');
    }
}