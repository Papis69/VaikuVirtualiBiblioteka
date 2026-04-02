<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Profilio kontroleris – vartotojo asmeninio profilio puslapis
class ProfileController extends AbstractController
{
    // Profilio puslapis: /profilis
    #[Route('/profilis', name: 'app_profile')]
    public function index(): Response
    {
        // Reikalaujame, kad vartotojas būtų prisijungęs
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Gauname prisijungusį vartotoją
        $user = $this->getUser();

        // Grąžiname profilio šabloną su vartotojo duomenimis
        return $this->render('profile/index.html.twig', [
            'user' => $user, // Perduodame vartotojo objektą šablonui
        ]);
    }
}
