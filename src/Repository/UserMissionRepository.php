<?php

namespace App\Repository;

use App\Entity\UserMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMission>
 */
class UserMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMission::class);
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('um')
            ->leftJoin('um.mission', 'm')
            ->addSelect('m')
            ->andWhere('um.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Rasti visas laukiančias patvirtinimo misijas (admin peržiūrai).
     *
     * @return UserMission[]
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('um')
            ->leftJoin('um.user', 'u')
            ->addSelect('u')
            ->leftJoin('um.mission', 'm')
            ->addSelect('m')
            ->andWhere('um.status = :status')
            ->setParameter('status', UserMission::STATUS_PENDING)
            ->orderBy('um.completedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Suskaičiuoti laukiančias patvirtinimo misijas.
     */
    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('um')
            ->select('COUNT(um.id)')
            ->andWhere('um.status = :status')
            ->setParameter('status', UserMission::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
