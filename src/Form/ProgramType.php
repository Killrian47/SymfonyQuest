<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du programme ',
                'attr' => [
                    'placeholder' => 'Titre du programme'
                ]
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => 'Synopsis du programme ',
                'attr' => [
                    'placeholder' => 'Renseigner le synopsis du programme'
                ],
            ])
            ->add('poster', TextType::class, [
                'label' => 'Image du programme ',
                'attr' => [
                    'placeholder' => 'Renseigner l\'image correspondant a votre programme'
                ]
            ])
            ->add('country')
            ->add('year')
            ->add('category', null, [
                'label' => 'Category Name ',
                'attr' => [
                    'placeholder' => 'Choisir une catégorie'
                ],
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
