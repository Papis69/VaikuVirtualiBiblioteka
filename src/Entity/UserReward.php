<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame UserRewardRepository – saugyklą vartotojų prizų operacijoms
use App\Repository\UserRewardRepository;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su UserRewardRepository
#[ORM\Entity(repositoryClass: UserRewardRepository::class)]
// Tarpinė lentelė tarp User ir Reward – saugo, kokie prizai įsigyti vartotojo
class UserReward
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Įrašo unikalus identifikatorius

    // Ryšys: daug UserReward priklauso vienam vartotojui (ManyToOne)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRewards')]
    #[ORM\JoinColumn(nullable: false)] // Vartotojas privalomas
    private ?User $user = null; // Vartotojas, kuris įsigijo prizą

    // Ryšys: daug UserReward priklauso vienam prizui (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Reward::class, inversedBy: 'userRewards')]
    #[ORM\JoinColumn(nullable: false)] // Prizas privalomas
    private ?Reward $reward = null; // Įsigytas prizas

    // Prizo įsigijimo data – nekeičiama datos reikšmė
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $claimedAt = null; // Kada prizas buvo įsigytas

    // Konstruktorius – nustato įsigijimo datą dabartinei datai
    public function __construct()
    {
        $this->claimedAt = new \DateTimeImmutable(); // Automatiškai nustatoma dabartinė data
    }

    // Grąžina įrašo ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina vartotoją
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Nustato vartotoją
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Grąžina prizą
    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    // Nustato prizą
    public function setReward(?Reward $reward): static
    {
        $this->reward = $reward;
        return $this;
    }

    // Grąžina prizo įsigijimo datą
    public function getClaimedAt(): ?\DateTimeImmutable
    {
        return $this->claimedAt;
    }

    // Nustato prizo įsigijimo datą
    public function setClaimedAt(\DateTimeImmutable $claimedAt): static
    {
        $this->claimedAt = $claimedAt;
        return $this;
    }
}
