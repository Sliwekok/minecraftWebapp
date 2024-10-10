<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use App\UniqueNameInterface\ModsInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class ModsLoadCustomModsFormType extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(ModsInterface::FORM_FILES, FileType::class, [
                'label'         => 'Custom Mods',
                'attr'          => [
                        'required'  => 'true',
                        'class'     => 'form-control',
                        'type'          => 'jar',

                ],
                'multiple'      => true,
                'constraints'   => [
                    new All([
                        'constraints'   => [
                            new File([
                                 'maxSize'           => '500M',
                                 'maxSizeMessage'    => 'Files must not be bigger than 500 MB',
                                 'mimeTypes'         => [
                                     'application/java-archive',
                                     'application/x-java-archive',
                                     'application/x-jar',
                                 ],
                                 'mimeTypesMessage'  => 'Upload valid JAR file',
                            ])
                        ]
                    ])
                ],

            ])
            ->add('submit', SubmitType::class, [
                'attr'      => [
                    'class'     => 'btn btn-lg btn-primary',
                ],
                'disabled'  => true,
                'label'     => 'Upload'
            ])
            ->setAction($this->router->generate('mods_index'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'            => null,
            'csrf_protection'       => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name'       => 'csrf_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'         => 'login_form',
        ]);
    }
}
