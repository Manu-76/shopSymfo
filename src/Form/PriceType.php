<?php

namespace App\Form;

use App\Entity\Shoe;
use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Fixer un prix'
            ])
            ->add('size', ChoiceType::class, [
                'label' => 'Tailles disponibles',
                'expanded' => true,
                'multiple' => true,
                'choices' => array_combine(range(27, 57), range(27, 57)),
                'attr' => [
                    'class' => 'd-flex flex-wrap gap-3'
                ]
            ])
            // ->add('shoe', EntityType::class, [
            //     'class' => Shoe::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Price::class,
        ]);
    }
}
