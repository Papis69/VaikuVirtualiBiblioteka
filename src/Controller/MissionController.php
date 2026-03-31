<?php

namespace App\Controller;

use App\Entity\UserMission;
use App\Repository\MissionRepository;
use App\Repository\UserMissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/misijos')]
class MissionController extends AbstractController
{
    #[Route('', name: 'app_missions')]
    public function index(MissionRepository $missionRepository, UserMissionRepository $userMissionRepository): Response
    {
        $missions = $missionRepository->findAll();
        $completedMissionIds = [];
        $pendingMissionIds = [];
        $rejectedMissionIds = [];
        $rejectedReasons = [];

        if ($this->getUser()) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $userMissions = $userMissionRepository->findByUser($user->getId());
            foreach ($userMissions as $um) {
                if ($um->isApproved()) {
                    $completedMissionIds[] = $um->getMission()->getId();
                } elseif ($um->isPending()) {
                    $pendingMissionIds[] = $um->getMission()->getId();
                } elseif ($um->isRejected()) {
                    $rejectedMissionIds[] = $um->getMission()->getId();
                    $rejectedReasons[$um->getMission()->getId()] = $um->getRejectionReason();
                }
            }
        }

        return $this->render('mission/index.html.twig', [
            'missions' => $missions,
            'completedMissionIds' => $completedMissionIds,
            'pendingMissionIds' => $pendingMissionIds,
            'rejectedMissionIds' => $rejectedMissionIds,
            'rejectedReasons' => $rejectedReasons,
        ]);
    }

    #[Route('/atlikti/{id}', name: 'app_mission_submit_proof', requirements: ['id' => '\d+'])]
    public function submitProof(
        int $id,
        Request $request,
        MissionRepository $missionRepository,
        UserMissionRepository $userMissionRepository,
        EntityManagerInterface $em,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $mission = $missionRepository->find($id);
        if (!$mission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Patikrinti, ar jau pateikta arba patvirtinta
        $existing = $userMissionRepository->findOneBy([
            'user' => $user,
            'mission' => $mission,
        ]);

        if ($existing && $existing->isApproved()) {
            $this->addFlash('warning', 'Ši misija jau patvirtinta!');
            return $this->redirectToRoute('app_missions');
        }

        if ($existing && $existing->isPending()) {
            $this->addFlash('warning', 'Ši misija jau pateikta ir laukia patvirtinimo!');
            return $this->redirectToRoute('app_missions');
        }

        // Apdoroti formą
        if ($request->isMethod('POST')) {
            $proofText = trim($request->request->get('proof_text', ''));

            if (empty($proofText)) {
                $this->addFlash('danger', 'Prašome parašyti įrodymą, kad atlikote misiją.');
                return $this->render('mission/submit_proof.html.twig', [
                    'mission' => $mission,
                ]);
            }

            if (!$existing) {
                $existing = new UserMission();
                $existing->setUser($user);
                $existing->setMission($mission);
            }

            $existing->setProofText($proofText);
            $existing->setStatus(UserMission::STATUS_PENDING);
            $existing->setIsCompleted(false);
            $existing->setCompletedAt(new \DateTimeImmutable());
            $existing->setRejectionReason(null);

            $em->persist($existing);
            $em->flush();

            $this->addFlash('success', sprintf('Misija „%s" pateikta! Administratorius peržiūrės jūsų įrodymą.', $mission->getTitle()));
            return $this->redirectToRoute('app_missions');
        }

        return $this->render('mission/submit_proof.html.twig', [
            'mission' => $mission,
        ]);
    }
}
