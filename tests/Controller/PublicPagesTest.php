<?php
// Kontrolerių „smoke" testai – tikrina, ar puslapiai atsidaro be klaidų
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicPagesTest extends WebTestCase
{
    // Testuojame pagrindinio puslapio atidarymą
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame knygų katalogo atidarymą
    public function testBooksPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/knygos');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame misijų puslapio atidarymą
    public function testMissionsPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/misijos');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame prizų puslapio atidarymą
    public function testRewardsPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/prizai');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame lyderių lentelės atidarymą
    public function testLeaderboardPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lyderiai');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame „Apie mus" puslapį
    public function testAboutPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/apie');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame kontaktų puslapį
    public function testContactsPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/kontaktai');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame privatumo politikos puslapį
    public function testPrivacyPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/privatumas');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame prisijungimo puslapį
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/prisijungimas');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame registracijos puslapį
    public function testRegistrationPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/registracija');
        $this->assertResponseIsSuccessful();
    }

    // Testuojame, kad profilio puslapis nukreipia neprisijungusį vartotoją
    public function testProfileRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profilis');
        $this->assertResponseRedirects(); // Turi nukreipti į prisijungimą
    }

    // Testuojame, kad admin skydelis nukreipia neprisijungusį vartotoją
    public function testAdminRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseRedirects(); // Turi nukreipti į prisijungimą
    }
}
