<?php

namespace App\Form;

use App\Entity\Reward;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RewardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Pavadinimas'])
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false,
            ])
            ->add('image', TextType::class, [
                'label' => 'Paveikslėlio URL',
                'required' => false,
            ])
            ->add('costInPoints', IntegerType::class, ['label' => 'Kaina (taškai)'])
            ->add('stock', IntegerType::class, ['label' => 'Likutis']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reward::class,
        ]);
    }
}
