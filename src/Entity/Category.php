<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame CategoryRepository – saugyklą kategorijų duomenų bazės operacijoms
use App\Repository\CategoryRepository;
// Importuojame ArrayCollection – Doctrine kolekcijos tipą
use Doctrine\Common\Collections\ArrayCollection;
// Importuojame Collection sąsają – tipas kolekcijų grąžinimui
use Doctrine\Common\Collections\Collection;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su CategoryRepository
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
// Kategorijos esybė – atitinka 'category' lentelę duomenų bazėje
class Category
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Kategorijos unikalus identifikatorius

    // Kategorijos pavadinimas – privalomas, iki 100 simbolių
    #[ORM\Column(length: 100)]
    private ?string $name = null; // Pvz., „Pasakos", „Nuotykiai", „Mokslas"

    // Kategorijos spalvos kodas – neprivalomas, iki 7 simbolių (pvz., #FF6B9D)
    #[ORM\Column(length: 7, nullable: true)]
    private ?string $color = null; // HEX spalvos kodas, naudojamas UI dizaine

    /** @var Collection<int, Book> Knygų, priklausančių šiai kategorijai, kolekcija */
    // Ryšys: viena kategorija turi daug knygų (OneToMany)
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'category')]
    private Collection $books; // Šios kategorijos knygų sąrašas

    // Konstruktorius – inicializuoja knygų kolekciją
    public function __construct()
    {
        $this->books = new ArrayCollection(); // Sukuriame tuščią knygų kolekciją
    }

    // Grąžina kategorijos ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina kategorijos pavadinimą
    public function getName(): ?string
    {
        return $this->name;
    }

    // Nustato kategorijos pavadinimą
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this; // Fluent interface
    }

    // Grąžina kategorijos spalvos kodą
    public function getColor(): ?string
    {
        return $this->color;
    }

    // Nustato kategorijos spalvos kodą
    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    /** @return Collection<int, Book> Grąžina šios kategorijos knygas */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    // Konvertuoja objektą į tekstą (naudojama formų select laukuose)
    public function __toString(): string
    {
        return $this->name ?? ''; // Grąžina kategorijos pavadinimą arba tuščią eilutę
    }
}
