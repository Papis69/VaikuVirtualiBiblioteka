<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

// Importuojame UserBook esybę
use App\Entity\UserBook;
// Importuojame Doctrine saugyklos bazinę klasę
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * UserBook saugykla – specializuotos užklausos knygų skaitymo duomenims
 * @extends ServiceEntityRepository<UserBook>
 */
class UserBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBook::class);
    }

    /**
     * Tikrina, ar vartotojas jau pažymėjo šią knygą kaip perskaitytą
     */
    public function hasUserReadBook(int $userId, int $bookId): bool
    {
        return (bool) $this->findOneBy([
            'user' => $userId,
            'book' => $bookId,
        ]);
    }

    /**
     * Grąžina visas vartotojo perskaitytas knygas
     */
    public function findByUser(int $userId): array
    {
        return $this->findBy(['user' => $userId], ['readAt' => 'DESC']);
    }
}
