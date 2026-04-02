<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame User esybę – naujo vartotojo sukūrimui
use App\Entity\User;
// Importuojame RegistrationFormType – registracijos formos tipą
use App\Form\RegistrationFormType;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame UserPasswordHasherInterface – slaptažodžio šifravimo sąsaja
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Registracijos kontroleris – naujų vartotojų registracija
class RegistrationController extends AbstractController
{
    // Registracijos puslapis: /registracija
    #[Route('/registracija', name: 'app_register')]
    public function register(
        Request $request,                                 // HTTP užklausa
        UserPasswordHasherInterface $userPasswordHasher,  // Slaptažodžio šifravimo servisas
        EntityManagerInterface $entityManager,             // DB valdytojas
    ): Response {
        // Jei vartotojas jau prisijungęs – nukreipiame į pradžią (negalima registruotis du kartus)
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Sukuriame naują tuščią vartotojo objektą
        $user = new User();
        // Sukuriame registracijos formą, susietą su vartotojo objektu
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Apdorojame užklausos duomenis (užpildome formą iš POST duomenų)
        $form->handleRequest($request);

        // Tikriname, ar forma buvo pateikta ir ar duomenys validūs
        if ($form->isSubmitted() && $form->isValid()) {
            // Šifruojame slaptažodį prieš išsaugojimą (bcrypt algoritmu)
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() // Gauname nešifruotą slaptažodį iš formos
                )
            );

            // Išsaugome naują vartotoją duomenų bazėje
            $entityManager->persist($user); // Pažymime objektą išsaugojimui
            $entityManager->flush();        // Vykdome SQL INSERT

            // Rodome sėkmės pranešimą
            $this->addFlash('success', 'Registracija sėkminga! Galite prisijungti.');

            // Nukreipiame vartotoją į prisijungimo puslapį
            return $this->redirectToRoute('app_login');
        }

        // Rodome registracijos formą (GET užklausa arba validacijos klaidos)
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form, // Perduodame formą šablonui
        ]);
    }
}
