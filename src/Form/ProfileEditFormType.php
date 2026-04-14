<?php
// Vardų erdvė – forma profilio redagavimui
namespace App\Form;

// Importuojame User esybę
use App\Entity\User;
// Importuojame Symfony Form komponentus
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

// Profilio redagavimo formos tipas
class ProfileEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Vartotojo vardo laukas
            ->add('username', TextType::class, [
                'label' => 'Vartotojo vardas',
                'attr' => ['placeholder' => 'Jūsų vardas'],
            ])
            // El. pašto laukas
            ->add('email', EmailType::class, [
                'label' => 'El. paštas',
                'attr' => ['placeholder' => 'jusu@pastas.lt'],
            ])
            // Naujas slaptažodis (neprivalomas – tik jei nori pakeisti)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // Nesusietas su esybe tiesiogiai
                'required' => false,
                'first_options' => [
                    'label' => 'Naujas slaptažodis',
                    'attr' => ['placeholder' => 'Palikite tuščią, jei nenorite keisti'],
                ],
                'second_options' => [
                    'label' => 'Pakartokite slaptažodį',
                    'attr' => ['placeholder' => 'Pakartokite naują slaptažodį'],
                ],
                'invalid_message' => 'Slaptažodžiai turi sutapti.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Slaptažodis turi būti bent {{ limit }} simbolių.',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
