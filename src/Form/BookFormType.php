<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Pavadinimas'])
            ->add('author', TextType::class, ['label' => 'Autorius'])
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Kategorija',
                'required' => false,
                'placeholder' => '-- Pasirinkite --',
            ])
            ->add('minAge', IntegerType::class, [
                'label' => 'Amžius nuo',
                'required' => false,
            ])
            ->add('maxAge', IntegerType::class, [
                'label' => 'Amžius iki',
                'required' => false,
            ])
            ->add('coverImage', TextType::class, [
                'label' => 'Viršelio nuotraukos URL',
                'required' => false,
            ])
            ->add('contentUrl', UrlType::class, [
                'label' => 'Turinio nuoroda (PDF / Audio)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
