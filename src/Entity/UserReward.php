<?php

namespace App\Entity;

use App\Repository\UserRewardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRewardRepository::class)]
class UserReward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRewards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Reward::class, inversedBy: 'userRewards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reward $reward = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $claimedAt = null;

    public function __construct()
    {
        $this->claimedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    public function setReward(?Reward $reward): static
    {
        $this->reward = $reward;
        return $this;
    }

    public function getClaimedAt(): ?\DateTimeImmutable
    {
        return $this->claimedAt;
    }

    public function setClaimedAt(\DateTimeImmutable $claimedAt): static
    {
        $this->claimedAt = $claimedAt;
        return $this;
    }
}
