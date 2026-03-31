<?php

namespace App\Service;

use App\Entity\Badge;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Repository\BadgeRepository;
use App\Repository\UserBadgeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Servisas automatiniam ženkliukų suteikimui pagal taškų kriterijus.
 */
class BadgeService
{
    public function __construct(
        private BadgeRepository $badgeRepository,
        private UserBadgeRepository $userBadgeRepository,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Patikrina, ar vartotojas užsitarnavo naujų ženkliukų, ir juos suteikia.
     */
    public function checkAndAwardBadges(User $user): array
    {
        $allBadges = $this->badgeRepository->findAll();
        $earnedBadgeIds = [];

        foreach ($user->getUserBadges() as $ub) {
            $earnedBadgeIds[] = $ub->getBadge()->getId();
        }

        $newBadges = [];

        foreach ($allBadges as $badge) {
            if (in_array($badge->getId(), $earnedBadgeIds)) {
                continue;
            }

            if ($user->getPoints() >= $badge->getRequiredPoints()) {
                $userBadge = new UserBadge();
                $userBadge->setUser($user);
                $userBadge->setBadge($badge);
                $this->em->persist($userBadge);
                $newBadges[] = $badge;
            }
        }

        if (count($newBadges) > 0) {
            $this->em->flush();
        }

        return $newBadges;
    }
}
