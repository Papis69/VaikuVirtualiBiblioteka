<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/knygos')]
class AdminBookController extends AbstractController
{
    #[Route('', name: 'admin_books')]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('admin/book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/nauja', name: 'admin_book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Knyga sukurta!');
            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/book/form.html.twig', [
            'form' => $form,
            'title' => 'Nauja knyga',
        ]);
    }

    #[Route('/redaguoti/{id}', name: 'admin_book_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, BookRepository $bookRepository, EntityManagerInterface $em): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Knyga nerasta');
        }

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Knyga atnaujinta!');
            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/book/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti knygą',
        ]);
    }

    #[Route('/trinti/{id}', name: 'admin_book_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, BookRepository $bookRepository, EntityManagerInterface $em): Response
    {
        $book = $bookRepository->find($id);
        if ($book) {
            $em->remove($book);
            $em->flush();
            $this->addFlash('success', 'Knyga ištrinta!');
        }

        return $this->redirectToRoute('admin_books');
    }
}
