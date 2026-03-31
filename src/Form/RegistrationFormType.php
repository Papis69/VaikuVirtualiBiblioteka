<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Vartotojo vardas',
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite vartotojo vardą']),
                    new Length(['min' => 3, 'max' => 100]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'El. paštas',
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite el. paštą']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Slaptažodis'],
                'second_options' => ['label' => 'Pakartokite slaptažodį'],
                'constraints' => [
                    new NotBlank(['message' => 'Įveskite slaptažodį']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Slaptažodis turi būti bent {{ limit }} simbolių',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
