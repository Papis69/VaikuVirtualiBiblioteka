<?php
// Vardų erdvė – kontrolerių paketas
namespace App\Controller;

// Importuojame UserReward esybę – vartotojo prizo įrašas
use App\Entity\UserReward;
// Importuojame RewardRepository – saugyklą prizų gavimui
use App\Repository\RewardRepository;
// Importuojame EntityManagerInterface – DB valdytojas
use Doctrine\ORM\EntityManagerInterface;
// Importuojame AbstractController – bazinė kontrolerio klasė
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Importuojame Response – HTTP atsakymo objektas
use Symfony\Component\HttpFoundation\Response;
// Importuojame Route – maršrutų atributas
use Symfony\Component\Routing\Attribute\Route;

// Prizų kontroleris – prisų peržiūra ir įsigijimas. Visi URL prasideda /prizai
#[Route('/prizai')]
class RewardController extends AbstractController
{
    // Prizų sąrašo puslapis: /prizai
    #[Route('', name: 'app_rewards')]
    public function index(RewardRepository $rewardRepository): Response
    {
        // Gauname visus prizus iš duomenų bazės
        $rewards = $rewardRepository->findAll();

        // Grąžiname prizų šabloną
        return $this->render('reward/index.html.twig', [
            'rewards' => $rewards, // Perduodame prizus šablonui
        ]);
    }

    // Prizo įsigijimo veiksmas: /prizai/isigyti/{id}
    #[Route('/isigyti/{id}', name: 'app_reward_claim', requirements: ['id' => '\d+'])]
    public function claim(int $id, RewardRepository $rewardRepository, EntityManagerInterface $em): Response
    {
        // Reikalaujame, kad vartotojas būtų prisijungęs
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Ieškome prizo pagal ID
        $reward = $rewardRepository->find($id);
        if (!$reward) {
            throw $this->createNotFoundException('Prizas nerastas'); // 404 klaida
        }

        // Gauname prisijungusį vartotoją
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Tikriname, ar vartotojas turi pakankamai taškų
        if ($user->getPoints() < $reward->getCostInPoints()) {
            $this->addFlash('danger', 'Neturite pakankamai taškų šiam prizui!');
            return $this->redirectToRoute('app_rewards');
        }

        // Tikriname, ar prizas dar yra sandėlyje
        if ($reward->getStock() <= 0) {
            $this->addFlash('danger', 'Šio prizo likutis baigėsi!');
            return $this->redirectToRoute('app_rewards');
        }

        // Atimame taškus nuo vartotojo
        $user->removePoints($reward->getCostInPoints());
        // Sumažiname prizo likutį per 1
        $reward->setStock($reward->getStock() - 1);

        // Sukuriame naują vartotojo prizo įrašą
        $userReward = new UserReward();
        $userReward->setUser($user);     // Nustatome vartotoją
        $userReward->setReward($reward); // Nustatome prizą

        // Išsaugome visus pakeitimus duomenų bazėje
        $em->persist($userReward); // Naujas UserReward įrašas
        $em->persist($user);       // Atnaujinti vartotojo taškai
        $em->persist($reward);     // Atnaujintas prizo likutis
        $em->flush();              // Vykdome SQL užklausas

        // Pranešimas apie sėkmingą įsigijimą
        $this->addFlash('success', sprintf('Prizas „%s" sėkmingai įsigytas!', $reward->getName()));

        // Nukreipiame atgal į prizų sąrašą
        return $this->redirectToRoute('app_rewards');
    }
}
