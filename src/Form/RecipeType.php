<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => 'Nom de la recette',
                'label_attr' => [
                    'class' => 'block mb-2.5 text-sm font-medium text-heading'
                ],
                'attr' => [
                    'class' => 'bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body'
                ]
            ])
            ->add('image', FileType::class,[
                'label' => 'Image de la recette',
                'label_attr' => [
                    'class' => 'block mb-2.5 text-sm font-medium text-heading'
                ],
                'attr' => [
                    'class' => 'cursor-pointer bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full shadow-xs placeholder:text-body'
                ],
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
                'label' => 'Temps de préparation (minutes)',
                'label_attr' => [
                    'class' => 'block mb-2.5 text-sm font-medium text-heading'
                ],
                'attr' => [
                    'class' => 'bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la recette',
                'label_attr' => [
                    'class' => 'block mb-2.5 text-sm font-medium text-heading'
                ],
                'attr' => [
                    'class' => 'bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body'
                ],
            ])
            ->add('ingredients', EntityType::class, [
                'label' => 'Liste des ingrédients',
                'label_attr' => [
                    'class' => 'ms-2 text-sm font-medium text-heading select-none'
                ],
                'choice_attr' => [
                    'class' => 'w-4 h-4 rounded border-gray-300 accent-green-600'
                ],
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut de la recette',
                'required' => false,
                'choices' => [
                    'Actif' => 'Actif',
                    'En attente' => 'En attente'
                ]
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
