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
                'empty_data' => '',
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 255
                    ]),
                ],

            ])
            ->add('raceTime', TextType::class, [
                'required' => true,
                'label' => 'Race Time',
                'attr' => [
                    'placeholder' => 'xx:xx:xx',
                    'class' => 'input-group mb-3 text-center border border-4 rounded',
                ],
                'empty_data' => '',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{1,2}([:][0-5][0-9]){2}$/',
                        'message' => 'Wrong format. Format: (0)2:51:26'
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
