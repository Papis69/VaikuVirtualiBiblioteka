<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame Badge esybę
use App\Entity\Badge;
// Importuojame BadgeFormType – ženkliuko formos tipą
use App\Form\BadgeFormType;
// Importuojame BadgeRepository – ženkliukų saugyklą
use App\Repository\BadgeRepository;
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

// Administravimo ženkliukų valdymo kontroleris. Visi URL prasideda /admin/zenkliukai
#[Route('/admin/zenkliukai')]
class AdminBadgeController extends AbstractController
{
    // Ženkliukų sąrašas: /admin/zenkliukai
    #[Route('', name: 'admin_badges')]
    public function index(BadgeRepository $badgeRepository): Response
    {
        return $this->render('admin/badge/index.html.twig', [
            'badges' => $badgeRepository->findAll(),
        ]);
    }

    // Naujo ženkliuko kūrimas: /admin/zenkliukai/naujas
    #[Route('/naujas', name: 'admin_badge_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $badge = new Badge();
        $form = $this->createForm(BadgeFormType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($badge);
            $em->flush();
            $this->addFlash('success', 'Ženkliukas sukurtas!');
            return $this->redirectToRoute('admin_badges');
        }

        return $this->render('admin/badge/form.html.twig', [
            'form' => $form,
            'title' => 'Naujas ženkliukas',
        ]);
    }

    // Ženkliuko redagavimas: /admin/zenkliukai/redaguoti/{id}
    #[Route('/redaguoti/{id}', name: 'admin_badge_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, BadgeRepository $badgeRepository, EntityManagerInterface $em): Response
    {
        $badge = $badgeRepository->find($id);
        if (!$badge) {
            throw $this->createNotFoundException('Ženkliukas nerastas');
        }

        $form = $this->createForm(BadgeFormType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Ženkliukas atnaujintas!');
            return $this->redirectToRoute('admin_badges');
        }

        return $this->render('admin/badge/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti ženkliuką',
        ]);
    }

    // Ženkliuko trynimas: /admin/zenkliukai/trinti/{id}
    #[Route('/trinti/{id}', name: 'admin_badge_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, BadgeRepository $badgeRepository, EntityManagerInterface $em): Response
    {
        $badge = $badgeRepository->find($id);
        if ($badge) {
            $em->remove($badge);
            $em->flush();
            $this->addFlash('success', 'Ženkliukas ištrintas!');
        }

        return $this->redirectToRoute('admin_badges');
    }
}
