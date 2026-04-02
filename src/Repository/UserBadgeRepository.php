<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

use App\Entity\UserBadge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Vartotojų ženkliukų saugykla – atlieka duomenų bazės užklausas su vartotojų ženkliukais.
 * @extends ServiceEntityRepository<UserBadge>
 */
class UserBadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBadge::class);
    }
}
