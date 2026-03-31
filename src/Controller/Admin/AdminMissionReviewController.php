<?php

namespace App\Controller\Admin;

use App\Entity\UserMission;
use App\Repository\UserMissionRepository;
use App\Service\BadgeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/perziura')]
class AdminMissionReviewController extends AbstractController
{
    #[Route('', name: 'admin_mission_review')]
    public function index(UserMissionRepository $userMissionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pendingMissions = $userMissionRepository->findPending();

        return $this->render('admin/mission_review/index.html.twig', [
            'pendingMissions' => $pendingMissions,
        ]);
    }

    #[Route('/patvirtinti/{id}', name: 'admin_mission_approve', requirements: ['id' => '\d+'])]
    public function approve(
        int $id,
        UserMissionRepository $userMissionRepository,
        EntityManagerInterface $em,
        BadgeService $badgeService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userMission = $userMissionRepository->find($id);
        if (!$userMission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        if (!$userMission->isPending()) {
            $this->addFlash('warning', 'Ši misija jau buvo peržiūrėta.');
            return $this->redirectToRoute('admin_mission_review');
        }

        // Patvirtinti misiją
        $userMission->setStatus(UserMission::STATUS_APPROVED);
        $userMission->setIsCompleted(true);
        $userMission->setReviewedAt(new \DateTimeImmutable());

        // Suteikti taškus
        $user = $userMission->getUser();
        $mission = $userMission->getMission();
        $user->addPoints($mission->getRewardPoints());

        $em->persist($userMission);
        $em->persist($user);
        $em->flush();

        // Patikrinti ženkliukus
        $newBadges = $badgeService->checkAndAwardBadges($user);

        if (count($newBadges) > 0) {
            $names = array_map(fn($b) => $b->getName(), $newBadges);
            $this->addFlash('success', sprintf(
                'Naudotojas „%s" gavo naujų ženkliukų: %s!',
                $user->getUsername(),
                implode(', ', $names)
            ));
        }

        $this->addFlash('success', sprintf(
            'Misija „%s" patvirtinta! Naudotojas „%s" gavo %d taškų.',
            $mission->getTitle(),
            $user->getUsername(),
            $mission->getRewardPoints()
        ));

        return $this->redirectToRoute('admin_mission_review');
    }

    #[Route('/atmesti/{id}', name: 'admin_mission_reject', requirements: ['id' => '\d+'])]
    public function reject(
        int $id,
        Request $request,
        UserMissionRepository $userMissionRepository,
        EntityManagerInterface $em,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userMission = $userMissionRepository->find($id);
        if (!$userMission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        if (!$userMission->isPending()) {
            $this->addFlash('warning', 'Ši misija jau buvo peržiūrėta.');
            return $this->redirectToRoute('admin_mission_review');
        }

        $reason = trim($request->request->get('rejection_reason', ''));

        $userMission->setStatus(UserMission::STATUS_REJECTED);
        $userMission->setIsCompleted(false);
        $userMission->setReviewedAt(new \DateTimeImmutable());
        $userMission->setRejectionReason($reason ?: null);

        $em->persist($userMission);
        $em->flush();

        $this->addFlash('success', sprintf(
            'Misija „%s" naudotojui „%s" atmesta.',
            $userMission->getMission()->getTitle(),
            $userMission->getUser()->getUsername()
        ));

        return $this->redirectToRoute('admin_mission_review');
    }
}
