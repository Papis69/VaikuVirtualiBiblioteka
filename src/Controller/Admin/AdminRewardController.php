<?php

namespace App\Controller\Admin;

use App\Entity\Reward;
use App\Form\RewardFormType;
use App\Repository\RewardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/prizai')]
class AdminRewardController extends AbstractController
{
    #[Route('', name: 'admin_rewards')]
    public function index(RewardRepository $rewardRepository): Response
    {
        return $this->render('admin/reward/index.html.twig', [
            'rewards' => $rewardRepository->findAll(),
        ]);
    }

    #[Route('/naujas', name: 'admin_reward_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
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
