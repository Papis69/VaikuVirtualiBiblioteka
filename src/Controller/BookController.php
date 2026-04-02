<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame BookRepository – saugyklą knygų paieškos ir filtravimo operacijoms
use App\Repository\BookRepository;
// Importuojame CategoryRepository – saugyklą kategorijų gavimui (filtro select laukui)
use App\Repository\CategoryRepository;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas (paieškos parametrų gavimui)
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Knygų kontroleris – visi URL prasideda /knygos prefiksu
#[Route('/knygos')]
class BookController extends AbstractController
{
    // Knygų sąrašo puslapis su paieška ir filtrais: /knygos
    #[Route('', name: 'app_books')]
    public function index(Request $request, BookRepository $bookRepository, CategoryRepository $categoryRepository): Response
    {
        // Gauname paieškos tekstą iš URL parametro ?q=...
        $search = $request->query->get('q');
        // Gauname pasirinktos kategorijos ID iš URL parametro ?category=...
        $categoryId = $request->query->get('category') !== '' ? (int) $request->query->get('category') : null;
        // Gauname amžiaus filtrą iš URL parametro ?age=...
        $age = $request->query->get('age') !== '' ? (int) $request->query->get('age') : null;

        // Atliekame knygų paiešką su filtrais (per BookRepository metodą)
        $books = $bookRepository->findByFilters($search, $categoryId, $age);
        // Gauname visas kategorijas (filtro pasirinkimo laukui)
        $categories = $categoryRepository->findAll();

        // Grąžiname šabloną su filtruotomis knygomis ir kategorijomis
        return $this->render('book/index.html.twig', [
            'books' => $books,                    // Rastos knygos
            'categories' => $categories,          // Visos kategorijos (filtrui)
            'search' => $search,                  // Paieškos tekstas (formoje parodymui)
            'selectedCategory' => $categoryId,    // Pasirinkta kategorija (select aktyviam elementui)
            'selectedAge' => $age,                // Pasirinktas amžius (select aktyviam elementui)
        ]);
    }

    // Vienos knygos peržiūros puslapis: /knygos/{id} (pvz., /knygos/5)
    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'])] // \d+ = tik skaičiai
    public function show(int $id, BookRepository $bookRepository): Response
    {
        // Ieškome knygos pagal ID
        $book = $bookRepository->find($id);

        // Jei knyga nerasta – grąžiname 404 klaidą
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        // Grąžiname knygos detalių šabloną
        return $this->render('book/show.html.twig', [
            'book' => $book, // Perduodame knygos objektą šablonui
        ]);
    }
}
