<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\CommentType;

class CommentController extends AbstractController 
{

    private EntityManagerInterface $entityManager;
    private UserInterface $currentUser;

    public function __construct(EntityManagerInterface $entityManager,  Security $security)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $security->getUser();
    }

    #[Route('comment/{id}', name:'comment')]
    public function getComments(Request $request, $id, EntityManagerInterface $entityManager, UserInterface $currentUser): Response
    {
        $comments = [];
        $card = $this->entityManager->getRepository(Card::class)->find($id);
        if (!$card) {
            return new JsonResponse(['error' => 'Card not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment); 
        $commentForm->handleRequest($request);
        $comments = $this->entityManager->getRepository(Comment::class)->findBy((array('card' => $card)));
        if ($commentForm->isSubmitted() && $commentForm->isValid() ) {
            $comment = $commentForm->getData();
            $comment->setCard($card);
            $comment->setCreatedBy($currentUser);
            $comment->setCreatedAt(new \DateTime());
            $entityManager->persist($comment);
            $entityManager->flush();

            unset($comment);
            unset($commentForm);
            $comment = new Comment();
            $commentForm = $this->createForm(CommentType::class, $comment); 

            return $this->render('comment/index.html.twig', [
                'card' => $card,
                'commentForm' => $commentForm,
                'comments' => $comments,
            ]);
        } 

        return $this->render('comment/index.html.twig', [
            'card' => $card,
            'commentForm' => $commentForm,
            'comments' => $comments,
        ]);
    }

#[Route('/comment/delete/{id}', name:"comment_delete")]
public function deleteComment($id, EntityManagerInterface $entityManager): Response {

    $comment = $this->entityManager->getRepository(Comment::class)->find($id);
    if (!$comment) {
        return new JsonResponse(['error' => 'Comment not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $id = $comment->getCard()->getId();
    $entityManager->remove($comment);
    $entityManager->flush();
    return $this->redirectToRoute('comment', ['id' => $id]);
}

}