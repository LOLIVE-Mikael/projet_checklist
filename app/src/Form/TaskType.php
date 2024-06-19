<?php

namespace App\Form;

use App\Entity\Checklist;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => $options['requis'], 
            ])
			
            ->add('duration', DateIntervalType::class, [
					'widget'      => 'integer', // render a text field for each part
					// 'input'    => 'string',  // if you want the field to return a ISO 8601 string back to you
					// customize which text boxes are shown
					'with_years'  => false,
					'with_months' => false,
					'with_days'   => false,
					'with_hours'  => true,
					'with_minutes'  => true,
					'required' => false,				
				])
			->add('id', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'requis' => true,
        ]);
    }
}
