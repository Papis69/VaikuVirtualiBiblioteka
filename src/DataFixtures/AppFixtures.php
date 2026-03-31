<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Mission;
use App\Entity\Reward;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Vartotojai ---
        $admin = new User();
        $admin->setUsername('Administratorius');
        $admin->setEmail('admin@biblioteka.lt');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setPoints(0);
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('Jonas');
        $user->setEmail('jonas@biblioteka.lt');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setPoints(50);
        $manager->persist($user);

        // --- Kategorijos ---
        $categories = [];
        $catData = [
            ['Pasakos', '#FF6B9D'],
            ['Nuotykiai', '#6C63FF'],
            ['Mokslas', '#00D2FF'],
            ['Gamta', '#2ED573'],
            ['Fantastika', '#FFA502'],
        ];
        foreach ($catData as [$name, $color]) {
            $cat = new Category();
            $cat->setName($name);
            $cat->setColor($color);
            $manager->persist($cat);
            $categories[$name] = $cat;
        }

        // --- Knygos ---
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
        foreach ($booksData as [$title, $author, $desc, $catName, $minAge, $maxAge]) {
            $book = new Book();
            $book->setTitle($title);
            $book->setAuthor($author);
            $book->setDescription($desc);
            $book->setCategory($categories[$catName]);
            $book->setMinAge($minAge);
            $book->setMaxAge($maxAge);
            $manager->persist($book);
        }

        // --- Misijos ---
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
        foreach ($missionsData as [$title, $desc, $points, $type]) {
            $mission = new Mission();
            $mission->setTitle($title);
            $mission->setDescription($desc);
            $mission->setRewardPoints($points);
            $mission->setType($type);
            $manager->persist($mission);
        }

        // --- Ženkliukai ---
        $badgesData = [
            ['Pradedantysis', 'Surinkote pirmuosius 10 taškų!', '📖', 10],
            ['Skaitytojas', 'Surinkote 50 taškų!', '⭐', 50],
            ['Knygų draugas', 'Surinkote 100 taškų!', '🏅', 100],
            ['Skaitymo čempionas', 'Surinkote 200 taškų!', '🏆', 200],
            ['Knygų meistras', 'Surinkote 500 taškų!', '👑', 500],
        ];
        foreach ($badgesData as [$name, $desc, $icon, $reqPoints]) {
            $badge = new Badge();
            $badge->setName($name);
            $badge->setDescription($desc);
            $badge->setIcon($icon);
            $badge->setRequiredPoints($reqPoints);
            $manager->persist($badge);
        }

        // --- Prizai ---
        $rewardsData = [
            ['Spalvinimo knygelė', 'Graži spalvinimo knygelė su gyvūnais!', null, 30, 10],
            ['Lipdukai', 'Rinkinys spalvingų lipdukų!', null, 20, 20],
            ['Knyga-dovana', 'Pasirink bet kurią knygą kaip dovaną!', null, 100, 5],
            ['Žaisliukas', 'Mažas žaisliukas-staigmena!', null, 150, 3],
            ['Specialus ženkliukas', 'Unikalus aukso ženkliukas!', null, 200, 2],
        ];
        foreach ($rewardsData as [$name, $desc, $img, $cost, $stock]) {
            $reward = new Reward();
            $reward->setName($name);
            $reward->setDescription($desc);
            $reward->setImage($img);
            $reward->setCostInPoints($cost);
            $reward->setStock($stock);
            $manager->persist($reward);
        }

        $manager->flush();
    }
}
