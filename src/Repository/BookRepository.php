<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Paieška pagal pavadinimą arba autorių ir filtravimas pagal amžių/kategoriją.
     *
     * @return Book[]
     */
    public function findByFilters(?string $search, ?int $categoryId, ?int $age): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.category', 'c')
            ->addSelect('c');

        if ($search) {
            $qb->andWhere('b.title LIKE :search OR b.author LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($categoryId) {
            $qb->andWhere('c.id = :catId')
               ->setParameter('catId', $categoryId);
        }

        if ($age) {
            $qb->andWhere('b.minAge <= :age AND b.maxAge >= :age')
               ->setParameter('age', $age);
        }

        $qb->orderBy('b.title', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
