<?php

namespace App\DataFixtures;

use App\Entity\Checklists;
use App\Entity\Tasks;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\TasksFixtures;

class ChecklistsFixtures extends Fixture implements DependentFixtureInterface
{
    public const CHECKLISTS_1_REFERENCE = 'checklist-1';
    public const CHECKLISTS_2_REFERENCE = 'checklist-2';
	
    public function getDependencies()
    {
        return [
            TasksFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // Récupération des références aux tâches créées dans les fixtures précédentes
        $task1 = $this->getReference(TasksFixtures::TASK_1_REFERENCE);
        $task2 = $this->getReference(TasksFixtures::TASK_2_REFERENCE);
        $task3 = $this->getReference(TasksFixtures::TASK_3_REFERENCE);
        $task4 = $this->getReference(TasksFixtures::TASK_4_REFERENCE);
        $task5 = $this->getReference(TasksFixtures::TASK_5_REFERENCE);
        $task6 = $this->getReference(TasksFixtures::TASK_6_REFERENCE);
        $task7 = $this->getReference(TasksFixtures::TASK_7_REFERENCE);
       
        // Création d'une nouvelle checklist
        $checklist = new Checklists(); // Correction du nom de la classe Checklists
        $checklist->setTitle('Maintenance préventive');

        // Ajout des tâches à la checklist
        $checklist->addTask($task1);
        $checklist->addTask($task2);
        $checklist->addTask($task3);
        $checklist->addTask($task7);

        // Persist et flush
        $manager->persist($checklist);

        // Création d'une nouvelle checklist
        $checklist2 = new Checklists(); // Correction du nom de la classe Checklists
        $checklist2->setTitle('Vérification de sécurité'); // Correction du titre

        // Ajout des tâches à la checklist
        $checklist2->addTask($task4);
        $checklist2->addTask($task5);
        $checklist2->addTask($task6);
        $checklist2->addTask($task7);


        // Persist et flush
        $manager->persist($checklist2);

        // Définition des références pour les tâches
        $this->addReference(self::CHECKLISTS_1_REFERENCE, $checklist);
        $this->addReference(self::CHECKLISTS_2_REFERENCE, $checklist2);

        $manager->flush();
    }
}
