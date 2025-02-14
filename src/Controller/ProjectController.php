<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Project;
use App\Entity\ProjectColumn;
use App\Entity\User;
use App\Form\CardType;
use App\Form\ProjectColumnType;
use App\Repository\ProjectColumnRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserInterface $currentUser;

    public function __construct(EntityManagerInterface $entityManager,  Security $security)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $security->getUser();
    }


    #[Route('/api/projects', name: 'api_projects')]
    public function getProjects(): Response
    {

        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $uri);
        if (is_array($parts) && count($parts) > 1) {
            $id = end($parts);
        }
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        return $this->render('components/sidebar_projects_render.html.twig', [
            'projects' => $projects,
            'current_project_id' => $id,
        ]);
    }


    #[Route('api/create-project', name: 'new-project')]
    public function createProject(Request $request, EntityManagerInterface $entityManager, UserInterface $currentUser): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['projectName']) || empty(trim($data['projectName']))) {
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
    public function projectPage(int $id, EntityManagerInterface $entityManager): Response
    {
        $projectRepo = $this->entityManager->getRepository(ProjectColumn::class);
        $project = $this->entityManager->getRepository(Project::class)->findOneBy([
            'id' => $id
        ]);
        $columns = $entityManager->getRepository(ProjectColumn::class)->findBy(array('project' => $project));
        $newColumn = new ProjectColumn();
        $form = $this->createForm(ProjectColumnType::class, $newColumn, [
            'action' => $this->generateUrl('add_column'),
        ]);
        $newCard = new Card();
        $addCardForm = $this->createForm(CardType::class, $newCard, [
            'action' => $this->generateUrl('add_card',),
        ]);
        $editCardForm = $this->createForm(CardType::class, $newCard);

        return $this->render('project/index.html.twig', [
            'project' => $project,
            'columns' => $columns,
            'form' => $form,
            'projectRepo' => $projectRepo,
            'addCardForm' => $addCardForm,
            'editCardForm' => $editCardForm,
        ]);
    }


    #[Route('/add_column', name: 'add_column')]
    public function addColumn(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $column = new ProjectColumn();

        $form = $this->createForm(ProjectColumnType::class, $column);
        $form->handleRequest($request);

        $projectId = $request->request->get('project_id');
        $project = $projectRepository->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Projekt nie istnieje');
        }
        if ($form->isValid() && $form->isSubmitted()) {
            $column->setProject($project);
            $column = $form->getData();

            $entityManager->persist($column);
            $entityManager->flush();

            return $this->redirectToRoute('project_id', ['id' => $column->getProject()->getId()]);
        }
    }

    #[Route('/columns/delete/{id}', name: 'delete_column')]
    public function deleteColumn($id, EntityManagerInterface $entityManager): Response
    {
        $column = $this->entityManager->getRepository(ProjectColumn::class)->find($id);
        if (!$column) {
            return new JsonResponse(['error' => 'Column not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $cardRepository = $entityManager->getRepository(ProjectColumn::class);
        $cardRepository->deleteCards($id);
        $id = $column->getProject()->getId();
        $entityManager->remove($column);
        $entityManager->flush();
        return $this->redirectToRoute('project_id', ['id' => $id]);
    }

    #[Route('/card/add_card', name: 'add_card')]
    public function addCard(Request $request, EntityManagerInterface $entityManager, UserInterface $currentUser): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $card = $form->getData();
            $projectColumn = $entityManager->getRepository(ProjectColumn::class)->find($card->getProjectColumn());

            if (!$projectColumn) {
                throw $this->createNotFoundException('Column not found');
            }
            $card->setProjectColumn($projectColumn);
            $card->setCreatedBy($currentUser);
            $card->setCreatedAt(new \DateTime());
            $entityManager->persist($card);
            $entityManager->flush();
        }
        return $this->redirectToRoute('project_id', ['id' => $card->getProjectColumn()->getProject()->getId()]);
    }

    #[Route('/card/delete_card/{id}', name: 'delete_card')]
    public function deleteCard($id, EntityManagerInterface $entityManager): Response
    {
        $card = $this->entityManager->getRepository(Card::class)->find($id);
        if (!$card) {
            return new JsonResponse(['error' => 'Card not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $id = $card->getProjectColumn()->getProject()->getId();
        $entityManager->remove($card);
        $entityManager->flush();
        return $this->redirectToRoute('project_id', ['id' => $id]);
    }

    #[Route('/card/edit_card/{id}', name: 'edit_card', methods: ['POST', 'GET'])]
    public function editCard(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $card = $this->entityManager->getRepository(Card::class)->find($id);
        if (!$card) {
            return new JsonResponse(['error' => 'Card not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $card = $form->getData();
            $projectColumn = $entityManager->getRepository(ProjectColumn::class)->find($card->getProjectColumn());
            if (!$projectColumn) {
                throw $this->createNotFoundException('Column not found');
            }
            $card->setProjectColumn($projectColumn);
            $entityManager->persist($card);
            $entityManager->flush();
            return $this->redirectToRoute('project_id', ['id' => $card->getProjectColumn()->getProject()->getId()]);
        }

        $id = $card->getProjectColumn()->getProject()->getId();
        return $this->render('project/card-edit.html.twig', [
            'cardForm' => $form,
            'card' => $card,
        ]);
    }

    #[Route('/card/move', name: 'move-card', methods: ['POST'])]
    public function move(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $cardId = $request->request->get('cardId');
        $newColumnId = $request->request->get('newColumnId');

        $card = $entityManager->getRepository(Card::class)->find($cardId);
        $newColumn = $entityManager->getRepository(ProjectColumn::class)->find($newColumnId);

        if (!$card || !$newColumn) {
            return new JsonResponse(['error' => 'Karta lub kolumna nie istnieje'], 400);
        }

        $card->setProjectColumn($newColumn);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Karta przeniesiona!']);
    }

    #[Route('/add-user-to-project', name: 'add_user_to_project')]
    public function addUserToProject(Request $request, UserRepository $userRepository, ProjectRepository $projectRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $projectId = $data['projectId'] ?? null;

        if (!$email || !$projectId) {
            return new JsonResponse(['message' => 'Brak danych'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        $project = $projectRepository->find($projectId);

        if (!$user || !$project) {
            return new JsonResponse(['message' => 'Nie znaleziono użytkownika lub projektu'], 404);
        }

        if ($project->getContributors()->contains($user)) {
            return new JsonResponse(['message' => 'Użytkownik już jest w tym projekcie'], 400);
        }

        $project->addContributor($user);
        $entityManager->persist($project);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Użytkownik dodany do projektu!'], 200);
    }

    #[Route('project/delete/{id}', name: 'delete_project')]
    public function deleteProject($id, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
     {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $columns = $projectRepository->getProjectColumns($id);
       
        foreach ($columns as $column) {
            $cardRepository = $entityManager->getRepository(ProjectColumn::class);
           
            $cardRepository->deleteCards($column);
            $entityManager->remove($column);
        }
    
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }

    #[Route('/project/edit/{id}', name: 'edit_project')]
    public function editProject($id, EntityManagerInterface $entityManager) : Response {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        return $this->render('project/edit.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/update_title/{id}', name: 'update_project_title', methods:['POST', 'GET'])]
    public function updateTitle(Request $request, $id, EntityManagerInterface $entityManager): JsonResponse 
    {
        $project = $entityManager->getRepository(Project::class)->find($id);
    
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
        $newTitle = $data['newTitle'] ?? null;
      
        if (!$newTitle) {
            return new JsonResponse(['error' => 'New project title not provided'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $project->setName($newTitle);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Project title updated successfully']);
    }

    #[Route('/project/delete_contributor/{projectId}/{id}', name:'delete_contributor_project')]
    public function deleteContributorProject($projectId, $id, EntityManagerInterface $entityManager,): Response {

        $project = $this->entityManager->getRepository(Project::class)->find($projectId);
        $contributor = $this->entityManager->getRepository(User::class)->find($id);
        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        if (!$contributor) {
            return new JsonResponse(['error' => 'Contributor not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        if (!$project->getContributors()->contains($contributor)) {
            return new JsonResponse(['error' => 'User is not a contributor'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $project->removeContributor($contributor);
        $entityManager->persist($project);
        $entityManager->flush();


        return $this->render('project/edit.html.twig', [
            'project' => $project,
        ]);
    }
}
