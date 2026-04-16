<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame UserBook esybę – knygos perskaitymo įrašas
use App\Entity\UserBook;
// Importuojame BookRepository – saugyklą knygų paieškos ir filtravimo operacijoms
use App\Repository\BookRepository;
// Importuojame CategoryRepository – saugyklą kategorijų gavimui (filtro select laukui)
use App\Repository\CategoryRepository;
// Importuojame UserBookRepository – saugyklą skaitymo būklės tikrinimui
use App\Repository\UserBookRepository;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
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
            'books' => $books,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedAge' => $age,
        ]);
    }

    // Vienos knygos peržiūros puslapis: /knygos/{id}
    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'])]
    public function show(int $id, BookRepository $bookRepository, UserBookRepository $userBookRepository): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        // Tikriname, ar vartotojas jau perskaitė šią knygą
        $isRead = false;
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($user) {
            $isRead = $userBookRepository->hasUserReadBook($user->getId(), $id);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'isRead' => $isRead,
        ]);
    }

    // Knygos skaitymo puslapis: /knygos/{id}/skaityti
    #[Route('/{id}/skaityti', name: 'app_book_read', requirements: ['id' => '\d+'])]
    public function read(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        if (!$book->getContentUrl()) {
            $this->addFlash('warning', 'Ši knyga dar neturi skaitymo turinio.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        return $this->render('book/read.html.twig', [
            'book' => $book,
        ]);
    }

    // Pažymėti knygą kaip perskaitytą: POST /knygos/{id}/perskaityta
    #[Route('/{id}/perskaityta', name: 'app_book_mark_read', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function markAsRead(int $id, Request $request, BookRepository $bookRepository, UserBookRepository $userBookRepository, EntityManagerInterface $em): Response
    {
        // Reikalaujame prisijungimo
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Tikriname CSRF tokeną
        $token = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('mark_read_' . $id, $token)) {
            $this->addFlash('danger', 'Neteisingas saugumo tokenas.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Tikriname, ar dar neperskaityta
        if (!$userBookRepository->hasUserReadBook($user->getId(), $id)) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
            $em->persist($userBook);
            $em->flush();
            $this->addFlash('success', sprintf('Knyga „%s" pažymėta kaip perskaityta!', $book->getTitle()));
        } else {
            $this->addFlash('info', 'Ši knyga jau pažymėta kaip perskaityta.');
        }

        return $this->redirectToRoute('app_book_show', ['id' => $id]);
    }
}
