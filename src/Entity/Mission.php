<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame MissionRepository – saugyklą misijų duomenų bazės operacijoms
use App\Repository\MissionRepository;
// Importuojame ArrayCollection – Doctrine kolekcijos tipą
use Doctrine\Common\Collections\ArrayCollection;
// Importuojame Collection sąsają
use Doctrine\Common\Collections\Collection;
// Importuojame Types – Doctrine duomenų tipų konstantas
use Doctrine\DBAL\Types\Types;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su MissionRepository
#[ORM\Entity(repositoryClass: MissionRepository::class)]
// Misijos esybė – atitinka 'mission' lentelę duomenų bazėje
class Mission
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Misijos unikalus identifikatorius

    // Misijos pavadinimas – privalomas, iki 255 simbolių
    #[ORM\Column(length: 255)]
    private ?string $title = null; // Pvz., „Perskaityk pirmą knygą"

    // Misijos aprašymas – ilgas tekstas, neprivalomas
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null; // Detalesnis misijos aprašymas

    // Taškų atlygis už misijos atlikimą – sveikasis skaičius
    #[ORM\Column(type: 'integer')]
    private int $rewardPoints = 0; // Kiek taškų gaunama už misijos atlikimą

    // Misijos tipas – neprivalomas, iki 50 simbolių (pvz., „skaitymas", „kūryba")
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null; // Misijos kategorija/tipas

    /** @var Collection<int, UserMission> Vartotojų misijų įrašų kolekcija */
    // Ryšys: viena misija gali būti atlikta daugelio vartotojų (OneToMany)
    #[ORM\OneToMany(targetEntity: UserMission::class, mappedBy: 'mission', orphanRemoval: true)]
    private Collection $userMissions; // Visi vartotojai, kurie šią misiją atliko/pateikė

    // Konstruktorius – inicializuoja vartotojų misijų kolekciją
    public function __construct()
    {
        $this->userMissions = new ArrayCollection(); // Sukuriame tuščią kolekciją
    }

    // Grąžina misijos ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina misijos pavadinimą
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Nustato misijos pavadinimą
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Grąžina misijos aprašymą
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Nustato misijos aprašymą
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Grąžina taškų atlygį
    public function getRewardPoints(): int
    {
        return $this->rewardPoints;
    }

    // Nustato taškų atlygį
    public function setRewardPoints(int $rewardPoints): static
    {
        $this->rewardPoints = $rewardPoints;
        return $this;
    }

    // Grąžina misijos tipą
    public function getType(): ?string
    {
        return $this->type;
    }

    // Nustato misijos tipą
    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /** @return Collection<int, UserMission> Grąžina visus vartotojų misijos įrašus */
    public function getUserMissions(): Collection
    {
        return $this->userMissions;
    }
}
