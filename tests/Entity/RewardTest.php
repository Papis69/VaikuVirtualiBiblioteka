<?php
// Testai Reward esybei
namespace App\Tests\Entity;

use App\Entity\Reward;
use PHPUnit\Framework\TestCase;

class RewardTest extends TestCase
{
    // Testuojame prizo pavadinimo nustatymą
    public function testSetName(): void
    {
        $reward = new Reward();
        $reward->setName('Spalvinimo knygelė');
        $this->assertEquals('Spalvinimo knygelė', $reward->getName());
    }

    // Testuojame prizo kainos nustatymą
    public function testSetCostInPoints(): void
    {
        $reward = new Reward();
        $reward->setCostInPoints(50);
        $this->assertEquals(50, $reward->getCostInPoints());
    }

    // Testuojame likučio valdymą
    public function testStockManagement(): void
    {
        $reward = new Reward();
        $reward->setStock(10);
        $this->assertEquals(10, $reward->getStock());

        // Mažiname po 1
        $reward->setStock($reward->getStock() - 1);
        $this->assertEquals(9, $reward->getStock());
    }

    // Testuojame nuotraukos URL nustatymą
    public function testSetImage(): void
    {
        $reward = new Reward();
        $reward->setImage('/images/rewards/test.svg');
        $this->assertEquals('/images/rewards/test.svg', $reward->getImage());
    }

    // Testuojame, kad nuotrauka pagal nutylėjimą yra null
    public function testImageDefaultNull(): void
    {
        $reward = new Reward();
        $this->assertNull($reward->getImage());
    }
}
