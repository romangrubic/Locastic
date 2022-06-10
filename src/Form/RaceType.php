<?php

/**
 * This file contains Race form type
 */

namespace App\Form;

use App\Entity\Race;
use Symfony\Component\Form\{AbstractType,
    FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\{DateType,
    FileType,
    TextType};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{File,
    Length};

/**
 * Race form type class
 */
class RaceType extends AbstractType
{    
    /**
     * buildForm
     *
     * @param  FormBuilderInterface $builder
     * @param  array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raceName', TextType::class, [
                'label' => 'Race name',
                'attr' => [
                    'placeholder' => 'Marathon',
                    'class' => 'input-group mb-3 text-center border border-4 rounded',
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 255
                    ]),
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'attr' => [
                    'class' => 'mb-3 text-center',
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'label' => 'CSV file only',
                'attr' => [
                    'class' => 'input-group mb-3',
                ],
                'row_attr' => [
                    'class' => 'text-center'
                ],
                "constraints" => [
                    new File([
                        "mimeTypes" => [
                            "text/csv",
                            "text/plain"
                        ],
                    ])
                ]
            ])
        ;
    }
    
    /**
     * configureOptions
     *
     * @param  OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Race::class,
        ]);
    }
}
