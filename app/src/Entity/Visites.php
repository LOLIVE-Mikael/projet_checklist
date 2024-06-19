<?php

namespace App\Entity;

use App\Repository\VisitesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitesRepository::class)]
class Visites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Sites $site;

    #[ORM\ManyToOne]
    private ?Users $user;

    #[ORM\ManyToOne]
    private ?Checklists $checklist;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Sites
    {
        return $this->site;
    }

    public function setSite(?Sites $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $User): static
    {
        $this->user = $User;

        return $this;
    }

    public function getChecklist(): ?Checklists
    {
        return $this->checklist;
    }

    public function setChecklist(?Checklists $checklist): static
    {
        $this->checklist = $checklist;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
