<?php

namespace App\Controller;

use App\Entity\UserReward;
use App\Repository\RewardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prizai')]
class RewardController extends AbstractController
{
    #[Route('', name: 'app_rewards')]
    public function index(RewardRepository $rewardRepository): Response
    {
        $rewards = $rewardRepository->findAll();

        return $this->render('reward/index.html.twig', [
            'rewards' => $rewards,
        ]);
    }

    #[Route('/isigyti/{id}', name: 'app_reward_claim', requirements: ['id' => '\d+'])]
    public function claim(int $id, RewardRepository $rewardRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $reward = $rewardRepository->find($id);
        if (!$reward) {
            throw $this->createNotFoundException('Prizas nerastas');
        }

        $user = $this->getUser();

        if ($user->getPoints() < $reward->getCostInPoints()) {
            $this->addFlash('danger', 'Neturite pakankamai taškų šiam prizui!');
            return $this->redirectToRoute('app_rewards');
        }

        if ($reward->getStock() <= 0) {
            $this->addFlash('danger', 'Šio prizo likutis baigėsi!');
            return $this->redirectToRoute('app_rewards');
        }

        $user->removePoints($reward->getCostInPoints());
        $reward->setStock($reward->getStock() - 1);

        $userReward = new UserReward();
        $userReward->setUser($user);
        $userReward->setReward($reward);

        $em->persist($userReward);
        $em->persist($user);
        $em->persist($reward);
        $em->flush();

        $this->addFlash('success', sprintf('Prizas „%s" sėkmingai įsigytas!', $reward->getName()));

        return $this->redirectToRoute('app_rewards');
    }
}
