<?php

namespace App\Form\Product\Step;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductLogisticsStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weight', NumberType::class, [
                'label' => 'Poids (kg)',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent'],
            ])
            ->add('dimensions', TextType::class, [
                'label' => 'Dimensions (L x l x H cm)',
                'required' => false,
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                    'placeholder' => 'Ex: 30 x 20 x 10',
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
