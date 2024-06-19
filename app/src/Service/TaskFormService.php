<?php

namespace App\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Repository\TaskRepository;
use App\Entity\Checklist;
use App\Entity\Task;
use App\Form\TaskType;

/*
use Symfony\Component\Form\Extension\Core\Type\TextType;
 */

class TaskFormService
{

    private $formFactory;
    private $TaskRepository;

    public function __construct(FormFactoryInterface $formFactory, TaskRepository $TaskRepository)
    {
        $this->formFactory = $formFactory;
        $this->TaskRepository = $TaskRepository;
    }

    public function createAddTaskForm(Checklist $checklist = null)
    {
        if ($checklist) {
            $choices = $this->TaskRepository->findTasksNotInChecklist($checklist);
            $idChecklist = $checklist->getId();
			} else {
            $choices = $this->TaskRepository->findAll();
            $idChecklist = null;
        }

        $task = new Task();

        $form = $this->formFactory->create(TaskType::class, $task, [
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
