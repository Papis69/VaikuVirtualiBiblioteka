<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame ProfileEditFormType – profilio redagavimo formą
use App\Form\ProfileEditFormType;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Request – HTTP užklausos objektas
use Symfony\Component\HttpFoundation\Request;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;
// Importuojame UserPasswordHasherInterface – slaptažodžių šifravimui
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Profilio kontroleris – vartotojo asmeninio profilio puslapis ir redagavimas
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

    // Profilio redagavimo puslapis: /profilis/redaguoti
    #[Route('/profilis/redaguoti', name: 'app_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        // Reikalaujame, kad vartotojas būtų prisijungęs
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Sukuriame profilio redagavimo formą su esamais duomenimis
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Jei buvo įvestas naujas slaptažodis – šifruojame ir nustatome
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            // Išsaugome pakeitimus
            $em->flush();

            $this->addFlash('success', 'Profilis sėkmingai atnaujintas!');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
