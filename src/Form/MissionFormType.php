<?php
// Vardų erdvė – formų paketas
namespace App\Form;

// Importuojame Mission esybę
use App\Entity\Mission;
// Importuojame AbstractType – bazinė formos tipo klasė
use Symfony\Component\Form\AbstractType;
// Importuojame formos laukų tipus
use Symfony\Component\Form\Extension\Core\Type\IntegerType;   // Sveikojo skaičiaus laukas
use Symfony\Component\Form\Extension\Core\Type\TextareaType;  // Daugiaeilis teksto laukas
use Symfony\Component\Form\Extension\Core\Type\TextType;      // Teksto laukas
// Importuojame FormBuilderInterface – formos kūrimo sąsaja
use Symfony\Component\Form\FormBuilderInterface;
// Importuojame OptionsResolver – numatytųjų parametrų konfigūravimas
use Symfony\Component\OptionsResolver\OptionsResolver;

// Misijos formos tipas – naudojamas misijų kūrimui ir redagavimui admin puslapyje
class MissionFormType extends AbstractType
{
    // Metodas, kuris kuria formos laukus
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pavadinimo laukas
            ->add('title', TextType::class, ['label' => 'Pavadinimas'])
            // Aprašymo laukas – neprivalomas
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false,
            ])
            // Taškų atlygio laukas – privalomas sveikasis skaičius
            ->add('rewardPoints', IntegerType::class, ['label' => 'Taškų skaičius'])
            // Misijos tipo laukas – neprivalomas
            ->add('type', TextType::class, [
                'label' => 'Tipas',
                'required' => false,
            ]);
    }

    // Numatytieji formos parametrai – susieta su Mission esybe
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class, // Forma automatiškai užpildo Mission objektą
        ]);
    }
}
