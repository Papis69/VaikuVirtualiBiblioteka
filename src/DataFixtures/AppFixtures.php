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

        // Realių knygų duomenys iš Project Gutenberg (public domain)
        // [pavadinimas, autorius, aprašymas, kategorija, minAmžius, maxAmžius, viršelis, skaitymo URL]
        $booksData = [
            // --- Fantastika ---
            [
                'Alisa stebuklų šalyje',
                'Lewis Carroll',
                'Mergaitė Alisa seka baltą triušį į triušio urvą ir patenka į fantastinį pasaulį, pilną keistų būtybių, absurdiškų situacijų ir nepamirštamų nuotykių.',
                'Fantastika', 6, 14,
                'https://www.gutenberg.org/cache/epub/11/pg11.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/11/pg11-images.html',
            ],
            [
                'Alisa veidrodžių karalystėje',
                'Lewis Carroll',
                'Alisa pereina per veidrodį ir atsiduria fantastiškame pasaulyje, kur viskas yra priešingai nei tikrovėje – šachmatų figūros atgyja, o logika veikia kitaip.',
                'Fantastika', 7, 14,
                'https://www.gutenberg.org/cache/epub/12/pg12.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/12/pg12-images.html',
            ],
            [
                'Piteris Penas',
                'J.M. Barrie',
                'Berniukas, kuris niekada neužauga, nuneša Vendę ir jos brolius į Niekados šalį, kur jų laukia nuotykiai su piratais ir fėjomis.',
                'Fantastika', 6, 12,
                'https://www.gutenberg.org/cache/epub/16/pg16.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/16/pg16-images.html',
            ],
            [
                'Ozo šalies burtininkas',
                'L. Frank Baum',
                'Mergaitė Dorotė su šuniuku Totu patenka į stebuklingą Ozo šalį ir leidžiasi į kelionę pas didįjį burtininką kartu su Kaliausiu, Skardžiumi ir Bailiuoju Liūtu.',
                'Fantastika', 6, 12,
                'https://www.gutenberg.org/cache/epub/55/pg55.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/55/pg55-images.html',
            ],
            [
                'Paslaptingas sodas',
                'Frances Hodgson Burnett',
                'Merė Lenoks atranda paslaptingą, užrakintą sodą ir kartu su draugais padeda jam vėl pražysti, tuo pačiu atskleisdama tikrąją draugystės galią.',
                'Fantastika', 7, 12,
                'https://www.gutenberg.org/cache/epub/113/pg113.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/113/pg113-images.html',
            ],
            // --- Pasakos ---
            [
                'Brolių Grimų pasakos',
                'Broliai Grimm',
                'Klasikinis pasakų rinkinys su tokiomis pasakomis kaip Snieguolė, Raudonkepuraitė, Pelenė, Rapuncelė ir daugeliu kitų – visų laikų vaikų mėgstamiausios istorijos.',
                'Pasakos', 4, 12,
                'https://www.gutenberg.org/cache/epub/2591/pg2591.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/2591/pg2591-images.html',
            ],
            [
                'Ezopo pasakėčios',
                'Ezopas',
                'Senovės graikų pasakėčių rinkinys su gyvūnais – kiekviena pasakėčia moko svarbios gyvenimo pamokos apie išmintį, draugystę ir sąžiningumą.',
                'Pasakos', 4, 10,
                'https://www.gutenberg.org/cache/epub/11339/pg11339.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/11339/pg11339-images.html',
            ],
            [
                'Pinokio nuotykiai',
                'Carlo Collodi',
                'Medinis lėlė Pinokis atgyja ir patiria daugybę nuotykių, kol galiausiai savo gerumu ir drąsa nusipelno tapti tikru berniuku.',
                'Pasakos', 5, 12,
                'https://www.gutenberg.org/cache/epub/500/pg500.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/500/pg500-images.html',
            ],
            [
                'Aksominis zuikutis',
                'Margery Williams',
                'Nuostabus pasakojimas apie pliušinį zuikutį, kuris svajoja tapti tikru. Jautri istorija apie meilės galią ir tai, ką reiškia būti tikram.',
                'Pasakos', 4, 8,
                'https://www.gutenberg.org/cache/epub/11757/pg11757.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/11757/pg11757-images.html',
            ],
            [
                'Kalėdų giesmė',
                'Charles Dickens',
                'Šykštuolis Ebenezeris Skrudžas per vieną Kalėdų naktį sulaukia trijų dvasių vizitų, kurie visiškai pakeičia jo požiūrį į gyvenimą ir žmones.',
                'Pasakos', 8, 14,
                'https://www.gutenberg.org/cache/epub/46/pg46.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/46/pg46-images.html',
            ],
            [
                'Mažoji princesė',
                'Frances Hodgson Burnett',
                'Sara Krju – turtingo tėvo dukra, kuri netekusi visko išsaugo kilnumą, vaizduotę ir gerumą net sunkiausiomis aplinkybėmis.',
                'Pasakos', 7, 12,
                'https://www.gutenberg.org/cache/epub/146/pg146.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/146/pg146-images.html',
            ],
            // --- Nuotykiai ---
            [
                'Tomo Sojerio nuotykiai',
                'Mark Twain',
                'Išdykęs berniukas Tomas Sojeris gyvena prie Misisipės upės ir patenka į įvairius nuotykius – nuo tvorų dažymo iki lobių paieškos.',
                'Nuotykiai', 8, 14,
                'https://www.gutenberg.org/cache/epub/74/pg74.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/74/pg74-images.html',
            ],
            [
                'Heklberio Fino nuotykiai',
                'Mark Twain',
                'Hakas Finas pabėga nuo savo tėvo ir kartu su draugu Džimu keliauja plaustu Misisipės upe, patirdamas nepamirštamus nuotykius.',
                'Nuotykiai', 10, 16,
                'https://www.gutenberg.org/cache/epub/76/pg76.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/76/pg76-images.html',
            ],
            [
                'Lobių sala',
                'Robert Louis Stevenson',
                'Jaunas Džimas Hokinsas randa piratų lobio žemėlapį ir leidžiasi į pavojingą kelionę laivu, kur susiduria su klastinguoju Džonu Silveru.',
                'Nuotykiai', 8, 14,
                'https://www.gutenberg.org/cache/epub/120/pg120.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/120/pg120-images.html',
            ],
            [
                'Robinzonas Kruzas',
                'Daniel Defoe',
                'Jūreivis Robinzonas Kruzas po laivo sudužimo atsiduria negyvenamoje saloje ir turi išmokti išgyventi vienas – statydamas pastogę, augindamas maistą.',
                'Nuotykiai', 9, 16,
                'https://www.gutenberg.org/cache/epub/521/pg521.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/521/pg521-images.html',
            ],
            [
                'Gamtos šauksmas',
                'Jack London',
                'Šuo Bekas iš patogaus Kalifornijos gyvenimo patenka į Aliaskos tyrus, kur turi prisitaikyti prie laukinės gamtos ir atrasti savo tikrąją prigimtį.',
                'Nuotykiai', 10, 16,
                'https://www.gutenberg.org/cache/epub/215/pg215.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/215/pg215-images.html',
            ],
            [
                'Anė iš Žaliųjų Stogų',
                'L.M. Montgomery',
                'Našlaitė Anė Šėrlė atvyksta į Žaliuosius Stogus Princo Edvardo saloje ir užkariauja visų širdis savo vaizduote, nuoširdumu ir energija.',
                'Nuotykiai', 8, 14,
                'https://www.gutenberg.org/cache/epub/45/pg45.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/45/pg45-images.html',
            ],
            [
                'Mažosios moterys',
                'Louisa May Alcott',
                'Keturių seserų – Meg, Džo, Bet ir Eimės – augimo, draugystės ir šeimos meilės istorija Amerikos pilietinio karo metais.',
                'Nuotykiai', 9, 16,
                'https://www.gutenberg.org/cache/epub/514/pg514.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/514/pg514-images.html',
            ],
            // --- Gamta ---
            [
                'Džiunglių knyga',
                'Rudyard Kipling',
                'Berniukas Mauglis auga džiunglėse tarp vilkų, meškų ir panterų, mokosi džiunglių dėsnių ir susiduria su grėsmingu tigru Šir Chanu.',
                'Gamta', 7, 14,
                'https://www.gutenberg.org/cache/epub/236/pg236.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/236/pg236-images.html',
            ],
            [
                'Juodoji Gražuolė',
                'Anna Sewell',
                'Žirgo Juodosios Gražuolės gyvenimo istorija, pasakojama paties žirgo lūpomis – apie meilę, skausmą, gerumą ir gyvūnų gerovę.',
                'Gamta', 7, 14,
                'https://www.gutenberg.org/cache/epub/271/pg271.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/271/pg271-images.html',
            ],
            [
                'Vėjas gluosniuose',
                'Kenneth Grahame',
                'Kurmio, Žiurkėno, Rupūžės ir Barsuko nuotykiai paupyje – jaukus ir juokingas pasakojimas apie draugystę ir gamtos grožį.',
                'Gamta', 6, 12,
                'https://www.gutenberg.org/cache/epub/289/pg289.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/289/pg289-images.html',
            ],
            [
                'Heidi',
                'Johanna Spyri',
                'Maža mergaitė Heidi auga pas senelį Alpėse, kur ji atranda gamtos grožį, patiria draugystės džiaugsmą ir moko mus vertinti paprastą gyvenimą.',
                'Gamta', 5, 12,
                'https://www.gutenberg.org/cache/epub/1448/pg1448.cover.medium.jpg',
                'https://www.gutenberg.org/cache/epub/1448/pg1448-images.html',
            ],
        ];
        // Sukuriame kiekvieną knygą ir priskiriame kategoriją
        foreach ($booksData as [$title, $author, $desc, $catName, $minAge, $maxAge, $cover, $url]) {
            $book = new Book();
            $book->setTitle($title);                  // Pavadinimas
            $book->setAuthor($author);                // Autorius
            $book->setDescription($desc);             // Aprašymas
            $book->setCategory($categories[$catName]); // Priskiriame kategoriją iš masyvo
            $book->setMinAge($minAge);                // Minimalus amžius
            $book->setMaxAge($maxAge);                // Maksimalus amžius
            $book->setCoverImage($cover);             // Viršelio nuotrauka (Gutenberg)
            $book->setContentUrl($url);               // Skaitymo nuoroda (Gutenberg HTML)
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
            ['Spalvinimo knygelė', 'Graži spalvinimo knygelė su gyvūnais – puikus būdas lavinti kūrybiškumą!', '/images/rewards/spalvinimo-knygele.svg', 30, 10],
            ['Lipdukai', 'Rinkinys spalvingų lipdukų su knygų personažais – puoškite sąsiuvinius!', '/images/rewards/lipdukai.svg', 20, 20],
            ['Knyga-dovana', 'Pasirinkite bet kurią knygą iš mūsų katalogo kaip asmeninę dovaną!', '/images/rewards/knyga-dovana.svg', 100, 5],
            ['Žaisliukas', 'Mažas žaisliukas-staigmena – nežinosi, ką gausi, kol neatidarysi!', '/images/rewards/zaisliukas.svg', 150, 3],
            ['Specialus ženkliukas', 'Unikalus aukso ženkliukas – tik išrinktiesiems skaitytojams!', '/images/rewards/specialus-zenkliukas.svg', 200, 2],
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
