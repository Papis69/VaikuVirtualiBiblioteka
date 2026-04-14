<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Statinių puslapių kontroleris – „Apie mus", „Kontaktai", „Privatumo politika"
class StaticPageController extends AbstractController
{
    // „Apie mus" puslapis: /apie
    #[Route('/apie', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('static/about.html.twig');
    }

    // „Kontaktai" puslapis: /kontaktai
    #[Route('/kontaktai', name: 'app_contacts')]
    public function contacts(): Response
    {
        return $this->render('static/contacts.html.twig');
    }

    // „Privatumo politika" puslapis: /privatumas
    #[Route('/privatumas', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('static/privacy.html.twig');
    }
}
