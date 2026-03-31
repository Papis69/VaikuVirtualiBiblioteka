<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Šis el. paštas jau užregistruotas')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    /** @var list<string> */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $points = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, UserMission> */
    #[ORM\OneToMany(targetEntity: UserMission::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userMissions;

    /** @var Collection<int, UserBadge> */
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userBadges;

    /** @var Collection<int, UserReward> */
    #[ORM\OneToMany(targetEntity: UserReward::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userRewards;

    public function __construct()
    {
        $this->userMissions = new ArrayCollection();
        $this->userBadges = new ArrayCollection();
        $this->userRewards = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;
        return $this;
    }

    public function addPoints(int $points): static
    {
        $this->points += $points;
        return $this;
    }

    public function removePoints(int $points): static
    {
        $this->points -= $points;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @return Collection<int, UserMission> */
    public function getUserMissions(): Collection
    {
        return $this->userMissions;
    }

    /** @return Collection<int, UserBadge> */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }

    /** @return Collection<int, UserReward> */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }
}
