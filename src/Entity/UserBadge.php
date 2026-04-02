<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame UserBadgeRepository – saugyklą vartotojų ženkliukų operacijoms
use App\Repository\UserBadgeRepository;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su UserBadgeRepository
#[ORM\Entity(repositoryClass: UserBadgeRepository::class)]
// Tarpinė lentelė tarp User ir Badge – saugo, kokie ženkliukai suteikti vartotojui
class UserBadge
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Įrašo unikalus identifikatorius

    // Ryšys: daug UserBadge priklauso vienam vartotojui (ManyToOne)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userBadges')]
    #[ORM\JoinColumn(nullable: false)] // Vartotojas privalomas
    private ?User $user = null; // Vartotojas, kuris gavo ženkliuką

    // Ryšys: daug UserBadge priklauso vienam ženkliukui (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Badge::class, inversedBy: 'userBadges')]
    #[ORM\JoinColumn(nullable: false)] // Ženkliukas privalomas
    private ?Badge $badge = null; // Suteiktas ženkliukas

    // Ženkliuko suteikimo data – nekeičiama datos reikšmė
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $awardedAt = null; // Kada ženkliukas buvo suteiktas

    // Konstruktorius – nustato suteikimo datą dabartinei datai
    public function __construct()
    {
        $this->awardedAt = new \DateTimeImmutable(); // Automatiškai nustatoma dabartinė data
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

    // Grąžina ženkliuką
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    // Nustato ženkliuką
    public function setBadge(?Badge $badge): static
    {
        $this->badge = $badge;
        return $this;
    }

    // Grąžina ženkliuko suteikimo datą
    public function getAwardedAt(): ?\DateTimeImmutable
    {
        return $this->awardedAt;
    }

    // Nustato ženkliuko suteikimo datą
    public function setAwardedAt(\DateTimeImmutable $awardedAt): static
    {
        $this->awardedAt = $awardedAt;
        return $this;
    }
}
