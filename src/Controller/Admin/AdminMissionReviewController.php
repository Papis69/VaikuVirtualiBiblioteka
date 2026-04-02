<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame UserMission esybę – vartotojo misijos objektas (su statuso konstantomis)
use App\Entity\UserMission;
// Importuojame UserMissionRepository – vartotojų misijų saugyklą
use App\Repository\UserMissionRepository;
// Importuojame BadgeService – ženkliukų automatinio suteikimo servisą
use App\Service\BadgeService;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas (atmetimo priežasties gavimui)
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Misijų peržiūros kontroleris (admin patvirtina arba atmeta vartotojų pateiktas misijas)
// Visi URL prasideda /admin/perziura
#[Route('/admin/perziura')]
class AdminMissionReviewController extends AbstractController
{
    // Laukiančių misijų sąrašas: /admin/perziura
    #[Route('', name: 'admin_mission_review')]
    public function index(UserMissionRepository $userMissionRepository): Response
    {
        // Reikalaujame ROLE_ADMIN rolės (tik administratoriai gali peržiūrėti)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Gauname visas misijas, kurios laukia peržiūros (statusas: pending)
        $pendingMissions = $userMissionRepository->findPending();

        // Grąžiname peržiūros šabloną
        return $this->render('admin/mission_review/index.html.twig', [
            'pendingMissions' => $pendingMissions, // Laukiančios misijos
        ]);
    }

    // Misijos patvirtinimo veiksmas: /admin/perziura/patvirtinti/{id}
    #[Route('/patvirtinti/{id}', name: 'admin_mission_approve', requirements: ['id' => '\d+'])]
    public function approve(
        int $id,                                          // UserMission ID iš URL
        UserMissionRepository $userMissionRepository,     // Vartotojų misijų saugykla
        EntityManagerInterface $em,                       // DB valdytojas
        BadgeService $badgeService,                       // Ženkliukų servisas
    ): Response {
        // Reikalaujame ROLE_ADMIN rolės
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Ieškome vartotojo misijos pagal ID
        $userMission = $userMissionRepository->find($id);
        if (!$userMission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        // Tikriname, ar misija dar laukia peržiūros
        if (!$userMission->isPending()) {
            $this->addFlash('warning', 'Ši misija jau buvo peržiūrėta.');
            return $this->redirectToRoute('admin_mission_review');
        }

        // === Patvirtinimo logika ===

        // Nustatome misijos statusą į „patvirtinta"
        $userMission->setStatus(UserMission::STATUS_APPROVED);
        $userMission->setIsCompleted(true);                    // Pažymime kaip atliktą
        $userMission->setReviewedAt(new \DateTimeImmutable()); // Užfiksuojame peržiūros datą

        // Suteikiame taškus vartotojui
        $user = $userMission->getUser();      // Gauname vartotoją
        $mission = $userMission->getMission(); // Gauname misiją
        $user->addPoints($mission->getRewardPoints()); // Pridedame taškus

        // Išsaugome pakeitimus DB
        $em->persist($userMission);
        $em->persist($user);
        $em->flush();

        // Tikriname, ar vartotojas užsitarnavo naujų ženkliukų po taškų pridėjimo
        $newBadges = $badgeService->checkAndAwardBadges($user);

        // Jei gauta naujų ženkliukų – rodome pranešimą
        if (count($newBadges) > 0) {
            // Surenkame ženkliukų pavadinimus
            $names = array_map(fn($b) => $b->getName(), $newBadges);
            $this->addFlash('success', sprintf(
                'Naudotojas „%s" gavo naujų ženkliukų: %s!',
                $user->getUsername(),
                implode(', ', $names) // Sujungiame pavadinimus kableliais
            ));
        }

        // Pranešimas apie sėkmingą patvirtinimą
        $this->addFlash('success', sprintf(
            'Misija „%s" patvirtinta! Naudotojas „%s" gavo %d taškų.',
            $mission->getTitle(),
            $user->getUsername(),
            $mission->getRewardPoints()
        ));

        // Nukreipiame atgal į peržiūros puslapį
        return $this->redirectToRoute('admin_mission_review');
    }

    // Misijos atmetimo veiksmas: /admin/perziura/atmesti/{id}
    #[Route('/atmesti/{id}', name: 'admin_mission_reject', requirements: ['id' => '\d+'])]
    public function reject(
        int $id,                                          // UserMission ID iš URL
        Request $request,                                 // HTTP užklausa (atmetimo priežasčiai)
        UserMissionRepository $userMissionRepository,     // Vartotojų misijų saugykla
        EntityManagerInterface $em,                       // DB valdytojas
    ): Response {
        // Reikalaujame ROLE_ADMIN rolės
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Ieškome vartotojo misijos pagal ID
        $userMission = $userMissionRepository->find($id);
        if (!$userMission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        // Tikriname, ar misija dar laukia peržiūros
        if (!$userMission->isPending()) {
            $this->addFlash('warning', 'Ši misija jau buvo peržiūrėta.');
            return $this->redirectToRoute('admin_mission_review');
        }

        // Gauname atmetimo priežastį iš formos (neprivaloma)
        $reason = trim($request->request->get('rejection_reason', ''));

        // === Atmetimo logika ===

        // Nustatome misijos statusą į „atmesta"
        $userMission->setStatus(UserMission::STATUS_REJECTED);
        $userMission->setIsCompleted(false);                   // Neatlikta
        $userMission->setReviewedAt(new \DateTimeImmutable()); // Peržiūros data
        $userMission->setRejectionReason($reason ?: null);     // Atmetimo priežastis (arba null)

        // Išsaugome pakeitimus DB
        $em->persist($userMission);
        $em->flush();

        // Pranešimas apie sėkmingą atmetimą
        $this->addFlash('success', sprintf(
            'Misija „%s" naudotojui „%s" atmesta.',
            $userMission->getMission()->getTitle(),
            $userMission->getUser()->getUsername()
        ));

        // Nukreipiame atgal į peržiūros puslapį
        return $this->redirectToRoute('admin_mission_review');
    }
}
