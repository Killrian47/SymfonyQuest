<?php

namespace App\Form;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
            ->add('country', TextType::class, [
                'label' => 'Région où la série a été produite',
                'attr' => [
                    'placeholder' => 'Entrer la région de la série'
                ]
            ])
            ->add('year', NumberType::class, [
                'label' => 'Année de production',
                'attr' => [
                    'placeholder' => 'Entrer l\'année de production de la série'
                ]
            ])
            ->add('category', null, [
                'label' => 'Category Name ',
                'attr' => [
                    'select' => 'Choisir une catégorie'
                ],
                'choice_label' => 'name',
                'choice_attr' => [
                    'placeholder' => '--Select a name--'
                ]
            ])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'd-flex justify-content-between',
                ],
                'by_reference' => false
            ])
            ->add('posterFile', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => true, // not mandatory, default is true
                'download_uri' => true, // not mandatory, default is true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter une série',
                'attr' => [
                    'class' => 'ws-btn-submit btn btn-dark mt-3'
                ],
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
