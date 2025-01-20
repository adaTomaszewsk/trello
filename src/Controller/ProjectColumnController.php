<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectColumn;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectColumnController extends AbstractController {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/columns/{id}', name:'all_columns')]
    public function all_columns(int $id, EntityManagerInterface $entityManager): Response 
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        $columns = $entityManager->getRepository(ProjectColumn::class)->findBy(array('project' => $project));
        return $this->render('columns/columns.html.twig', [
            'columns' => $columns,
        ]);
    }

    #[Route('/add_column', name:'add_column')]
    public function addColumn(int $id, string $columnName, EntityManagerInterface $entityManager): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        $column = new ProjectColumn();
        $column->setName($columnName);
        $column->setProject($project);
    
        $entityManager->persist($column);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Project column created successfully', JsonResponse::HTTP_CREATED]);

    }

} 