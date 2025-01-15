<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Project;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    #[Route('/api/projects', name:'api_projects')]
    public function getProjects(): Response
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
 
        return $this->render('components/sidebar_projects_render.html.twig',[
            'projects' => $projects
        ]);
    }

}