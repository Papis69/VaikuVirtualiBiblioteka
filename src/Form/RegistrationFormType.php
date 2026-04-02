<?php
// Vardų erdvė – formų paketas
namespace App\Form;

// Importuojame User esybę
use App\Entity\User;
// Importuojame AbstractType – bazinė formos tipo klasė
use Symfony\Component\Form\AbstractType;
// Importuojame formos laukų tipus
use Symfony\Component\Form\Extension\Core\Type\EmailType;     // El. pašto laukas
use Symfony\Component\Form\Extension\Core\Type\PasswordType;  // Slaptažodžio laukas (paslėpti simboliai)
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;  // Pakartoto laukas (slaptažodis + patvirtinimas)
use Symfony\Component\Form\Extension\Core\Type\TextType;      // Teksto laukas
// Importuojame FormBuilderInterface – formos kūrimo sąsaja
use Symfony\Component\Form\FormBuilderInterface;
// Importuojame OptionsResolver – numatytųjų parametrų konfigūravimas
use Symfony\Component\OptionsResolver\OptionsResolver;
// Importuojame validacijos apribojimus
use Symfony\Component\Validator\Constraints\Length;   // Teksto ilgio apribojimas
use Symfony\Component\Validator\Constraints\NotBlank; // Laukas negali būti tuščias

// Registracijos formos tipas – naudojamas naujų vartotojų registracijai
class RegistrationFormType extends AbstractType
{
    // Metodas, kuris kuria formos laukus
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Vartotojo vardo laukas su validacija
            ->add('username', TextType::class, [
                'label' => 'Vartotojo vardas',
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite vartotojo vardą']),     // Negali būti tuščias
                    new Length(['min' => 3, 'max' => 100]),                      // 3–100 simbolių
                ],
            ])
            // El. pašto laukas su validacija
            ->add('email', EmailType::class, [
                'label' => 'El. paštas',
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite el. paštą']),
                ],
            ])
            // Slaptažodžio laukas – pakartojamas (reikia įvesti du kartus)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,                                   // Naudojamas PasswordType
                'mapped' => false,                                               // Nesusiejamas su User esybės lauku tiesiogiai
                'first_options' => ['label' => 'Slaptažodis'],                  // Pirmojo lauko etiketė
                'second_options' => ['label' => 'Pakartokite slaptažodį'],      // Antrojo lauko etiketė
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite slaptažodį']),          // Negali būti tuščias
                    new Length([
                        'min' => 6,                                              // Minimalus ilgis: 6 simboliai
                        'minMessage' => 'Slaptažodis turi būti bent {{ limit }} simbolių',
                        'max' => 4096,                                           // Maksimalus ilgis (saugumo ribojimas)
                    ]),
                ],
            ]);
    }

    // Numatytieji formos parametrai – susieta su User esybe
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Forma automatiškai užpildo User objektą
        ]);
    }
}
