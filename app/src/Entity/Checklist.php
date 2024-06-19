<?php

namespace App\Entity;

use App\Repository\ChecklistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateInterval;
use DateTime;

#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
class Checklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'checklist', cascade: ["persist"])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        $this->tasks->removeElement($task);

        return $this;
    }

    public function getDuration(): \DateInterval
    {
		// pour ajouter des DateInterval, il faut :
		//			- créer deux dates (DateTime) identiques
		//			- ajouter les différents DateInterval à l'une des dates
		//			- regarder l'écart entre les deux dates.
		
		$ref = new DateTime('00:00');
		$total = clone $ref;
		
        foreach ($this->getTasks() as $task) {
			if (!$task->isArchived()){
				$total->add($task->getDuration());
			}
        }
		$totalDuration = $total->diff($ref);
		
        return $totalDuration;
    }


}
