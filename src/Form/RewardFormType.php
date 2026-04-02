<?php
// Vardų erdvė – formų paketas
namespace App\Form;

// Importuojame Reward esybę
use App\Entity\Reward;
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

// Prizo formos tipas – naudojamas prizų kūrimui ir redagavimui admin puslapyje
class RewardFormType extends AbstractType
{
    // Metodas, kuris kuria formos laukus
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pavadinimo laukas
            ->add('name', TextType::class, ['label' => 'Pavadinimas'])
            // Aprašymo laukas – neprivalomas
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false,
            ])
            // Paveikslėlio URL laukas – neprivalomas
            ->add('image', TextType::class, [
                'label' => 'Paveikslėlio URL',
                'required' => false,
            ])
            // Kainos taškais laukas – privalomas sveikasis skaičius
            ->add('costInPoints', IntegerType::class, ['label' => 'Kaina (taškai)'])
            // Likučio laukas – privalomas sveikasis skaičius
            ->add('stock', IntegerType::class, ['label' => 'Likutis']);
    }

    // Numatytieji formos parametrai – susieta su Reward esybe
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reward::class, // Forma automatiškai užpildo Reward objektą
        ]);
    }
}
