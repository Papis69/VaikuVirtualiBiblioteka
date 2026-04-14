<?php
// Vardų erdvė – forma ženkliukų kūrimui/redagavimui
namespace App\Form;

// Importuojame Badge esybę
use App\Entity\Badge;
// Importuojame Symfony Form komponentus
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Ženkliuko formos tipas – apibrėžia laukus ženkliuko kūrimui/redagavimui
class BadgeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pavadinimo laukas
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas',
                'attr' => ['placeholder' => 'Pvz., Skaitytojas'],
            ])
            // Aprašymo laukas
            ->add('description', TextType::class, [
                'label' => 'Aprašymas',
                'required' => false,
                'attr' => ['placeholder' => 'Pvz., Surinkote 50 taškų!'],
            ])
            // Ikonos laukas (emoji)
            ->add('icon', TextType::class, [
                'label' => 'Ikona (emoji)',
                'required' => false,
                'attr' => ['placeholder' => 'Pvz., ⭐, 🏆, 📖'],
            ])
            // Reikalingas taškų skaičius
            ->add('requiredPoints', IntegerType::class, [
                'label' => 'Reikalingi taškai',
                'attr' => ['min' => 0],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Badge::class, // Susieta su Badge esybe
        ]);
    }
}
