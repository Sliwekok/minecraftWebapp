<?php

namespace App\Form;

use App\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\UniqueNameInterface\ConfigInterface;

class ConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add(ConfigInterface::ENTITY_PORT, null, [
                'label'     => 'Port',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                    'readonly'      => true
                ]
            ])
            ->add(ConfigInterface::ENTITY_DIFFICULTY, ChoiceType::class, [
                'label'     => 'Difficulty',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true
                ],
               'choices'   => [
                    ConfigInterface::DIFFICULTY_PEACEFUL    => ConfigInterface::DIFFICULTY_PEACEFUL,
                    ConfigInterface::DIFFICULTY_EASY        => ConfigInterface::DIFFICULTY_EASY,
                    ConfigInterface::DIFFICULTY_NORMAL      => ConfigInterface::DIFFICULTY_NORMAL,
                    ConfigInterface::DIFFICULTY_HARD        => ConfigInterface::DIFFICULTY_HARD,
                ]
            ])
            ->add(ConfigInterface::ENTITY_ALLOWFLIGHT, ChoiceType::class, [
                'label'     => 'Allow flying',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_PVP, ChoiceType::class, [
                'label'     => 'Allow PvP',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_HARDCORE, ChoiceType::class, [
                'label'     => 'Is hardcore',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_MAXPLAYERS, null, [
                'label'     => 'Max players',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                    'readonly'      => true
                ]
            ])
            ->add(ConfigInterface::ENTITY_WHITELIST, ChoiceType::class, [
                'label'     => 'Use white list',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_ONLINEMODE, ChoiceType::class, [
                'label'     => 'Enable online mode',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_GENERATESTRUCTURES, ChoiceType::class, [
                'label'     => 'Generate structures',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                ],
                'choices'   => [
                    'No'        => false,
                    'Yes'       => true,
                ]
            ])
            ->add(ConfigInterface::ENTITY_SEED, TextType::class, [
                'label'     => 'World seed',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                    'readonly'      => true
                ]
            ])
            ->add(ConfigInterface::ENTITY_LEVELNAME, TextType::class, [
                'label'     => 'Level name',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true,
                    'readonly'      => true
                ]
            ])
            ->add(ConfigInterface::ENTITY_MOTD, TextType::class, [
                'label'     => 'Welcome message',
                'attr'      => [
                    'required'      => true,
                    'class'         => 'form-control col-12',
                    'inherit_data'  => true
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update Config',
                'attr'  => [
                    'required'  => true,
                    'class'     => 'form-control btn btn-outline-primary col-12 offset-6',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => Config::class,
            'csrf_protection'   => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name'   => 'csrf_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'     => 'login_form',
        ]);
    }
}
