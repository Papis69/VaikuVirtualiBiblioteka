<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

// Importuojame Badge esybę
use App\Entity\Badge;
// Importuojame ServiceEntityRepository – bazinė saugyklos klasė
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// Importuojame ManagerRegistry
use Doctrine\Persistence\ManagerRegistry;

/**
 * Ženkliukų saugykla – atlieka duomenų bazės užklausas su ženkliukais.
 * Naudoja standartinius Doctrine metodus (find, findAll, findBy ir kt.)
 * @extends ServiceEntityRepository<Badge>
 */
class BadgeRepository extends ServiceEntityRepository
{
    // Konstruktorius – registruojame saugyklą su Badge esybe
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }
}
