<?php

namespace App\Form;

use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Cette variable $builder est le constructeur de formulaire (géré par FormBuilderInterface)
        $builder
        // Ici le controller lors de la création du formulaire ayant récupérer les propriétés de l'objet, ils sont donc ajouter comme champ de formulaire
    // ->add('name') construit de base
        // Ici on peut apporter des modifications au champ en indiquant quel type de champ c'est (un text, une date, un boolean...., et dans un [], des options 'label', 'required'...voir la doc : https://symfony.com/doc/current/reference/forms/types.html)
            ->add('name', TextType::class, [
                'label' => 'Nom de la marque à créer',
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Choix de l\'image de la marque',
                'required' => false
            ])
        ;
    }

    // On ne s'occupe pas de cette partie
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
        ]);
    }
}
