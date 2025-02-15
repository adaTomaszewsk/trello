<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserDataType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController {

    #[Route('/search-user', name:'search-user')]
    public function searchUser(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->get('q');
        dump($query);
        
        if (!$query) {
            new JsonResponse([]. 200);
        }
        $users = $userRepository->createQueryBuilder('u')
        ->where('u.email LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(10) 
        ->getQuery()
        ->getResult();

    $response = [];
    foreach ($users as $user) {
        $response[] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];
    }

    return new JsonResponse($response, 200);
}

#[Route('/user/settings', name:'settings')]
public function settings(Request $request, 
    UserPasswordHasherInterface $passwordHasher, 
    EntityManagerInterface $entityManager
): Response {

    $user = new User();
    
    $form = $this->createForm(UserDataType::class);
    $form->handleRequest($request);
    $firstName = $request->request->get('first_name');
    $lastName = $request->request->get('second_name');
    $phoneNumber = $request->request->get('phone_number');

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $form->getData();
        $user->setFristName($firstName);
        $user->setLastName($lastName);
        $user->setPhoneNumber($phoneNumber);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    return $this->render('settings/index.html.twig', [
        'form' => $form,
]);
}

}
