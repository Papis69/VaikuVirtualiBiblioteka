<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository, MissionRepository $missionRepository): Response
    {
        $latestBooks = $bookRepository->findBy([], ['createdAt' => 'DESC'], 6);
        $missions = $missionRepository->findBy([], [], 4);

        return $this->render('home/index.html.twig', [
            'latestBooks' => $latestBooks,
            'missions' => $missions,
        ]);
    }
}
