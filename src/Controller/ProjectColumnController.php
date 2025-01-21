<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectColumn;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function addColumn(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = $this->entityManager->getRepository(Project::class)->find($data['projectId']);
        $column = new ProjectColumn();
        $column->setName($data['column']);
        $column->setProject($project);

    
        $entityManager->persist($column);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Project column created successfully', JsonResponse::HTTP_CREATED]);

    }

    #[Route('/columns/{id}/delete', name: 'delete_column')]
    public function deleteColumn($id, EntityManagerInterface $entityManager): Response
    {
        $column = $this->entityManager->getRepository(ProjectColumn::class)->find($id);
        if (!$column) {
            return new JsonResponse(['error' => 'Column not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($column);
        $entityManager->flush();
        return $this->redirectToRoute('project_id', ['id' => $column->getProject()->getId()]);
    }

} 