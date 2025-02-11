<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

}
