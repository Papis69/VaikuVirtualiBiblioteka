<?php

namespace App\Controller\Admin;

use App\Entity\Mission;
use App\Form\MissionFormType;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/misijos')]
class AdminMissionController extends AbstractController
{
    #[Route('', name: 'admin_missions')]
    public function index(MissionRepository $missionRepository): Response
    {
        return $this->render('admin/mission/index.html.twig', [
            'missions' => $missionRepository->findAll(),
        ]);
    }

    #[Route('/nauja', name: 'admin_mission_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $mission = new Mission();
        $form = $this->createForm(MissionFormType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mission);
            $em->flush();
            $this->addFlash('success', 'Misija sukurta!');
            return $this->redirectToRoute('admin_missions');
        }

        return $this->render('admin/mission/form.html.twig', [
            'form' => $form,
            'title' => 'Nauja misija',
        ]);
    }

    #[Route('/redaguoti/{id}', name: 'admin_mission_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, MissionRepository $missionRepository, EntityManagerInterface $em): Response
    {
        $mission = $missionRepository->find($id);
        if (!$mission) {
            throw $this->createNotFoundException('Misija nerasta');
        }

        $form = $this->createForm(MissionFormType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Misija atnaujinta!');
            return $this->redirectToRoute('admin_missions');
        }

        return $this->render('admin/mission/form.html.twig', [
            'form' => $form,
            'title' => 'Redaguoti misiją',
        ]);
    }

    #[Route('/trinti/{id}', name: 'admin_mission_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, MissionRepository $missionRepository, EntityManagerInterface $em): Response
    {
        $mission = $missionRepository->find($id);
        if ($mission) {
            $em->remove($mission);
            $em->flush();
            $this->addFlash('success', 'Misija ištrinta!');
        }

        return $this->redirectToRoute('admin_missions');
    }
}
