<?php
// Vardų erdvė – administravimo kontrolerių paketas
namespace App\Controller\Admin;

// Importuojame UserMissionRepository – laukiančių misijų skaičiavimui
use App\Repository\UserMissionRepository;
// Importuojame UserRepository – vartotojų sąrašo gavimui
use App\Repository\UserRepository;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Administravimo pagrindinis kontroleris. Visi URL prasideda /admin prefiksu
#[Route('/admin')]
class AdminController extends AbstractController
{
    // Administravimo skydelio puslapis: /admin
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(UserRepository $userRepository, UserMissionRepository $userMissionRepository): Response
    {
        // Grąžiname admin skydelio šabloną su vartotojų sąrašu ir laukiančių misijų skaičiumi
        return $this->render('admin/dashboard.html.twig', [
            'users' => $userRepository->findAll(),                 // Visi vartotojai (lentelei)
            'pendingCount' => $userMissionRepository->countPending(), // Kiek misijų laukia peržiūros
        ]);
    }
}
