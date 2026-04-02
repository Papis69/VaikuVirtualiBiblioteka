<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame RewardRepository – saugyklą prizų duomenų bazės operacijoms
use App\Repository\RewardRepository;
// Importuojame ArrayCollection – Doctrine kolekcijos tipą
use Doctrine\Common\Collections\ArrayCollection;
// Importuojame Collection sąsają
use Doctrine\Common\Collections\Collection;
// Importuojame Types – Doctrine duomenų tipų konstantas
use Doctrine\DBAL\Types\Types;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su RewardRepository
#[ORM\Entity(repositoryClass: RewardRepository::class)]
// Prizo esybė – atitinka 'reward' lentelę duomenų bazėje
class Reward
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Prizo unikalus identifikatorius

    // Prizo pavadinimas – privalomas, iki 255 simbolių
    #[ORM\Column(length: 255)]
    private ?string $name = null; // Pvz., „Spalvinimo knygelė", „Lipdukai"

    // Prizo aprašymas – ilgas tekstas, neprivalomas
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null; // Detalesnis prizo aprašymas

    // Prizo paveikslėlio URL – neprivalomas, iki 255 simbolių
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null; // Nuoroda į prizo nuotrauką

    // Prizo kaina taškais – kiek taškų reikia sumokėti už prizą
    #[ORM\Column(type: 'integer')]
    private int $costInPoints = 0; // Pvz., 30 taškų

    // Prizo likutis – kiek prizų dar galima įsigyti
    #[ORM\Column(type: 'integer')]
    private int $stock = 0; // Pvz., 10 vnt.

    /** @var Collection<int, UserReward> Vartotojų, įsigijusių šį prizą, kolekcija */
    // Ryšys: vienas prizas gali būti įsigytas daugelio vartotojų
    #[ORM\OneToMany(targetEntity: UserReward::class, mappedBy: 'reward', orphanRemoval: true)]
    private Collection $userRewards; // Visi vartotojai, kurie įsigijo šį prizą

    // Konstruktorius – inicializuoja vartotojų prizų kolekciją
    public function __construct()
    {
        $this->userRewards = new ArrayCollection(); // Sukuriame tuščią kolekciją
    }

    // Grąžina prizo ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina prizo pavadinimą
    public function getName(): ?string
    {
        return $this->name;
    }

    // Nustato prizo pavadinimą
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Grąžina prizo aprašymą
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Nustato prizo aprašymą
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Grąžina prizo paveikslėlio URL
    public function getImage(): ?string
    {
        return $this->image;
    }

    // Nustato prizo paveikslėlio URL
    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    // Grąžina prizo kainą taškais
    public function getCostInPoints(): int
    {
        return $this->costInPoints;
    }

    // Nustato prizo kainą taškais
    public function setCostInPoints(int $costInPoints): static
    {
        $this->costInPoints = $costInPoints;
        return $this;
    }

    // Grąžina prizo likutį (kiek dar yra sandėlyje)
    public function getStock(): int
    {
        return $this->stock;
    }

    // Nustato prizo likutį
    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    /** @return Collection<int, UserReward> Grąžina visus vartotojus, įsigijusius šį prizą */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }
}
