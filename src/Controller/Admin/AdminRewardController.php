<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame Reward esybę – prizo objektas
use App\Entity\Reward;
// Importuojame RewardFormType – prizo formos tipą
use App\Form\RewardFormType;
// Importuojame RewardRepository – prizų saugyklą
use App\Repository\RewardRepository;
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

// Administravimo prizų valdymo kontroleris. Visi URL prasideda /admin/prizai
#[Route('/admin/prizai')]
class AdminRewardController extends AbstractController
{
    // Prizų sąrašas admin puslapyje: /admin/prizai
    #[Route('', name: 'admin_rewards')]
    public function index(RewardRepository $rewardRepository): Response
    {
        return $this->render('admin/reward/index.html.twig', [
            'rewards' => $rewardRepository->findAll(), // Visi prizai lentelei
        ]);
    }

    // Naujo prizo kūrimo puslapis: /admin/prizai/naujas
    #[Route('/naujas', name: 'admin_reward_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Sukuriame naują tuščią prizo objektą
        $reward = new Reward();
        $form = $this->createForm(RewardFormType::class, $reward);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reward);
            $em->flush();
            $this->addFlash('success', 'Prizas sukurtas!');
            return $this->redirectToRoute('admin_rewards');
        }

        return $this->render('admin/reward/form.html.twig', [
            'form' => $form,
            'title' => 'Naujas prizas',
        ]);
    }

    // Prizo redagavimo puslapis: /admin/prizai/redaguoti/{id}
    #[Route('/redaguoti/{id}', name: 'admin_reward_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, RewardRepository $rewardRepository, EntityManagerInterface $em): Response
    {
        $reward = $rewardRepository->find($id);
        if (!$reward) {
            throw $this->createNotFoundException('Prizas nerastas');
        }

        $form = $this->createForm(RewardFormType::class, $reward);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Prizas atnaujintas!');
            return $this->redirectToRoute('admin_rewards');
        }

        return $this->render('admin/reward/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti prizą',
        ]);
    }

    // Prizo trynimo veiksmas: /admin/prizai/trinti/{id}
    #[Route('/trinti/{id}', name: 'admin_reward_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, RewardRepository $rewardRepository, EntityManagerInterface $em): Response
    {
        $reward = $rewardRepository->find($id);
        if ($reward) {
            $em->remove($reward);
            $em->flush();
            $this->addFlash('success', 'Prizas ištrintas!');
        }

        return $this->redirectToRoute('admin_rewards');
    }
}
