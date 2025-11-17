<?php

namespace src\Form;

use src\DTO\MacroDataDTO;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReduceMacrosType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            -> add('protein', IntegerType::class)
            -> add('carbs', IntegerType::class)
            -> add('fats', IntegerType::class)
            -> add('fiber', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => MacroDataDTO::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token'
        ]);
    }
}
