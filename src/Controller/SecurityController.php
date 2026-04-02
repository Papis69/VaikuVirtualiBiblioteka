<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;
// Importuojame AuthenticationUtils – pagalbinė klasė autentifikacijos klaidų ir paskutinio vartotojo vardo gavimui
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// Saugumo kontroleris – prisijungimas ir atsijungimas
class SecurityController extends AbstractController
{
    // Prisijungimo puslapis: /prisijungimas
    #[Route('/prisijungimas', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Jei vartotojas jau prisijungęs – nukreipiame į pradžią
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Gauname paskutinę autentifikacijos klaidą (jei buvo neteisingas slaptažodis)
        $error = $authenticationUtils->getLastAuthenticationError();
        // Gauname paskutinį įvestą el. paštą (kad nereikėtų vesti iš naujo)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Grąžiname prisijungimo formą su klaidomis ir paskutiniu el. paštu
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Paskutinis įvestas el. paštas
            'error' => $error,               // Klaidos pranešimas (jei buvo)
        ]);
    }

    // Atsijungimo maršrutas: /atsijungimas (Symfony perima automatiškai per firewall)
    #[Route('/atsijungimas', name: 'app_logout')]
    public function logout(): void
    {
        // Šis metodas gali būti tuščias – Symfony firewall automatiškai apdoroja atsijungimą
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
