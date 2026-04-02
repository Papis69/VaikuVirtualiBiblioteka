<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame UserMission esybę – vartotojo misijos objektas
use App\Entity\UserMission;
// Importuojame MissionRepository – saugyklą misijų gavimui
use App\Repository\MissionRepository;
// Importuojame UserMissionRepository – saugyklą vartotojų misijų operacijoms
use App\Repository\UserMissionRepository;
// Importuojame EntityManagerInterface – Doctrine DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Misijų kontroleris – visi URL prasideda /misijos prefiksu
#[Route('/misijos')]
class MissionController extends AbstractController
{
    // Visų misijų sąrašo puslapis: /misijos
    #[Route('', name: 'app_missions')]
    public function index(MissionRepository $missionRepository, UserMissionRepository $userMissionRepository): Response
    {
        // Gauname visas misijas iš duomenų bazės
        $missions = $missionRepository->findAll();
        // Sukuriame masyvus skirtingų būsenų misijų ID saugojimui
        $completedMissionIds = []; // Patvirtintų misijų ID
        $pendingMissionIds = [];   // Laukiančių patvirtinimo misijų ID
        $rejectedMissionIds = [];  // Atmestų misijų ID
        $rejectedReasons = [];     // Atmetimo priežastys (misijos ID => priežastis)

        // Tikriname, ar vartotojas prisijungęs
        if ($this->getUser()) {
            /** @var \App\Entity\User $user Tipų nuoroda IDE pagalbai */
            $user = $this->getUser();
            // Gauname visas šio vartotojo misijų įrašus
            $userMissions = $userMissionRepository->findByUser($user->getId());
            // Grupuojame misijas pagal statusą
            foreach ($userMissions as $um) {
                if ($um->isApproved()) {
                    // Misija patvirtinta – pridedame prie patvirtintų sąrašo
                    $completedMissionIds[] = $um->getMission()->getId();
                } elseif ($um->isPending()) {
                    // Misija laukia peržiūros – pridedame prie laukiančių sąrašo
                    $pendingMissionIds[] = $um->getMission()->getId();
                } elseif ($um->isRejected()) {
                    // Misija atmesta – pridedame prie atmestų sąrašo ir saugome priežastį
                    $rejectedMissionIds[] = $um->getMission()->getId();
                    $rejectedReasons[$um->getMission()->getId()] = $um->getRejectionReason();
                }
            }
        }

        // Grąžiname misijų šabloną su visais būsenų duomenimis
        return $this->render('mission/index.html.twig', [
            'missions' => $missions,                       // Visos misijos
            'completedMissionIds' => $completedMissionIds, // Patvirtintų misijų ID
            'pendingMissionIds' => $pendingMissionIds,     // Laukiančių misijų ID
            'rejectedMissionIds' => $rejectedMissionIds,   // Atmestų misijų ID
            'rejectedReasons' => $rejectedReasons,         // Atmetimo priežastys
        ]);
    }

    // Misijos įrodymo pateikimo puslapis: /misijos/atlikti/{id}
    #[Route('/atlikti/{id}', name: 'app_mission_submit_proof', requirements: ['id' => '\d+'])]
    public function submitProof(
        int $id,                                     // Misijos ID iš URL
        Request $request,                            // HTTP užklausa (formos duomenims)
        MissionRepository $missionRepository,        // Misijų saugykla
        UserMissionRepository $userMissionRepository, // Vartotojų misijų saugykla
        EntityManagerInterface $em,                  // Duomenų bazės valdytojas
    ): Response {
        // Reikalaujame, kad vartotojas būtų prisijungęs (ROLE_USER rolė)
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Ieškome misijos pagal ID
        $mission = $missionRepository->find($id);
        if (!$mission) {
            throw $this->createNotFoundException('Misija nerasta'); // 404 klaida
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser(); // Gauname prisijungusį vartotoją

        // Tikriname, ar vartotojas jau pateikė šią misiją
        $existing = $userMissionRepository->findOneBy([
            'user' => $user,       // Pagal vartotoją
            'mission' => $mission, // Pagal misiją
        ]);

        // Jei misija jau patvirtinta – neleidžiame pateikti iš naujo
        if ($existing && $existing->isApproved()) {
            $this->addFlash('warning', 'Ši misija jau patvirtinta!'); // Pranešimas vartotojui
            return $this->redirectToRoute('app_missions'); // Nukreipiame atgal
        }

        // Jei misija jau pateikta ir laukia peržiūros
        if ($existing && $existing->isPending()) {
            $this->addFlash('warning', 'Ši misija jau pateikta ir laukia patvirtinimo!');
            return $this->redirectToRoute('app_missions');
        }

        // Apdorojame POST formą (kai vartotojas paspaudžia „Pateikti")
        if ($request->isMethod('POST')) {
            // Gauname ir apvalome įrodymo tekstą
            $proofText = trim($request->request->get('proof_text', ''));

            // Tikriname, ar įrodymo tekstas netuščias
            if (empty($proofText)) {
                $this->addFlash('danger', 'Prašome parašyti įrodymą, kad atlikote misiją.');
                return $this->render('mission/submit_proof.html.twig', [
                    'mission' => $mission,
                ]);
            }

            // Jei nėra esamo įrašo (pirmas pateikimas) – sukuriame naują
            if (!$existing) {
                $existing = new UserMission();
                $existing->setUser($user);       // Nustatome vartotoją
                $existing->setMission($mission); // Nustatome misiją
            }

            // Nustatome pateikimo duomenis
            $existing->setProofText($proofText);                  // Įrodymo tekstas
            $existing->setStatus(UserMission::STATUS_PENDING);    // Statusas: laukia peržiūros
            $existing->setIsCompleted(false);                     // Dar ne atlikta (nepatvirtinta)
            $existing->setCompletedAt(new \DateTimeImmutable());  // Pateikimo data
            $existing->setRejectionReason(null);                  // Ištriname seną atmetimo priežastį

            // Išsaugome duomenų bazėje
            $em->persist($existing); // Pažymime objektą išsaugojimui
            $em->flush();            // Vykdome SQL INSERT/UPDATE

            // Pranešimas apie sėkmingą pateikimą
            $this->addFlash('success', sprintf('Misija „%s" pateikta! Administratorius peržiūrės jūsų įrodymą.', $mission->getTitle()));
            return $this->redirectToRoute('app_missions'); // Nukreipiame į misijų sąrašą
        }

        // GET užklausa – rodome įrodymo pateikimo formą
        return $this->render('mission/submit_proof.html.twig', [
            'mission' => $mission, // Perduodame misiją šablonui
        ]);
    }
}
