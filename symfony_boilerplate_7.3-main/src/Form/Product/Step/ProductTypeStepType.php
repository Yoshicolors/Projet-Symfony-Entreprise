<?php

namespace App\Form\Product\Step;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productType', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => [
                    'Produit physique' => Product::TYPE_PHYSICAL,
                    'Produit numérique' => Product::TYPE_DIGITAL,
                ],
                'expanded' => true,
                'attr' => ['class' => 'space-y-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['product_type'],
        ]);
    }
}
