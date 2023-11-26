<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CreateNewServerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // here's grouping for form - we create 'wizard' type installer with panels
        $builder
            // group 1 - server name and server version
            ->add(
                $builder->create('step_1', FormType::class, array('inherit_data' => true))
                    ->add('name', TextType::class, [
                        'label'         => 'Server name',
                        'attr'          => [
                            'required'      => true,
                            'class'         => 'form-control',
                            'id'            => 'formServerName',
                            'value'         => $options['defaultServerName']
                        ],
                        'constraints'   => [
                            new Length([
                                'min'           => 6,
                                'minMessage'    => 'Your server name must be least 6 characters long',
                                'max'           => 100,
                            ])
                        ]
                    ])
                    ->add('version', TextType::class, [
                        'label'         => 'Server version',
                        'attr'          => [
                            'required'      => true,
                            'class'         => 'form-control',
                            'id'            => 'formServerVersion',
                            'list'          => 'minecraftVersionsDatalist'
                        ]
                    ])
                    ->add('seed', TextType::class, [
                        'label'     => 'World seed',
                        'required'  => false,
                        'attr'      => [
                            'class'     => 'form-control',
                        ]
                    ])
            )
            // group 2 - mods
            ->add(
                $builder->create('step_2', FormType::class, array('inherit_data' => true))
                    ->add('gameType', ChoiceType::class, [
                        'label'     => 'Select game type of your server',
                        'attr'      => [
                            'required'  => true,
                            'class'     => 'form-control',
                        ],
                        'choices'   => [
                            'Vanilla'       => 'vanilla',
                            'Forge client'  => 'forge',
                            'Custom'        => 'custom'
                        ]
                    ])
            )
            // group 3 - whitelist
            ->add(
                $builder->create('step_3', FormType::class, array('inherit_data' => true))
                    ->add('whitelist', ChoiceType::class, [
                        'label'     => 'Do you need whitelist?',
                        'attr'      => [
                            'required'  => true,
                            'class'     => 'form-control',
                        ],
                        'choices'   => [
                            'No'        => false,
                            'Yes'       => true,
                        ]
                    ])
            )
            // group 4 - agreement and ToS
            ->add(
                $builder->create('step_4', FormType::class, array('inherit_data' => true))
                    ->add('ToS', null, [
                        'disabled'      => true,
                        'label'         => 'Creating server means you agree to <a class="special" href="'. $options['urlTos'] .'">our ToS</a>',
                        'label_html'    => true,
                        'attr'          => [
                            'required'      => false
                        ]

                    ])
                    ->add('submit', SubmitType::class, [
                        'label' => 'Create Server',
                        'attr'  => [
                            'required'  => true,
                            'class'     => 'form-control btn btn-outline-primary',
                        ],
                    ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => null,
            'csrf_protection'   => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name'   => 'csrf_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'     => 'login_form',
            'urlTos'            => null,
            'defaultServerName' => null,
        ]);
    }
}
