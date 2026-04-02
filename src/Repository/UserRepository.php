<?php
// Vardų erdvė – saugyklų paketas
namespace App\Repository;

// Importuojame User esybę
use App\Entity\User;
// Importuojame ServiceEntityRepository – bazinė saugyklos klasė
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// Importuojame ManagerRegistry – Doctrine konfigūracijos registras
use Doctrine\Persistence\ManagerRegistry;
// Importuojame UnsupportedUserException – klaida, kai vartotojas nėra tinkamo tipo
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
// Importuojame PasswordAuthenticatedUserInterface – slaptažodžio autentifikacijos sąsaja
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
// Importuojame PasswordUpgraderInterface – automatinio slaptažodžio atnaujinimo sąsaja
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Vartotojų saugykla – atlieka duomenų bazės operacijas su vartotojais.
 * Taip pat palaiko automatinį slaptažodžio hash'o atnaujinimą.
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    // Konstruktorius – registruojame saugyklą su User esybe
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Automatinis slaptažodžio atnaujinimas – Symfony kviečia, kai prisijungimo metu
     * nustato, kad slaptažodžio hash'avimo algoritmas pasikeitė.
     *
     * @param PasswordAuthenticatedUserInterface $user             Vartotojas
     * @param string                             $newHashedPassword Naujas hash'uotas slaptažodis
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Tikriname, ar objektas yra User tipo
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        // Nustatome naują slaptažodį ir išsaugome
        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Išsaugo vartotoją duomenų bazėje.
     *
     * @param User $entity  Vartotojo objektas
     * @param bool $flush   Ar iš karto vykdyti SQL (true = taip)
     */
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); // Pažymime išsaugojimui
        if ($flush) {
            $this->getEntityManager()->flush(); // Vykdome SQL, jei prašoma
        }
    }
}
