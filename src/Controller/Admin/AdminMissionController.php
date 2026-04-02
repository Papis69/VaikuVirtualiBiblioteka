<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame Mission esybę – misijos objektas
use App\Entity\Mission;
// Importuojame MissionFormType – misijos formos tipą
use App\Form\MissionFormType;
// Importuojame MissionRepository – misijų saugyklą
use App\Repository\MissionRepository;
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

// Administravimo misijų valdymo kontroleris. Visi URL prasideda /admin/misijos
#[Route('/admin/misijos')]
class AdminMissionController extends AbstractController
{
    // Misijų sąrašas admin puslapyje: /admin/misijos
    #[Route('', name: 'admin_missions')]
    public function index(MissionRepository $missionRepository): Response
    {
        // Grąžiname šabloną su visomis misijomis
        return $this->render('admin/mission/index.html.twig', [
            'missions' => $missionRepository->findAll(), // Visos misijos lentelei
        ]);
    }

    // Naujos misijos kūrimo puslapis: /admin/misijos/nauja
    #[Route('/nauja', name: 'admin_mission_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Sukuriame naują tuščią misijos objektą
        $mission = new Mission();
        // Sukuriame formą, susietą su misijos objektu
        $form = $this->createForm(MissionFormType::class, $mission);
        $form->handleRequest($request); // Apdorojame užklausos duomenis

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mission); // Pažymime misiją išsaugojimui
            $em->flush();           // Vykdome SQL INSERT
            $this->addFlash('success', 'Misija sukurta!');
            return $this->redirectToRoute('admin_missions');
        }

        return $this->render('admin/mission/form.html.twig', [
            'form' => $form,
            'title' => 'Nauja misija',
        ]);
    }

    // Misijos redagavimo puslapis: /admin/misijos/redaguoti/{id}
    #[Route('/redaguoti/{id}', name: 'admin_mission_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, MissionRepository $missionRepository, EntityManagerInterface $em): Response
    {
        // Ieškome misijos pagal ID
        $mission = $missionRepository->find($id);
        if (!$mission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        // Sukuriame formą su esamos misijos duomenimis
        $form = $this->createForm(MissionFormType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Atnaujinami pakeitimai
            $this->addFlash('success', 'Misija atnaujinta!');
            return $this->redirectToRoute('admin_missions');
        }

        return $this->render('admin/mission/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti misiją',
        ]);
    }

    // Misijos trynimo veiksmas: /admin/misijos/trinti/{id}
    #[Route('/trinti/{id}', name: 'admin_mission_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, MissionRepository $missionRepository, EntityManagerInterface $em): Response
    {
        $mission = $missionRepository->find($id);
        if ($mission) {
            $em->remove($mission); // Pažymime trynimui
            $em->flush();          // Vykdome SQL DELETE
            $this->addFlash('success', 'Misija ištrinta!');
        }

        return $this->redirectToRoute('admin_missions');
    }
}
