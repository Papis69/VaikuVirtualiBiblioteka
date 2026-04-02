<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame BadgeRepository – saugyklą ženkliukų duomenų bazės operacijoms
use App\Repository\BadgeRepository;
// Importuojame ArrayCollection – Doctrine kolekcijos tipą
use Doctrine\Common\Collections\ArrayCollection;
// Importuojame Collection sąsają
use Doctrine\Common\Collections\Collection;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su BadgeRepository
#[ORM\Entity(repositoryClass: BadgeRepository::class)]
// Ženkliuko esybė – atitinka 'badge' lentelę duomenų bazėje
class Badge
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Ženkliuko unikalus identifikatorius

    // Ženkliuko pavadinimas – privalomas, iki 150 simbolių
    #[ORM\Column(length: 150)]
    private ?string $name = null; // Pvz., „Pradedantysis", „Skaitytojas"

    // Ženkliuko aprašymas – neprivalomas, iki 255 simbolių
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null; // Pvz., „Surinkote pirmuosius 10 taškų!"

    // Ženkliuko ikona – neprivaloma, iki 100 simbolių (emoji arba CSS klasė)
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null; // Pvz., „📖", „⭐", „🏆"

    // Reikalingas taškų skaičius – kiek taškų reikia surinkti, kad gautum šį ženkliuką
    #[ORM\Column(type: 'integer')]
    private int $requiredPoints = 0; // Pvz., 10, 50, 100

    /** @var Collection<int, UserBadge> Vartotojų, gavusių šį ženkliuką, kolekcija */
    // Ryšys: vienas ženkliukas gali būti suteiktas daugeliui vartotojų
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'badge', orphanRemoval: true)]
    private Collection $userBadges; // Visi vartotojai, kurie gavo šį ženkliuką

    // Konstruktorius – inicializuoja vartotojų ženkliukų kolekciją
    public function __construct()
    {
        $this->userBadges = new ArrayCollection(); // Sukuriame tuščią kolekciją
    }

    // Grąžina ženkliuko ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina ženkliuko pavadinimą
    public function getName(): ?string
    {
        return $this->name;
    }

    // Nustato ženkliuko pavadinimą
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Grąžina ženkliuko aprašymą
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Nustato ženkliuko aprašymą
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Grąžina ženkliuko ikoną (emoji)
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    // Nustato ženkliuko ikoną
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    // Grąžina reikalingą taškų skaičių ženkliukui gauti
    public function getRequiredPoints(): int
    {
        return $this->requiredPoints;
    }

    // Nustato reikalingą taškų skaičių
    public function setRequiredPoints(int $requiredPoints): static
    {
        $this->requiredPoints = $requiredPoints;
        return $this;
    }

    /** @return Collection<int, UserBadge> Grąžina visus vartotojus, gavusius šį ženkliuką */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }
}
