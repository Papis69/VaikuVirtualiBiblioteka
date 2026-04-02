<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

use App\Entity\UserReward;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Vartotojų prizų saugykla – atlieka duomenų bazės užklausas su vartotojų prizais.
 * @extends ServiceEntityRepository<UserReward>
 */
class UserRewardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReward::class);
    }
}
