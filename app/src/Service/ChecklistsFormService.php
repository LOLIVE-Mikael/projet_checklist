<?php
namespace App\Service;

use App\Entity\Checklists;
use App\Form\ChecklistsType; 
use App\Repository\ChecklistsRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChecklistsFormService
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    } 

    public function createSelectionForm(ChecklistsRepository $checklistsRepository, Checklists $checklist = null): FormInterface
    {
        // Charger les données de la checklist si nécessaire
        if ($checklist) {
            $checklist->getTasks();
        }

        // Récupérer toutes les checklists
        $checklists = $checklistsRepository->findAll();

        return $this->formFactory->createBuilder()
            ->add('checklist', ChoiceType::class, [
                'choices' => $checklists,
                'choice_label' => 'title',
                'choice_value' => 'id',
                'placeholder' => 'Choisir une checklist',
                'data' => $checklist,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voir',
            ])
			->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
            ])
            ->getForm();
    }

    public function createAddForm(Checklists $checklist = null)
    {
        // Créer un formulaire en utilisant le formulaire ChecklistType
        $form = $this->formFactory->createBuilder(ChecklistsType::class,$checklist)
            ->add('creer', SubmitType::class)
            ->getForm();

        return $form;
    }
}
