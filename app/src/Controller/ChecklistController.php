<?php

namespace App\Controller;

use App\Entity\Checklist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChecklistRepository;
use App\Repository\TaskRepository;
use App\Service\TaskFormService;
use App\Service\ChecklistFormService;
use Doctrine\ORM\EntityManagerInterface;

class ChecklistController extends AbstractController
{

    private $taskFormService;
    private $checklistFormService;

    public function __construct(TaskFormService $taskFormService, ChecklistFormService $checklistFormService)
    {
        $this->taskFormService = $taskFormService;
        $this->checklistFormService = $checklistFormService;
    }

	
    #[Route('/checklist/{checklistId?}', name: 'checklist_dashboard', requirements: ['checklistId' => '\d+'])]
    public function index(?int $checklistId, Request $request, ChecklistRepository $checklistRepository, TaskRepository $taskRepository): Response
    {	
		if($checklistId){
			$selectedChecklist = $checklistRepository->find($checklistId);
		} else {
			$selectedChecklist=NULL;
		}
		
        // Créer le formulaire de sélection de la checklist et le formulaire création de checklist
        $form = $this->checklistFormService->createSelectionForm($checklistRepository,$selectedChecklist);

        $formnew = $this->checklistFormService->createAddForm();

        if ($selectedChecklist) {
            // Récupérer les tâches de la checklist sélectionnée
			$tasks = $selectedChecklist->getTasks();
			$formAdd = $this->taskFormService->createAddTaskForm($selectedChecklist);

			// Afficher le formulaire dans le template Twig
			return $this->render('checklist/index.html.twig', [
				'form' => $form->createView(),
				'formnew' => $formnew->createView(),
				'formadd' => $formAdd->createView(),
				'tasks' => $tasks, 
				'checklist' => $selectedChecklist,
			]);
        }
        // Afficher le formulaire dans le template Twig
		return $this->render('checklist/index.html.twig', [
            'form' => $form->createView(),
			'formnew' => $formnew->createView(),
			'checklist' => '',
        ]);
    }

    #[Route('/checklist/selection', name: 'checklist_selection_checklist')]
    public function readChecklist(Request $request,ChecklistRepository $checklistRepository, EntityManagerInterface $entityManager): Response
    {
		$form = $this->checklistFormService->createSelectionForm($checklistRepository);
		$form->handleRequest($request);
		$checklist = $form->get('checklist')->getData();
		if ($form->getClickedButton()->getName() == 'delete'){
			$entityManager->remove($checklist);
			$entityManager->flush();
			$checklistId = null;
		} else {
			if($checklist){
				$checklistId = $checklist->getId();
			} else {
				$checklistId = null;
			}
		}	
			
			
		return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $checklistId]);
	}
	
    #[Route('/checklist/addtache', name: 'checklist_add_task')]	
	public function handleAddTask(EntityManagerInterface $entityManager, Request $request, ChecklistRepository $checklistRepository, TaskRepository $taskRepository)
    {
		// Récupérer les données soumises par le formulaire
		$formAdd = $this->taskFormService->createAddTaskForm();
		
		$formAdd->handleRequest($request);
		$newTask = $formAdd->getData();		
		$selectedtask = $formAdd->get('task')->getData();
        $selectedChecklist = $checklistRepository->find($formAdd->get('checklist_id')->getData());
		$selectedChecklist->getTasks();
        if (!$selectedtask && $newTask->getTitle()) {
            // Associer la nouvelle tâche à la checklist
            $selectedChecklist->addTask($newTask);

            // Persistez et flush la nouvelle tâche
            $entityManager->persist($newTask);
			$entityManager->flush();
			
			return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $selectedChecklist->getId()]);	
        
		} elseif ($selectedtask) {
            // Ajouter la tâche sélectionnée à la checklist
			$selectedtask->getChecklists()->initialize();		
            $selectedChecklist->addTask($selectedtask);

			$entityManager->persist($selectedChecklist);
			$entityManager->flush();

			$this->addFlash('success', 'La nouvelle tâche a été ajoutée avec succès !');
			
			return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $selectedChecklist->getId()]);	

        } else {
		// En cas de soumission invalide, ou si la requête n'est pas de type POST,
		// rediriger vers une page d'erreur ou afficher un message d'erreur
		return new Response('Invalid form submission', Response::HTTP_BAD_REQUEST);  
		}
	}
	
	#[Route('/checklist/removetask/{taskId}/{checklistId}', name: 'checklist_remove_task')]
	public function removeTask(int $taskId, int $checklistId, EntityManagerInterface $entityManager, TaskRepository $taskRepository, ChecklistRepository $checklistRepository): Response
	{
		$task = $taskRepository->find($taskId);
		$checklist = $checklistRepository->find($checklistId);

		if (!$task || !$checklist) {
			throw $this->createNotFoundException('La tâche ou la checklist n\'a pas été trouvée.');
		}

		// Dissocier la tâche de la checklist
		$checklist->removeTask($task);
		$entityManager->flush();
		
		return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $checklist->getId()]);	
	}
	
    #[Route('/checklist/addchecklist', name: 'checklist_ajout_checklist')]	
	public function handleAddChecklist(EntityManagerInterface $entityManager, Request $request)
	{
		$checklist = new Checklist();
		$form = $this->checklistFormService->createAddForm($checklist);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($checklist);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('checklist_dashboard', ['checklistId' => $checklist->getId()]);			
	}
}
