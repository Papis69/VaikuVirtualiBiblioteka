<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame Category esybę
use App\Entity\Category;
// Importuojame CategoryFormType – kategorijos formos tipą
use App\Form\CategoryFormType;
// Importuojame CategoryRepository – kategorijų saugyklą
use App\Repository\CategoryRepository;
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

// Administravimo kategorijų valdymo kontroleris. Visi URL prasideda /admin/kategorijos
#[Route('/admin/kategorijos')]
class AdminCategoryController extends AbstractController
{
    // Kategorijų sąrašas: /admin/kategorijos
    #[Route('', name: 'admin_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    // Naujos kategorijos kūrimas: /admin/kategorijos/nauja
    #[Route('/nauja', name: 'admin_category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Kategorija sukurta!');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'Nauja kategorija',
        ]);
    }

    // Kategorijos redagavimas: /admin/kategorijos/redaguoti/{id}
    #[Route('/redaguoti/{id}', name: 'admin_category_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Kategorija nerasta');
        }

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Kategorija atnaujinta!');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti kategoriją',
        ]);
    }

    // Kategorijos trynimas: /admin/kategorijos/trinti/{id}
    #[Route('/trinti/{id}', name: 'admin_category_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id);
        if ($category) {
            // Tikriname, ar kategorija neturi knygų
            if ($category->getBooks()->count() > 0) {
                $this->addFlash('danger', 'Negalima ištrinti kategorijos, kuri turi knygų!');
                return $this->redirectToRoute('admin_categories');
            }
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Kategorija ištrinta!');
        }

        return $this->redirectToRoute('admin_categories');
    }
}
