<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame UserRepository – saugyklą vartotojų gavimui iš duomenų bazės
use App\Repository\UserRepository;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Lyderių lentelės kontroleris – rodo geriausius skaitytojus pagal taškus
class LeaderboardController extends AbstractController
{
    // Lyderių lentelės puslapis: /lyderiai
    #[Route('/lyderiai', name: 'app_leaderboard')]
    public function index(UserRepository $userRepository): Response
    {
        // Gauname top 20 vartotojų, surūšiuotų pagal taškus (nuo daugiausiai)
        $topUsers = $userRepository->findBy([], ['points' => 'DESC'], 20);

        // Grąžiname lyderių lentelės šabloną
        return $this->render('leaderboard/index.html.twig', [
            'topUsers' => $topUsers, // Perduodame geriausius vartotojus šablonui
        ]);
    }
}
