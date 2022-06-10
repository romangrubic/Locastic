<?php

/**
 * This file contains Results form type
 */

namespace App\Form;

use App\Entity\Results;
use Symfony\Component\Form\{AbstractType,
    FormBuilderInterface};
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{Length,
    Regex};

/**
 * Results form type class
 */
class ResultsType extends AbstractType
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
            ->add('fullName', TextType::class, [
                'label' => 'Full name',
                'attr' => [
                    'placeholder' => 'John Doe',
                    'class' => 'input-group mb-3 text-center border border-4 rounded',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_ ]*$/',
                        'message' => 'Alphanumeric characters and spaces only.'
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 255
                    ])
                ]
            ])
            ->add('raceTime', TextType::class, [
                'required' => true,
                'label' => 'Race Time',
                'attr' => [
                    'placeholder' => 'xx:xx:xx',
                    'class' => 'input-group mb-3 text-center border border-4 rounded',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/[0-9][0-9][:][0-5][0-9][:][0-5][0-9]/',
                        'message' => 'Format: xx:xx:xx'
                    ]),

                ]
            ]);
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
            'data_class' => Results::class,
        ]);
    }
}
