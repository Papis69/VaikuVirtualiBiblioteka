<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Misijų saugykla – atlieka duomenų bazės užklausas su misijomis.
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }
}
