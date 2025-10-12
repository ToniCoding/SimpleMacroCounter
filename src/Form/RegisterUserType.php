<?php

namespace App\Form;

use App\DTO\RegisterUserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            -> add('username', TextType::class)
            -> add('password', PasswordType::class)
            -> add('email', EmailType::class)
            -> add('alias', TextType::class)
            -> add('age', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => RegisterUserDTO::class,
            'csrf_protection' => false,
            'csrf_field_name' => '_token',
        ]);
    }
}
