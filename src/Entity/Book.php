<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame BookRepository – saugyklą knygų duomenų bazės operacijoms
use App\Repository\BookRepository;
// Importuojame Types – Doctrine duomenų tipų konstantas (pvz., TEXT)
use Doctrine\DBAL\Types\Types;
// Importuojame ORM Mapping – Doctrine anotacijas duomenų bazės lentelių susiejimui
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su BookRepository
#[ORM\Entity(repositoryClass: BookRepository::class)]
// Knygos esybė – atitinka 'book' lentelę duomenų bazėje
class Book
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Knygos unikalus identifikatorius

    // Knygos pavadinimas – privalomas laukas, iki 255 simbolių
    #[ORM\Column(length: 255)]
    private ?string $title = null; // Pvz., „Raudonkepuraitė"

    // Knygos autorius – privalomas laukas, iki 255 simbolių
    #[ORM\Column(length: 255)]
    private ?string $author = null; // Pvz., „Broliai Grimm"

    // Knygos aprašymas – ilgas tekstas, neprivalomas
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null; // Trumpas knygos turinio aprašymas

    // Minimalus rekomenduojamas amžius – neprivalomas sveikasis skaičius
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minAge = null; // Pvz., 4 (metai)

    // Maksimalus rekomenduojamas amžius – neprivalomas sveikasis skaičius
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxAge = null; // Pvz., 8 (metai)

    // Viršelio nuotraukos URL – neprivalomas, iki 255 simbolių
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null; // Nuoroda į viršelio paveikslėlį

    // Turinio URL (PDF arba audio nuoroda) – neprivalomas, iki 500 simbolių
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $contentUrl = null; // Nuoroda į knygos turinį (PDF/audio)

    // Ryšys: daug knygų priklauso vienai kategorijai (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: true)] // Kategorija neprivaloma
    private ?Category $category = null; // Knygos kategorija (pvz., Pasakos, Nuotykiai)

    // Knygos sukūrimo data – nekeičiama datos reikšmė
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null; // Kada knyga buvo pridėta į sistemą

    // Konstruktorius – nustato sukūrimo datą dabartinei datai
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Automatiškai nustatoma dabartinė data
    }

    // Grąžina knygos ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina knygos pavadinimą
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Nustato knygos pavadinimą
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this; // Fluent interface – leidžia grandininius kvietimus
    }

    // Grąžina knygos autorių
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    // Nustato knygos autorių
    public function setAuthor(string $author): static
    {
        $this->author = $author;
        return $this;
    }

    // Grąžina knygos aprašymą
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Nustato knygos aprašymą (gali būti null)
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Grąžina minimalų rekomenduojamą amžių
    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    // Nustato minimalų rekomenduojamą amžių
    public function setMinAge(?int $minAge): static
    {
        $this->minAge = $minAge;
        return $this;
    }

    // Grąžina maksimalų rekomenduojamą amžių
    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    // Nustato maksimalų rekomenduojamą amžių
    public function setMaxAge(?int $maxAge): static
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    // Grąžina viršelio paveikslėlio URL
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    // Nustato viršelio paveikslėlio URL
    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;
        return $this;
    }

    // Grąžina turinio URL (PDF arba audio)
    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    // Nustato turinio URL
    public function setContentUrl(?string $contentUrl): static
    {
        $this->contentUrl = $contentUrl;
        return $this;
    }

    // Grąžina knygos kategoriją
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    // Nustato knygos kategoriją
    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    // Grąžina knygos sukūrimo datą
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Nustato knygos sukūrimo datą
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
