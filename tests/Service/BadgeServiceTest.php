<?php
// Testai BadgeService servisui
namespace App\Tests\Service;

use App\Entity\Badge;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Repository\BadgeRepository;
use App\Repository\UserBadgeRepository;
use App\Service\BadgeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BadgeServiceTest extends TestCase
{
    // Testuojame, kad ženkliukas suteikiamas kai vartotojas turi pakankamai taškų
    public function testBadgeAwardedWhenPointsMet(): void
    {
        // Sukuriame vartotoją su 100 taškų
        $user = new User();
        $user->addPoints(100);

        // Sukuriame ženkliuką, reikalaujantį 50 taškų
        $badge = new Badge();
        $badge->setName('Skaitytojas');
        $badge->setRequiredPoints(50);

        // Mockuojame BadgeRepository – grąžins mūsų ženkliuką
        $badgeRepository = $this->createMock(BadgeRepository::class);
        $badgeRepository->method('findAll')->willReturn([$badge]);

        // Mockuojame EntityManager
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist'); // Tikimės, kad bus persist
        $em->expects($this->once())->method('flush');   // Tikimės, kad bus flush

        // Sukuriame servisą ir vykdome
        $userBadgeRepository = $this->createMock(UserBadgeRepository::class);
        $service = new BadgeService($badgeRepository, $userBadgeRepository, $em);
        $service->checkAndAwardBadges($user);
    }

    // Testuojame, kad ženkliukas NESUTEIKIAMAS kai taškų nepakanka
    public function testBadgeNotAwardedWhenPointsInsufficient(): void
    {
        $user = new User();
        $user->addPoints(20); // Tik 20 taškų

        $badge = new Badge();
        $badge->setName('Ekspertas');
        $badge->setRequiredPoints(500); // Reikia 500

        $badgeRepository = $this->createMock(BadgeRepository::class);
        $badgeRepository->method('findAll')->willReturn([$badge]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist'); // NETURI būti persist
        $em->expects($this->never())->method('flush');

        $userBadgeRepository = $this->createMock(UserBadgeRepository::class);
        $service = new BadgeService($badgeRepository, $userBadgeRepository, $em);
        $service->checkAndAwardBadges($user);
    }
}
