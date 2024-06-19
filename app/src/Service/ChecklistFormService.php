<?php
namespace App\Service;

use App\Entity\Checklist;
use App\Form\ChecklistType; 
use App\Repository\ChecklistRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChecklistFormService
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    } 

    public function createSelectionForm(ChecklistRepository $checklistRepository, Checklist $checklist = null): FormInterface
    {
        // Charger les données de la checklist si nécessaire
        if ($checklist) {
            $checklist->getTasks();
        }

        // Récupérer toutes les checklists
        $checklists = $checklistRepository->findAll();

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

    public function createAddForm(Checklist $checklist = null)
    {
        // Créer un formulaire en utilisant le formulaire ChecklistType
        $form = $this->formFactory->createBuilder(ChecklistType::class,$checklist)
            ->add('creer', SubmitType::class)
            ->getForm();

        return $form;
    }
}
