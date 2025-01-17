<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Project;
use App\Entity\ProjectColumn;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private UserInterface $currentUser;

    public function __construct(EntityManagerInterface $entityManager,  Security $security){
        $this->entityManager = $entityManager;
        $this->currentUser = $security->getUser();
    }


    #[Route('/api/projects', name:'api_projects')]
    public function getProjects(): Response
    {

        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $uri);
        if (is_array($parts) && count($parts) > 1) {
            $id = end($parts);
        }
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        return $this->render('components/sidebar_projects_render.html.twig',[
            'projects' => $projects,
            'current_project_id' => $id,
        ]);
    }


    #[Route('api/create-project', name: 'new-project')]
    public function createProject(Request $request, EntityManagerInterface $entityManager, UserInterface $currentUser): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);

        if(!$data || !isset($data['projectName']) || empty(trim($data['projectName']))) {
            return new JsonResponse(['error' => 'Project name is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $project = new Project();
        $project->setName($data['projectName']);
        $project->setCreatedBy($currentUser);
        $project->setCreatedAt(new \DateTime());
        $entityManager->persist($project);

        foreach ($data['columns'] as $columnName) {
            if (empty(trim($columnName))) {
                return new JsonResponse(['error' => 'Column name cannot be empty.'], JsonResponse::HTTP_BAD_REQUEST);
            }
            $column = new ProjectColumn();
            $column->setName($columnName);
            $column->setProject($project);
    
            $entityManager->persist($column);
        }
        $entityManager->flush();

        return new JsonResponse(['message' => 'Project created successfully', JsonResponse::HTTP_CREATED]);

    }

    #[Route('project/{id}', name: 'project_id')]
    public function projectPage(int $id, EntityManagerInterface $entityManager): Response {
        $project =  $this->entityManager->getRepository(Project::class)->findOneBy([
            'id'=> $id
        ]);

        return $this->render('project/index.html.twig', [
            'project' => $project,
        ]);
    }

}