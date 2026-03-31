<?php

namespace App\Entity;

use App\Repository\RewardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RewardRepository::class)]
class Reward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'integer')]
    private int $costInPoints = 0;

    #[ORM\Column(type: 'integer')]
    private int $stock = 0;

    /** @var Collection<int, UserReward> */
    #[ORM\OneToMany(targetEntity: UserReward::class, mappedBy: 'reward', orphanRemoval: true)]
    private Collection $userRewards;

    public function __construct()
    {
        $this->userRewards = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCostInPoints(): int
    {
        return $this->costInPoints;
    }

    public function setCostInPoints(int $costInPoints): static
    {
        $this->costInPoints = $costInPoints;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    /** @return Collection<int, UserReward> */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }
}
