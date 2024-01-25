<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use App\UniqueNameInterface\BackupInterface;
use Symfony\Component\Validator\Constraints\File;

class BackupLoadUserWorld extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(BackupInterface::FORM_USERUPLOAD_FILE, FileType::class, [
                'label'         => 'Zip archive',
                'attr'          => [
                        'required'  => 'true',
                        'class'     => 'form-control',
                ],
                'constraints' => [
                    new File([
                        'maxSize'           => '500M',
                        'maxSizeMessage'    => 'File must not be bigger than 500 MB',
                        'mimeTypes'         => [
                                                'application/zip',
                                                'application/x-zip-compressed',
                        ],
                        'mimeTypesMessage'  => 'Upload valid ZIP file',
                    ])
                ],

            ])
            ->add('submit', SubmitType::class, [
                'attr'  => [
                    'class'     => 'btn btn-lg btn-primary',
                ],
                'label' => 'Upload'
            ])
            ->setAction($this->router->generate('backup_loadcustom'))
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
