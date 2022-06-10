<?php

namespace App\Form;

use App\Entity\Results;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ResultsType extends AbstractType
{
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
                        'message' => 'Format should be xx:xx:xx'
                    ]),

                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Results::class,
        ]);
    }
}
