<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

// Importuojame UserMission esybę
use App\Entity\UserMission;
// Importuojame ServiceEntityRepository – bazinė saugyklos klasė
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// Importuojame ManagerRegistry – Doctrine konfigūracijos registras
use Doctrine\Persistence\ManagerRegistry;

/**
 * Vartotojų misijų saugykla – atlieka užklausas susijusias su vartotojų misijomis.
 * @extends ServiceEntityRepository<UserMission>
 */
class UserMissionRepository extends ServiceEntityRepository
{
    // Konstruktorius – registruojame saugyklą su UserMission esybe
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMission::class);
    }

    /**
     * Rasti visas konkrečiau vartotojo misijas (su misijų duomenimis).
     * Naudojama misijų puslapyje, kad nustatyti, kurios misijos jau atliktos.
     *
     * @param int $userId Vartotojo ID
     * @return UserMission[] Vartotojo misijų masyvas
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('um')          // um = user_mission alias
            ->leftJoin('um.mission', 'm')               // JOIN su misijų lentele
            ->addSelect('m')                            // Pridedame misiją į SELECT (optimizacija)
            ->andWhere('um.user = :userId')             // Filtruojame pagal vartotojo ID
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Rasti visas laukiančias patvirtinimo misijas (admin peržiūrai).
     * Naudojama admin misijų peržiūros puslapyje.
     *
     * @return UserMission[] Laukiančių misijų masyvas
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('um')
            ->leftJoin('um.user', 'u')                 // JOIN su vartotojų lentele
            ->addSelect('u')                            // Pridedame vartotoją (kad nereikėtų papildomų užklausų)
            ->leftJoin('um.mission', 'm')              // JOIN su misijų lentele
            ->addSelect('m')                            // Pridedame misiją
            ->andWhere('um.status = :status')           // Filtruojame tik „pending" statusą
            ->setParameter('status', UserMission::STATUS_PENDING)
            ->orderBy('um.completedAt', 'ASC')         // Rūšiuojame pagal pateikimo datą (seniausios pirmos)
            ->getQuery()
            ->getResult();
    }

    /**
     * Suskaičiuoti laukiančias patvirtinimo misijas.
     * Naudojama admin skydelyje skaičiuko rodymui.
     *
     * @return int Laukiančių misijų skaičius
     */
    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('um')
            ->select('COUNT(um.id)')                    // Skaičiuojame įrašus
            ->andWhere('um.status = :status')           // Tik „pending" statusas
            ->setParameter('status', UserMission::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();                  // Grąžina vieną skaičių
    }
}
