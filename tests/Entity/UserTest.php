<?php
// Testai vartotojo esybei
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    // Testuojame, kad naujas vartotojas turi 0 taškų
    public function testNewUserHasZeroPoints(): void
    {
        $user = new User();
        $this->assertEquals(0, $user->getPoints());
    }

    // Testuojame taškų pridėjimą
    public function testAddPoints(): void
    {
        $user = new User();
        $user->addPoints(50);
        $this->assertEquals(50, $user->getPoints());

        $user->addPoints(30);
        $this->assertEquals(80, $user->getPoints());
    }

    // Testuojame taškų atėmimą
    public function testRemovePoints(): void
    {
        $user = new User();
        $user->addPoints(100);
        $user->removePoints(40);
        $this->assertEquals(60, $user->getPoints());
    }

    // Testuojame, kad taškai nenukrenta žemiau 0
    public function testRemovePointsDoesNotGoBelowZero(): void
    {
        $user = new User();
        $user->addPoints(10);
        $user->removePoints(50); // Bandome atimti daugiau nei turime
        $this->assertGreaterThanOrEqual(0, $user->getPoints());
    }

    // Testuojame, kad vartotojo vardas nustatomas teisingai
    public function testSetUsername(): void
    {
        $user = new User();
        $user->setUsername('Petriukas');
        $this->assertEquals('Petriukas', $user->getUsername());
    }

    // Testuojame, kad el. paštas nustatomas teisingai
    public function testSetEmail(): void
    {
        $user = new User();
        $user->setEmail('petriukas@test.lt');
        $this->assertEquals('petriukas@test.lt', $user->getEmail());
    }

    // Testuojame, kad naujas vartotojas turi ROLE_USER rolę
    public function testDefaultRole(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    // Testuojame rolių nustatymą
    public function testSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // ROLE_USER visada turi būti
    }

    // Testuojame getUserIdentifier grąžina el. paštą
    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }
}
