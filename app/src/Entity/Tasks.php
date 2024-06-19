<?php

namespace App\Entity;

use App\Repository\TasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateInterval;

#[ORM\Entity(repositoryClass: TasksRepository::class)]
class Tasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title;

    #[ORM\Column]
    private ?\DateInterval $duration;

    #[ORM\Column]
    private ?bool $archived = false;

    /**
     * @var Collection<int, Checklists>
     */
    #[ORM\ManyToMany(targetEntity: Checklists::class, mappedBy: 'tasks')]
    private Collection $checklists;

    public function __construct()
    {
        $this->checklists = new ArrayCollection();
        $this->duration = new DateInterval('P0Y0M0DT0H0M0S');
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

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(\DateInterval $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection<int, Checklists>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklists $checklist): static
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
            $checklist->addTask($this);
        }

        return $this;
    }

    public function removeChecklist(Checklists $checklist): static
    {
        if ($this->checklists->removeElement($checklist)) {
            $checklist->removeTask($this);
        }

        return $this;
    }
}
