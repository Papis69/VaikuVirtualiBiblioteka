<?php
// Testai Book esybei
namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    // Testuojame knygos pavadinimo nustatymą
    public function testSetTitle(): void
    {
        $book = new Book();
        $book->setTitle('Raudonkepuraitė');
        $this->assertEquals('Raudonkepuraitė', $book->getTitle());
    }

    // Testuojame knygos autoriaus nustatymą
    public function testSetAuthor(): void
    {
        $book = new Book();
        $book->setAuthor('Charles Perrault');
        $this->assertEquals('Charles Perrault', $book->getAuthor());
    }

    // Testuojame kategorijos priskyrimo ryšį
    public function testSetCategory(): void
    {
        $book = new Book();
        $category = new Category();
        $category->setName('Pasakos');

        $book->setCategory($category);
        $this->assertSame($category, $book->getCategory());
        $this->assertEquals('Pasakos', $book->getCategory()->getName());
    }

    // Testuojame amžiaus diapazono nustatymą
    public function testAgeRange(): void
    {
        $book = new Book();
        $book->setMinAge(5);
        $book->setMaxAge(10);
        $this->assertEquals(5, $book->getMinAge());
        $this->assertEquals(10, $book->getMaxAge());
    }

    // Testuojame turinio URL
    public function testContentUrl(): void
    {
        $book = new Book();
        $book->setContentUrl('https://example.com/book');
        $this->assertEquals('https://example.com/book', $book->getContentUrl());
    }

    // Testuojame, kad contentUrl pagal nutylėjimą yra null
    public function testContentUrlDefaultNull(): void
    {
        $book = new Book();
        $this->assertNull($book->getContentUrl());
    }
}
