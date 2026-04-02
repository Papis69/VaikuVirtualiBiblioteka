<?php
// Vardų erdvė – duomenų pradinių duomenų (fixtures) paketas
namespace App\DataFixtures;

// Importuojame visas esybes, kurioms kursime pradinius duomenis
use App\Entity\Badge;    // Ženkliukų esybė
use App\Entity\Book;     // Knygų esybė
use App\Entity\Category; // Kategorijų esybė
use App\Entity\Mission;  // Misijų esybė
use App\Entity\Reward;   // Prizų esybė
use App\Entity\User;     // Vartotojų esybė
// Importuojame Fixture bazinę klasę – Doctrine DataFixtures pagrindas
use Doctrine\Bundle\FixturesBundle\Fixture;
// Importuojame ObjectManager – Doctrine objektų valdytojas (persist/flush)
use Doctrine\Persistence\ObjectManager;
// Importuojame UserPasswordHasherInterface – slaptažodžių šifravimo sąsaja
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Pradiniai duomenys (Fixtures) – užpildo duomenų bazę testiniais duomenimis.
 * Paleidžiama komanda: php bin/console doctrine:fixtures:load
 */
class AppFixtures extends Fixture
{
    // Konstruktorius – gauname slaptažodžių šifravimo servisą per Dependency Injection
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    // Pagrindinis metodas – čia sukuriami visi pradiniai duomenys
    public function load(ObjectManager $manager): void
    {
        // === Vartotojai ===

        // Sukuriame administratoriaus paskyrą
        $admin = new User();
        $admin->setUsername('Administratorius');                                    // Vardas
        $admin->setEmail('admin@biblioteka.lt');                                   // El. paštas
        $admin->setRoles(['ROLE_ADMIN']);                                           // Admin rolė
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123')); // Šifruojame slaptažodį
        $admin->setPoints(0);                                                       // 0 taškų pradžiale
        $manager->persist($admin);                                                  // Pažymime išsaugojimui

        // Sukuriame paprastą vartotoją
        $user = new User();
        $user->setUsername('Jonas');                                                // Vardas
        $user->setEmail('jonas@biblioteka.lt');                                    // El. paštas
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123')); // Šifruojame slaptažodį
        $user->setPoints(50);                                                       // 50 taškų pradžiai
        $manager->persist($user);

        // === Kategorijos ===

        $categories = []; // Masyvas kategorijų saugojimui (vėliau naudojamas knygoms)
        // Kategorijų duomenys: [pavadinimas, spalvos kodas]
        $catData = [
            ['Pasakos', '#FF6B9D'],     // Rožinė spalva
            ['Nuotykiai', '#6C63FF'],   // Violetinė spalva
            ['Mokslas', '#00D2FF'],     // Žydra spalva
            ['Gamta', '#2ED573'],       // Žalia spalva
            ['Fantastika', '#FFA502'],  // Oranžinė spalva
        ];
        // Sukuriame kiekvieną kategoriją ir išsaugome
        foreach ($catData as [$name, $color]) {
            $cat = new Category();
            $cat->setName($name);       // Nustatome pavadinimą
            $cat->setColor($color);     // Nustatome spalvą
            $manager->persist($cat);    // Pažymime išsaugojimui
            $categories[$name] = $cat;  // Saugome pagal pavadinimą (knygoms priskirti)
        }

        // === Knygos ===

        // Knygų duomenys: [pavadinimas, autorius, aprašymas, kategorija, minAmžius, maxAmžius]
        $booksData = [
            ['Raudonkepuraitė', 'Broliai Grimm', 'Klasikinė pasaka apie drąsią mergaitę.', 'Pasakos', 4, 8],
            ['Peliukas ir jo draugai', 'Autorius A', 'Nuotaikingas pasakojimas apie draugystę.', 'Pasakos', 3, 6],
            ['Džiunglių knyga', 'Rudyard Kipling', 'Berniukas Mauglis auga džiunglėse tarp gyvūnų.', 'Nuotykiai', 6, 12],
            ['Robinzonas Kruzas vaikams', 'Daniel Defoe', 'Supaprastinta versija vaikams.', 'Nuotykiai', 8, 14],
            ['Kosmoso paslaptys', 'Autorius B', 'Sužinok apie planetas ir žvaigždes!', 'Mokslas', 7, 12],
            ['Kaip veikia mašinos', 'Autorius C', 'Technika ir inžinerija vaikams.', 'Mokslas', 8, 14],
            ['Lietuvos miškų gyvūnai', 'Autorius D', 'Pažink Lietuvos gamtą!', 'Gamta', 5, 10],
            ['Augalų pasaulis', 'Autorius E', 'Viskas apie augalus ir gėles.', 'Gamta', 6, 11],
            ['Haris Poteris vaikams', 'J.K. Rowling', 'Magijos pasaulio nuotykiai.', 'Fantastika', 8, 14],
            ['Drakonų sala', 'Autorius F', 'Fantastiniai nuotykiai saloje.', 'Fantastika', 7, 13],
        ];
        // Sukuriame kiekvieną knygą ir priskiriame kategoriją
        foreach ($booksData as [$title, $author, $desc, $catName, $minAge, $maxAge]) {
            $book = new Book();
            $book->setTitle($title);                  // Pavadinimas
            $book->setAuthor($author);                // Autorius
            $book->setDescription($desc);             // Aprašymas
            $book->setCategory($categories[$catName]); // Priskiriame kategoriją iš masyvo
            $book->setMinAge($minAge);                // Minimalus amžius
            $book->setMaxAge($maxAge);                // Maksimalus amžius
            $manager->persist($book);
        }

        // === Misijos ===

        // Misijų duomenys: [pavadinimas, aprašymas, taškai, tipas]
        $missionsData = [
            ['Perskaityk pirmą knygą', 'Pasirink bet kurią knygą ir ją perskaityk!', 10, 'skaitymas'],
            ['Perskaityk 3 knygas', 'Perskaityk bet kurias 3 knygas iš katalogo.', 30, 'skaitymas'],
            ['Perskaityk 5 knygas', 'Perskaityk 5 knygas ir tapk tikru skaitytoju!', 50, 'skaitymas'],
            ['Parašyk atsiliepimą', 'Parašyk trumpą atsiliepimą apie perskaitytą knygą.', 15, 'kūryba'],
            ['Rekomenduok draugui', 'Rekomenduok knygą savo draugui!', 10, 'bendravimas'],
            ['Skaitymo maratonas', 'Skaityk kiekvieną dieną visą savaitę!', 70, 'iššūkis'],
            ['Atrask naują žanrą', 'Perskaityk knygą iš žanro, kurio dar neskaičiai.', 20, 'tyrimas'],
            ['Knygų kirminėlis', 'Perskaityk 10 knygų!', 100, 'skaitymas'],
        ];
        // Sukuriame kiekvieną misiją
        foreach ($missionsData as [$title, $desc, $points, $type]) {
            $mission = new Mission();
            $mission->setTitle($title);          // Pavadinimas
            $mission->setDescription($desc);     // Aprašymas
            $mission->setRewardPoints($points);  // Taškų atlygis
            $mission->setType($type);            // Misijos tipas
            $manager->persist($mission);
        }

        // === Ženkliukai ===

        // Ženkliukų duomenys: [pavadinimas, aprašymas, ikona (emoji), reikalingi taškai]
        $badgesData = [
            ['Pradedantysis', 'Surinkote pirmuosius 10 taškų!', '📖', 10],
            ['Skaitytojas', 'Surinkote 50 taškų!', '⭐', 50],
            ['Knygų draugas', 'Surinkote 100 taškų!', '🏅', 100],
            ['Skaitymo čempionas', 'Surinkote 200 taškų!', '🏆', 200],
            ['Knygų meistras', 'Surinkote 500 taškų!', '👑', 500],
        ];
        // Sukuriame kiekvieną ženkliuką
        foreach ($badgesData as [$name, $desc, $icon, $reqPoints]) {
            $badge = new Badge();
            $badge->setName($name);                 // Pavadinimas
            $badge->setDescription($desc);          // Aprašymas
            $badge->setIcon($icon);                 // Ikona (emoji)
            $badge->setRequiredPoints($reqPoints);  // Reikalingas taškų skaičius
            $manager->persist($badge);
        }

        // === Prizai ===

        // Prizų duomenys: [pavadinimas, aprašymas, nuotraukos URL, kaina taškais, likutis]
        $rewardsData = [
            ['Spalvinimo knygelė', 'Graži spalvinimo knygelė su gyvūnais!', null, 30, 10],
            ['Lipdukai', 'Rinkinys spalvingų lipdukų!', null, 20, 20],
            ['Knyga-dovana', 'Pasirink bet kurią knygą kaip dovaną!', null, 100, 5],
            ['Žaisliukas', 'Mažas žaisliukas-staigmena!', null, 150, 3],
            ['Specialus ženkliukas', 'Unikalus aukso ženkliukas!', null, 200, 2],
        ];
        // Sukuriame kiekvieną prizą
        foreach ($rewardsData as [$name, $desc, $img, $cost, $stock]) {
            $reward = new Reward();
            $reward->setName($name);             // Pavadinimas
            $reward->setDescription($desc);      // Aprašymas
            $reward->setImage($img);             // Paveikslėlio URL (null = nėra)
            $reward->setCostInPoints($cost);     // Kaina taškais
            $reward->setStock($stock);           // Likutis (kiek vnt.)
            $manager->persist($reward);
        }

        // Vykdome visas SQL INSERT užklausas vienu kartu (efektyviau nei po vieną)
        $manager->flush();
    }
}
