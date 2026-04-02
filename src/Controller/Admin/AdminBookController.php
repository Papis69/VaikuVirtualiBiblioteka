<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame Book esybę – knygos objektas
use App\Entity\Book;
// Importuojame BookFormType – knygos formos tipą
use App\Form\BookFormType;
// Importuojame BookRepository – knygų saugyklą
use App\Repository\BookRepository;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Administravimo knygų valdymo kontroleris. Visi URL prasideda /admin/knygos
#[Route('/admin/knygos')]
class AdminBookController extends AbstractController
{
    // Knygų sąrašas admin puslapyje: /admin/knygos
    #[Route('', name: 'admin_books')]
    public function index(BookRepository $bookRepository): Response
    {
        // Grąžiname šabloną su visomis knygomis
        return $this->render('admin/book/index.html.twig', [
            'books' => $bookRepository->findAll(), // Visos knygos lentelei
        ]);
    }

    // Naujos knygos kūrimo puslapis: /admin/knygos/nauja
    #[Route('/nauja', name: 'admin_book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Sukuriame naują tuščią knygos objektą
        $book = new Book();
        // Sukuriame formą, susietą su knygos objektu
        $form = $this->createForm(BookFormType::class, $book);
        // Apdorojame užklausos duomenis
        $form->handleRequest($request);

        // Jei forma pateikta ir valid – išsaugome knygą
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book); // Pažymime knygą išsaugojimui
            $em->flush();        // Vykdome SQL INSERT
            $this->addFlash('success', 'Knyga sukurta!'); // Sėkmės pranešimas
            return $this->redirectToRoute('admin_books'); // Nukreipiame į sąrašą
        }

        // Rodome formos šabloną
        return $this->render('admin/book/form.html.twig', [
            'form' => $form,            // Formos objektas
            'title' => 'Nauja knyga',   // Puslapio antraštė
        ]);
    }

    // Knygos redagavimo puslapis: /admin/knygos/redaguoti/{id}
    #[Route('/redaguoti/{id}', name: 'admin_book_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, BookRepository $bookRepository, EntityManagerInterface $em): Response
    {
        // Ieškome knygos pagal ID
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta'); // 404 klaida
        }

        // Sukuriame formą su esamos knygos duomenimis
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        // Jei forma pateikta ir valid – atnaujname knygą
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Doctrine automatiškai aptinka pakeitimus – tik flush reikia
            $this->addFlash('success', 'Knyga atnaujinta!');
            return $this->redirectToRoute('admin_books');
        }

        // Rodome formos šabloną su esamos knygos duomenimis
        return $this->render('admin/book/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti knygą',
        ]);
    }

    // Knygos trynimo veiksmas: /admin/knygos/trinti/{id}
    #[Route('/trinti/{id}', name: 'admin_book_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, BookRepository $bookRepository, EntityManagerInterface $em): Response
    {
        // Ieškome knygos pagal ID
        $book = $bookRepository->find($id);
        if ($book) {
            $em->remove($book); // Pažymime knygą trynimui
            $em->flush();       // Vykdome SQL DELETE
            $this->addFlash('success', 'Knyga ištrinta!');
        }

        // Nukreipiame atgal į knygų sąrašą
        return $this->redirectToRoute('admin_books');
    }
}
