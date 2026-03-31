<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'integer')]
    private int $requiredPoints = 0;

    /** @var Collection<int, UserBadge> */
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'badge', orphanRemoval: true)]
    private Collection $userBadges;

    public function __construct()
    {
        $this->userBadges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function getRequiredPoints(): int
    {
        return $this->requiredPoints;
    }

    public function setRequiredPoints(int $requiredPoints): static
    {
        $this->requiredPoints = $requiredPoints;
        return $this;
    }

    /** @return Collection<int, UserBadge> */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }
}
