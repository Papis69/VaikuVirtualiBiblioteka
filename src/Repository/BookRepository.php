<?php
// Vardų erdvė – saugyklų (repository) paketas
namespace App\Repository;

// Importuojame Book esybę
use App\Entity\Book;
// Importuojame ServiceEntityRepository – bazinė Doctrine saugyklos klasė
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// Importuojame ManagerRegistry – Doctrine konfigūracijos registras
use Doctrine\Persistence\ManagerRegistry;

/**
 * Knygų saugykla (Repository) – atlieka duomenų bazės užklausas susijusias su knygomis.
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    // Konstruktorius – registruojame saugyklą su Book esybe
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class); // Nurodome, kad ši saugykla dirba su Book esybe
    }

    /**
     * Paieška pagal pavadinimą arba autorių ir filtravimas pagal amžių/kategoriją.
     * Ši užklausa naudojama knygų katalogo puslapyje su filtrais.
     *
     * @param string|null $search     Paieškos tekstas (pavadinime arba autoriuje)
     * @param int|null    $categoryId Kategorijos ID filtravimui
     * @param int|null    $age        Vaiko amžius filtravimui
     * @return Book[] Grąžina knygų masyvą
     */
    public function findByFilters(?string $search, ?int $categoryId, ?int $age): array
    {
        // Kuriame QueryBuilder užklausą (b = book alias)
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.category', 'c')  // LEFT JOIN su kategorijų lentele (c = category alias)
            ->addSelect('c');               // Pridedame kategoriją į SELECT (optimizuoja – viena užklausa vietoj kelių)

        // Jei nurodytas paieškos tekstas – pridedame WHERE sąlygą
        if ($search) {
            $qb->andWhere('b.title LIKE :search OR b.author LIKE :search') // Ieškome pavadinime IR autoriuje
               ->setParameter('search', '%' . $search . '%');              // % – dalinis atitikimas
        }

        // Jei pasirinkta kategorija – filtruojame pagal kategorijos ID
        if ($categoryId) {
            $qb->andWhere('c.id = :catId')
               ->setParameter('catId', $categoryId);
        }

        // Jei nurodytas amžius – filtruojame pagal amžiaus intervalą
        if ($age) {
            $qb->andWhere('b.minAge <= :age AND b.maxAge >= :age') // Amžius turi tilpti tarp minAge ir maxAge
               ->setParameter('age', $age);
        }

        // Rūšiuojame pagal pavadinimą abėcėlės tvarka
        $qb->orderBy('b.title', 'ASC');

        // Vykdome užklausą ir grąžiname rezultatus
        return $qb->getQuery()->getResult();
    }
}
