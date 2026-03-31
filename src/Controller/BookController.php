<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/knygos')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_books')]
    public function index(Request $request, BookRepository $bookRepository, CategoryRepository $categoryRepository): Response
    {
        $search = $request->query->get('q');
        $categoryId = $request->query->get('category') !== '' ? (int) $request->query->get('category') : null;
        $age = $request->query->get('age') !== '' ? (int) $request->query->get('age') : null;

        $books = $bookRepository->findByFilters($search, $categoryId, $age);
        $categories = $categoryRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedAge' => $age,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'])]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
