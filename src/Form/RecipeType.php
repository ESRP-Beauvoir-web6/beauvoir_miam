<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la recette'
            ])
            ->add('image', FileType::class,[
                'label' => 'Image de la recette',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize : '16000k',
                        maxSizeMessage : 'Taille de fichier trop grande',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/svg',
                            'image/webp'
                        ],
                        mimeTypesMessage: "Extension de fichier invalide"
                    )
                ]
            ])
            ->add('time', NumberType::class, [
                'label' => 'Temps de préparation (minutes)'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la recette'
            ])
            ->add('ingredients', EntityType::class, [
                'label' => 'Liste des ingrédients',
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
