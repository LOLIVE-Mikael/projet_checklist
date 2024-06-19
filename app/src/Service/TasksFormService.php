<?php

namespace App\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Repository\TasksRepository;
use App\Entity\Checklists;
use App\Entity\Tasks;
use App\Form\TasksType;

/*
use Symfony\Component\Form\Extension\Core\Type\TextType;
 */

class TasksFormService
{

    private $formFactory;
    private $tasksRepository;

    public function __construct(FormFactoryInterface $formFactory, TasksRepository $tasksRepository)
    {
        $this->formFactory = $formFactory;
        $this->tasksRepository = $tasksRepository;
    }

    public function createAddTaskForm(Checklists $checklist = null)
    {
        if ($checklist) {
            $choices = $this->tasksRepository->findTasksNotInChecklist($checklist);
            $idChecklist = $checklist->getId();
			} else {
            $choices = $this->tasksRepository->findAll();
            $idChecklist = null;
        }

        $task = new Tasks();

        $form = $this->formFactory->create(TasksType::class, $task, [
				'requis' => false,
				]);
		
		$form->add('task', ChoiceType::class, [
                'choices' => $choices,
                'choice_value' => 'id',
                'choice_label' => 'title',
                'placeholder' => 'Choisir une tâche',
				'mapped' => false,
                'required' => false
            ])
            ->add('checklist_id', HiddenType::class, [
                'data' => $idChecklist,
				'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ]);
		return $form;
    }
}
