<?php

namespace src\Form;

use src\DTO\FoodDTO;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, NumberType, ChoiceType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFoodsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class)
            ->add('market', ChoiceType::class, [
                'choices' => [
                    'Mercadona' => 'mercadona',
                    'Lidl' => 'lidl',
                    'Carrefour' => 'Carrefour',
                    'Día' => 'dia',
                    'Aldi' => 'aldi',
                    'Ahorramás' => 'ahorramas',
                    'Naturitas' => 'naturitas',
                    'Consum' => 'consum'
                ],
                'placeholder' => 'Select a market',
                'required' => true
            ])
            ->add('protein', NumberType::class)
            ->add('carbs', NumberType::class)
            ->add('fats', NumberType::class)
            ->add('fiber', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => FoodDTO::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token'
        ]);
    }
}
