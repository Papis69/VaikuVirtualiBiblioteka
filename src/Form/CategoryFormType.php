<?php
// Vardų erdvė – forma kategorijų kūrimui/redagavimui
namespace App\Form;

// Importuojame Category esybę
use App\Entity\Category;
// Importuojame Symfony Form komponentus
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Kategorijos formos tipas – apibrėžia laukus kategorijos kūrimui/redagavimui
class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pavadinimo laukas
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas',
                'attr' => ['placeholder' => 'Pvz., Pasakos'],
            ])
            // Spalvos laukas (HTML color picker)
            ->add('color', ColorType::class, [
                'label' => 'Spalva',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class, // Susieta su Category esybe
        ]);
    }
}
