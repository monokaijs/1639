<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Full Name'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => 'Full Name'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'placeholder' => 'Home Address'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'placeholder' => 'Phone Number'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'Choose Role'
                ],
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ]
            ])
        ;
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
