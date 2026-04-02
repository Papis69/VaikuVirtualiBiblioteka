<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Entity paketui
namespace App\Entity;

// Importuojame UserRepository – saugyklą, kuri atlieka duomenų bazės užklausas vartotojams
use App\Repository\UserRepository;
// Importuojame ArrayCollection – Doctrine kolekcijos tipą (veikia kaip masyvas)
use Doctrine\Common\Collections\ArrayCollection;
// Importuojame Collection sąsają – tipas kolekcijų grąžinimui
use Doctrine\Common\Collections\Collection;
// Importuojame ORM Mapping – Doctrine anotacijas duomenų bazės lentelių susiejimui
use Doctrine\ORM\Mapping as ORM;
// Importuojame UniqueEntity – validacijos taisyklę, kad el. paštas būtų unikalus
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// Importuojame PasswordAuthenticatedUserInterface – sąsaja slaptažodžio autentifikacijai
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
// Importuojame UserInterface – pagrindinė Symfony vartotojo sąsaja
use Symfony\Component\Security\Core\User\UserInterface;

// Nurodome, kad ši klasė yra Doctrine esybė (entity), susijusi su UserRepository saugykla
#[ORM\Entity(repositoryClass: UserRepository::class)]
// Nurodome duomenų bazės lentelės pavadinimą (kabutės apsaugo nuo SQL raktinio žodžio konflikto)
#[ORM\Table(name: '`user`')]
// Validacijos taisyklė: el. pašto laukas turi būti unikalus, kitu atveju rodomas klaidos pranešimas
#[UniqueEntity(fields: ['email'], message: 'Šis el. paštas jau užregistruotas')]
// Pagrindinė User klasė, kuri įgyvendina UserInterface (prisijungimui) ir PasswordAuthenticatedUserInterface (slaptažodžiui)
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Pirminis raktas (ID) – automatiškai generuojamas duomenų bazėje
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Vartotojo ID (pradžioje null, nes dar nėra išsaugotas)

    // El. pašto laukas – unikalus, maksimalus ilgis 180 simbolių
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null; // Vartotojo el. pašto adresas

    // Vartotojo vardo laukas – maksimalus ilgis 100 simbolių
    #[ORM\Column(length: 100)]
    private ?string $username = null; // Vartotojo vardas (rodomas svetainėje)

    /** @var list<string> Rolių masyvas (pvz., ROLE_USER, ROLE_ADMIN) */
    #[ORM\Column]
    private array $roles = []; // Vartotojo rolės (tuščias masyvas pagal nutylėjimą)

    // Slaptažodžio laukas – saugomas užšifruotas (hash'uotas) slaptažodis
    #[ORM\Column]
    private ?string $password = null; // Hash'uotas slaptažodis

    // Taškų laukas – sveikasis skaičius, numatytoji reikšmė 0
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $points = 0; // Vartotojo surinktų taškų skaičius

    // Registracijos data – nekeičiama (immutable) datos reikšmė
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null; // Kada vartotojas užsiregistravo

    /** @var Collection<int, UserMission> Vartotojo misijų kolekcija */
    // Ryšys: vienas vartotojas turi daug UserMission įrašų. orphanRemoval = ištrinami „našlaičiai"
    #[ORM\OneToMany(targetEntity: UserMission::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userMissions; // Kolekcija vartotojo atliktų/pateiktų misijų

    /** @var Collection<int, UserBadge> Vartotojo ženkliukų kolekcija */
    // Ryšys: vienas vartotojas turi daug UserBadge įrašų
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userBadges; // Kolekcija vartotojo gautų ženkliukų

    /** @var Collection<int, UserReward> Vartotojo prizų kolekcija */
    // Ryšys: vienas vartotojas turi daug UserReward įrašų
    #[ORM\OneToMany(targetEntity: UserReward::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userRewards; // Kolekcija vartotojo įsigytų prizų

    // Konstruktorius – inicializuoja kolekcijas ir nustato registracijos datą
    public function __construct()
    {
        $this->userMissions = new ArrayCollection(); // Sukuriame tuščią misijų kolekciją
        $this->userBadges = new ArrayCollection();   // Sukuriame tuščią ženkliukų kolekciją
        $this->userRewards = new ArrayCollection();  // Sukuriame tuščią prizų kolekciją
        $this->createdAt = new \DateTimeImmutable();  // Nustatome dabartinę datą kaip registracijos datą
    }

    // Grąžina vartotojo ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Grąžina vartotojo el. paštą
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Nustato vartotojo el. paštą ir grąžina patį objektą (fluent interface)
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this; // Grąžina $this, kad galima būtų grandinėmis kviesti metodus
    }

    // Grąžina vartotojo vardą
    public function getUsername(): ?string
    {
        return $this->username;
    }

    // Nustato vartotojo vardą
    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    // Grąžina vartotojo identifikatorių (Symfony naudoja prisijungimui – čia el. paštas)
    public function getUserIdentifier(): string
    {
        return (string) $this->email; // Konvertuojame į string, kad nebūtų null
    }

    /** @return list<string> Grąžina vartotojo roles */
    public function getRoles(): array
    {
        $roles = $this->roles;       // Paimame esamas roles
        $roles[] = 'ROLE_USER';      // Pridedame bazinę ROLE_USER rolę (visi vartotojai ją turi)
        return array_unique($roles); // Grąžiname unikalias roles (be dublikatų)
    }

    /** @param list<string> $roles Nustato vartotojo roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Grąžina hash'uotą slaptažodį
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Nustato hash'uotą slaptažodį
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Symfony reikalauja šio metodo – ištrina laikinus slaptažodžio duomenis iš atminties
    public function eraseCredentials(): void
    {
        // Čia galima išvalyti laikinai saugomus jautrius duomenis, pvz. $this->plainPassword = null
    }

    // Grąžina vartotojo taškų skaičių
    public function getPoints(): int
    {
        return $this->points;
    }

    // Nustato vartotojo taškų skaičių (perrašo esamą reikšmę)
    public function setPoints(int $points): static
    {
        $this->points = $points;
        return $this;
    }

    // Prideda taškus prie esamų (pvz., kai misija patvirtinta)
    public function addPoints(int $points): static
    {
        $this->points += $points; // Pridedame prie esamos sumos
        return $this;
    }

    // Atima taškus nuo esamų (pvz., kai perkamas prizas)
    public function removePoints(int $points): static
    {
        $this->points -= $points; // Atimame nuo esamos sumos
        return $this;
    }

    // Grąžina registracijos datą
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Nustato registracijos datą
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @return Collection<int, UserMission> Grąžina visas vartotojo misijas */
    public function getUserMissions(): Collection
    {
        return $this->userMissions;
    }

    /** @return Collection<int, UserBadge> Grąžina visus vartotojo ženkliukus */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }

    /** @return Collection<int, UserReward> Grąžina visus vartotojo prizus */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }
}
