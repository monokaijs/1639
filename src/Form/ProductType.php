<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Product Name'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'placeholder' => 'Product Description'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('price', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Price'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('available', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Available amount'
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
            ])
            ->add('preview', FileType::class, [
                'attr' => [
                    'placeholder' => 'Preview Picture'
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'form-group floating-label',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image file',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
