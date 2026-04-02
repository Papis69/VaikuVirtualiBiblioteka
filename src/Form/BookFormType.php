<?php
// Vardų erdvė – formų paketas
namespace App\Form;

// Importuojame Book esybę – su šia esybe forma bus susijusi
use App\Entity\Book;
// Importuojame Category esybę – kategorijų select laukui
use App\Entity\Category;
// Importuojame EntityType – formos laukas, kuris rodo duomenų bazės esybių sąrašą (select)
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// Importuojame AbstractType – bazinė formos tipo klasė
use Symfony\Component\Form\AbstractType;
// Importuojame formos laukų tipus
use Symfony\Component\Form\Extension\Core\Type\IntegerType;   // Sveikojo skaičiaus laukas
use Symfony\Component\Form\Extension\Core\Type\TextareaType;  // Daugiaeilis teksto laukas
use Symfony\Component\Form\Extension\Core\Type\TextType;      // Teksto laukas
use Symfony\Component\Form\Extension\Core\Type\UrlType;       // URL laukas
// Importuojame FormBuilderInterface – formos kūrimo sąsaja
use Symfony\Component\Form\FormBuilderInterface;
// Importuojame OptionsResolver – numatytųjų formos parametrų konfigūravimas
use Symfony\Component\OptionsResolver\OptionsResolver;

// Knygos formos tipas – naudojamas knygų kūrimui ir redagavimui admin puslapyje
class BookFormType extends AbstractType
{
    // Metodas, kuris kuria formos laukus
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pavadinimo laukas – privalomas teksto laukas
            ->add('title', TextType::class, ['label' => 'Pavadinimas'])
            // Autoriaus laukas – privalomas teksto laukas
            ->add('author', TextType::class, ['label' => 'Autorius'])
            // Aprašymo laukas – neprivalomas, daugiaeilis teksto laukas
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false, // Neprivalomas laukas
            ])
            // Kategorijos laukas – select su kategorijomis iš duomenų bazės
            ->add('category', EntityType::class, [
                'class' => Category::class,          // Kokia esybė naudojama
                'choice_label' => 'name',            // Kuris laukas rodomas kaip pasirinkimo tekstas
                'label' => 'Kategorija',
                'required' => false,                 // Neprivalomas
                'placeholder' => '-- Pasirinkite --', // Tuščia reikšmė select viršuje
            ])
            // Minimalus amžius – neprivalomas sveikojo skaičiaus laukas
            ->add('minAge', IntegerType::class, [
                'label' => 'Amžius nuo',
                'required' => false,
            ])
            // Maksimalus amžius – neprivalomas sveikojo skaičiaus laukas
            ->add('maxAge', IntegerType::class, [
                'label' => 'Amžius iki',
                'required' => false,
            ])
            // Viršelio nuotraukos URL – neprivalomas teksto laukas
            ->add('coverImage', TextType::class, [
                'label' => 'Viršelio nuotraukos URL',
                'required' => false,
            ])
            // Turinio nuoroda (PDF/Audio) – neprivalomas URL laukas
            ->add('contentUrl', UrlType::class, [
                'label' => 'Turinio nuoroda (PDF / Audio)',
                'required' => false,
            ]);
    }

    // Numatytieji formos parametrai – susieta su Book esybe
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class, // Forma automatiškai užpildo Book objektą
        ]);
    }
}
