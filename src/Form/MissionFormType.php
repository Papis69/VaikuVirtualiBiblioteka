<?php

namespace App\Form;

use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Pavadinimas'])
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false,
            ])
            ->add('rewardPoints', IntegerType::class, ['label' => 'Taškų skaičius'])
            ->add('type', TextType::class, [
                'label' => 'Tipas',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
