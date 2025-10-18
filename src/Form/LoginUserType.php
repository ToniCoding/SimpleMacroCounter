<?php

namespace src\Form;

use src\DTO\LoggedUserDTO;

use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{TextType, PasswordType};
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            -> add('username', TextType::class)
            -> add('password', PasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => LoggedUserDTO::class,
            'csrf_protection' => false,
            'csrf_field_name' => '_token'
        ]);
    }
}
