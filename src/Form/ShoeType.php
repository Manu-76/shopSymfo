<?php

namespace App\Form;

use App\Entity\Shoe;
use App\Entity\Brand;
use App\Form\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ShoeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', null, [
                'label' => 'Nom du modèle'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Ajouter une description',
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Choix de l\'image du modèle',
                'required' => false
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
                'label' => 'Marque de la chaussure'
            ])
            ->add('prices', CollectionType::class, [
                'entry_type' => PriceType::class,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shoe::class,
        ]);
    }
}
