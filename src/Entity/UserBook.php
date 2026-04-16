<?php
// Vardų erdvė – esybių paketas
namespace App\Entity;

// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// UserBook esybė – fiksuoja, kad vartotojas perskaitė knygą
// Atitinka 'user_book' lentelę duomenų bazėje
#[ORM\Entity]
#[ORM\Table(name: 'user_book')]
#[ORM\UniqueConstraint(columns: ['user_id', 'book_id'])]
class UserBook
{
    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Ryšys su vartotoju (ManyToOne – daug UserBook priklauso vienam User)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Ryšys su knyga (ManyToOne – daug UserBook priklauso vienai Book)
    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    // Data, kada knyga buvo pažymėta kaip perskaityta
    #[ORM\Column]
    private ?\DateTimeImmutable $readAt = null;

    // Konstruktorius – automatiškai nustato datą
    public function __construct()
    {
        $this->readAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getBook(): ?Book { return $this->book; }
    public function setBook(?Book $book): static { $this->book = $book; return $this; }

    public function getReadAt(): ?\DateTimeImmutable { return $this->readAt; }
    public function setReadAt(\DateTimeImmutable $readAt): static { $this->readAt = $readAt; return $this; }
}
