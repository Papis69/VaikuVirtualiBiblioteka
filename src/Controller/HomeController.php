<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame BookRepository – saugyklą knygų gavimui iš duomenų bazės
use App\Repository\BookRepository;
// Importuojame MissionRepository – saugyklą misijų gavimui
use App\Repository\MissionRepository;
// Importuojame AbstractController – bazinė Symfony kontrolerio klasė su pagalbiniais metodais
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route atributą – maršrutų (URL) apibrėžimui
use Symfony\Component\Routing\Attribute\Route;

// Pradinio puslapio kontroleris
class HomeController extends AbstractController
{
    // Maršrutas: '/' (pagrindinis puslapis), vardas: 'app_home'
    #[Route('/', name: 'app_home')]
    // Pagrindinio puslapio veiksmas (akcija)
    public function index(BookRepository $bookRepository, MissionRepository $missionRepository): Response
    {
        // Gauname 6 naujausias knygas, surūšiuotas pagal sukūrimo datą (nuo naujausios)
        $latestBooks = $bookRepository->findBy([], ['createdAt' => 'DESC'], 6);
        // Gauname 4 misijas rodymui pagrindiniame puslapyje
        $missions = $missionRepository->findBy([], [], 4);

        // Grąžiname Twig šabloną su duomenimis
        return $this->render('home/index.html.twig', [
            'latestBooks' => $latestBooks, // Perduodame naujausias knygas šablonui
            'missions' => $missions,       // Perduodame misijas šablonui
        ]);
    }
}
