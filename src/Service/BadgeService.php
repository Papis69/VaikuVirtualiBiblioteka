<?php
// Vardų erdvė – nurodo, kad ši klasė priklauso App\Service paketui
namespace App\Service;

// Importuojame Badge esybę – ženkliuko objektą
use App\Entity\Badge;
// Importuojame User esybę – vartotojo objektą
use App\Entity\User;
// Importuojame UserBadge esybę – tarpinę lentelę tarp vartotojo ir ženkliuko
use App\Entity\UserBadge;
// Importuojame BadgeRepository – saugyklą visų ženkliukų gavimui iš DB
use App\Repository\BadgeRepository;
// Importuojame UserBadgeRepository – saugyklą vartotojų ženkliukų operacijoms
use App\Repository\UserBadgeRepository;
// Importuojame EntityManagerInterface – Doctrine sąsaja duomenų bazės operacijoms (persist, flush)
use Doctrine\ORM\EntityManagerInterface;

/**
 * Servisas automatiniam ženkliukų suteikimui pagal taškų kriterijus.
 * Kai vartotojas surenka pakankamai taškų, automatiškai gauna ženkliuką.
 */
class BadgeService
{
    // Konstruktorius su priklausomybių injekcija (Dependency Injection)
    // Symfony automatiškai paduoda šias priklausomybes (autowiring)
    public function __construct(
        private BadgeRepository $badgeRepository,         // Ženkliukų saugykla
        private UserBadgeRepository $userBadgeRepository, // Vartotojų ženkliukų saugykla
        private EntityManagerInterface $em,               // Duomenų bazės valdytojas
    ) {
    }

    /**
     * Patikrina, ar vartotojas užsitarnavo naujų ženkliukų, ir juos automatiškai suteikia.
     * Grąžina masyvą naujų ženkliukų, kurie buvo suteikti.
     */
    public function checkAndAwardBadges(User $user): array
    {
        // Gauname visus galimus ženkliukus iš duomenų bazės
        $allBadges = $this->badgeRepository->findAll();
        // Sukuriame masyvą jau turimų ženkliukų ID
        $earnedBadgeIds = [];

        // Einame per visus vartotojo turimus ženkliukus ir renkame jų ID
        foreach ($user->getUserBadges() as $ub) {
            $earnedBadgeIds[] = $ub->getBadge()->getId(); // Pridedame ženkliuko ID į masyvą
        }

        // Masyvas naujų ženkliukų, kuriuos suteikėme šio patikrinimo metu
        $newBadges = [];

        // Einame per visus galimus ženkliukus ir tikriname, ar vartotojas turi teisę juos gauti
        foreach ($allBadges as $badge) {
            // Praleidžiame ženkliuką, jei vartotojas jį jau turi
            if (in_array($badge->getId(), $earnedBadgeIds)) {
                continue; // Pereiname prie kito ženkliuko
            }

            // Tikriname, ar vartotojo taškai yra pakankami šiam ženkliukui gauti
            if ($user->getPoints() >= $badge->getRequiredPoints()) {
                // Sukuriame naują UserBadge įrašą (susiejimą tarp vartotojo ir ženkliuko)
                $userBadge = new UserBadge();
                $userBadge->setUser($user);     // Nustatome vartotoją
                $userBadge->setBadge($badge);   // Nustatome ženkliuką
                $this->em->persist($userBadge); // Pažymime įrašą išsaugojimui
                $newBadges[] = $badge;           // Pridedame prie naujų ženkliukų sąrašo
            }
        }

        // Jei buvo suteikta bent vienas naujas ženkliukas, išsaugome pakeitimus DB
        if (count($newBadges) > 0) {
            $this->em->flush(); // Įvykdome visas laukiančias SQL užklausas
        }

        // Grąžiname naujų ženkliukų masyvą (naudojama pranešimams rodyti)
        return $newBadges;
    }
}
