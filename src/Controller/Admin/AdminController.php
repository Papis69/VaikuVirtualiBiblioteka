<?php

namespace App\Controller\Admin;

use App\Repository\UserMissionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(UserRepository $userRepository, UserMissionRepository $userMissionRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
            'pendingCount' => $userMissionRepository->countPending(),
        ]);
    }
}
