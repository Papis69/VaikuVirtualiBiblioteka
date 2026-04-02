<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame UserMissionRepository – saugyklą vartotojų misijų operacijoms
use App\Repository\UserMissionRepository;
// Importuojame ORM Mapping – Doctrine anotacijas
use Doctrine\ORM\Mapping as ORM;

// Nurodome, kad ši klasė yra Doctrine esybė, susijusi su UserMissionRepository
#[ORM\Entity(repositoryClass: UserMissionRepository::class)]
// Tarpinė lentelė tarp User ir Mission – saugo vartotojo misijos atlikimo informaciją
class UserMission
{
    // Statuso konstantos – naudojamos vietoj „magiškų" tekstinių reikšmių
    public const STATUS_PENDING = 'pending';    // Laukia administratoriaus peržiūros
    public const STATUS_APPROVED = 'approved';  // Administratorius patvirtino misiją
    public const STATUS_REJECTED = 'rejected';  // Administratorius atmetė misiją

    // Pirminis raktas (ID) – automatiškai generuojamas
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Įrašo unikalus identifikatorius

    // Ryšys: daug UserMission priklauso vienam vartotojui (ManyToOne)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userMissions')]
    #[ORM\JoinColumn(nullable: false)] // Vartotojas privalomas
    private ?User $user = null; // Vartotojas, kuris atliko/pateikė misiją

    // Ryšys: daug UserMission priklauso vienai misijai (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Mission::class, inversedBy: 'userMissions')]
    #[ORM\JoinColumn(nullable: false)] // Misija privaloma
    private ?Mission $mission = null; // Misija, kurią vartotojas atlieka

    // Ar misija atlikta – boolean laukas
    #[ORM\Column(type: 'boolean')]
    private bool $isCompleted = false; // true, kai misija patvirtinta administratoriaus

    // Misijos pateikimo/atlikimo data – neprivaloma
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt = null; // Kada vartotojas pateikė misiją

    // Įrodymo tekstas – vartotojo parašytas įrodymas apie misijos atlikimą
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $proofText = null; // Pvz., „Perskaičiau knygą Raudonkepuraitė..."

    // Misijos statusas – naudoja konstantas (pending/approved/rejected)
    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    private string $status = self::STATUS_PENDING; // Numatytasis statusas: laukia peržiūros

    // Peržiūros data – kada administratorius peržiūrėjo misiją
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null; // Peržiūros data ir laikas

    // Atmetimo priežastis – neprivaloma, užpildoma tik kai misija atmesta
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rejectionReason = null; // Kodėl administratorius atmetė misiją

    // Grąžina įrašo ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina vartotoją, kuris pateikė misiją
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

    // Grąžina misiją
    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    // Nustato misiją
    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;
        return $this;
    }

    // Patikrina, ar misija yra patvirtinta (naudoja statusą, ne isCompleted lauką)
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_APPROVED; // Atlikta = patvirtinta
    }

    // Nustato isCompleted reikšmę
    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    // Grąžina misijos pateikimo datą
    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    // Nustato misijos pateikimo datą
    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    // Grąžina vartotojo įrodymo tekstą
    public function getProofText(): ?string
    {
        return $this->proofText;
    }

    // Nustato vartotojo įrodymo tekstą
    public function setProofText(?string $proofText): static
    {
        $this->proofText = $proofText;
        return $this;
    }

    // Grąžina dabartinį misijos statusą
    public function getStatus(): string
    {
        return $this->status;
    }

    // Nustato misijos statusą (pending/approved/rejected)
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    // Patikrina, ar misija laukia peržiūros
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Patikrina, ar misija yra patvirtinta
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // Patikrina, ar misija yra atmesta
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Grąžina peržiūros datą
    public function getReviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    // Nustato peržiūros datą
    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): static
    {
        $this->reviewedAt = $reviewedAt;
        return $this;
    }

    // Grąžina atmetimo priežastį
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    // Nustato atmetimo priežastį
    public function setRejectionReason(?string $rejectionReason): static
    {
        $this->rejectionReason = $rejectionReason;
        return $this;
    }
}
