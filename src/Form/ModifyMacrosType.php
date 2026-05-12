<?php

namespace src\Form;

use src\DTO\MacroDataDTO;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyMacrosType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('protein', NumberType::class, [
                'attr' => ['min' => 0]
            ])
            ->add('carbs', NumberType::class, [
                'attr' => ['min' => 0]
            ])
            ->add('fats', NumberType::class, [
                'attr' => ['min' => 0]
            ])
            ->add('fiber', NumberType::class, [
                'attr' => ['min' => 0]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => MacroDataDTO::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token'
        ]);
    }
}
